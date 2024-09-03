<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Author;
use App\Http\Services\GoogleBooksService;
use App\Http\Services\BrasilAPIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;



class BookController extends Controller
{
    private $googleBooksService;
    private $brasilAPIService;
    
    public function __construct(GoogleBooksService $googleBooksService, BrasilAPIService $brasilAPIService)
    {
        $this->googleBooksService = $googleBooksService;
        $this->brasilAPIService = $brasilAPIService;
    }
    public function index(Request $request)
    {
        $page = $request->query('page', 1);
        $limit = $request->query('limit', 10);
    
        $books = Book::with('author')->whereHas('author')->paginate($limit, ['*'], 'page', $page);
    
        return view('books.index', ['books' => $books]);
    }
    
    public function create()
    {
        $authors = Author::all();
        return view('books.create', [
            'authors' => $authors,
            'book' => new Book()
        ]);
    }

public function show($id)
{
    $book = Book::with('author')->find($id);

    // Se o livro não for encontrado no banco de dados, buscar na API do GoogleBooks
    if (!$book) {
        $googleBook = $this->googleBooksService->getBookById($id);
        if (isset($googleBook['error'])) {
            return redirect()->route('books.index')->withErrors('Erro ao buscar na API do Google Books: ' . $googleBook['error']);
        }

        if (!isset($googleBook['volumeInfo'])) {
            return redirect()->route('books.index')->with('message', 'Nenhum livro encontrado na API do Google Books.');
        }

        // Padroniza a resposta da API do Google Books para o formato do modelo de livro
        $book = (object) [
            'id' => $googleBook['id'] ?? '',
            'title' => $googleBook['volumeInfo']['title'] ?? 'Título Desconhecido',
            'author' => (object) [
                'name' => isset($googleBook['volumeInfo']['authors']) ? implode(', ', $googleBook['volumeInfo']['authors']) : 'Autor Desconhecido'
            ],
            'publisher' => $googleBook['volumeInfo']['publisher'] ?? 'Editora Desconhecida',
            'publication_year' => $googleBook['volumeInfo']['publishedDate'] ?? 'Data Desconhecida',
            'cover_url' => $googleBook['volumeInfo']['imageLinks']['thumbnail'] ?? '', 
            'synopsis' => $googleBook['volumeInfo']['description'] ?? 'Sinopse não disponível',
            'isbn_number' => $this->getIsbn($googleBook['volumeInfo']['industryIdentifiers'] ?? []),
            'pages_amount' => $googleBook['volumeInfo']['pageCount'] ?? 'Número de Páginas Desconhecido'
        ];
    }

    return view('books.show', compact('book'));
}


public function searchBooks(Request $request)
{
    $query = $request->input('query', '');

    // Buscar livros localmente pelo título, ISBN ou nome do autor
    $localBooks = Book::with('author')
        ->where('title', 'like', '%' . $query . '%')
        ->orWhere('isbn_number', 'like', '%' . $query . '%') // Busca pelo ISBN
        ->orWhereHas('author', function ($queryBuilder) use ($query) { // Busca pelo nome do autor
            $queryBuilder->where('name', 'like', '%' . $query . '%');
        })
        ->get();
    
    // Buscar livros na API do Google Books
    $googleBooks = $this->googleBooksService->searchBooks($query);

    // Verificar a resposta da API do Google Books
    if (isset($googleBooks['error'])) {
        return redirect()->route('books.index')->withErrors('Erro ao buscar na API do Google Books: ' . $googleBooks['error']);
    }

    if (!isset($googleBooks['items'])) {
        // Se a API não retornar itens, exibir mensagem de livro não encontrado
        $combinedBooks = $localBooks->map(function ($book) {
            return (object) [
                'id' => $book->id, 
                'title' => $book->title,
                'author' => $book->author ? $book->author->name : 'Autor Desconhecido',
                'publisher' => $book->publisher,
                'publication_year' => $book->publication_year,
                'cover_url' => $book->cover_url ?? '', 
                'isbn' => $book->isbn_number ?? 'ISBN não disponível',
                'page_count' => $book->pages_amount ?? 'Número de Páginas Desconhecido'
            ];
        })->toArray();

        if (empty($combinedBooks)) {
            return view('books.not_found'); // Mostrar view de livro não encontrado
        }

        $perPage = 10;
        $currentPage = $request->input('page', 1);

        $paginatedBooks = collect($combinedBooks)->forPage($currentPage, $perPage);

        $books = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedBooks,
            count($combinedBooks),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('books.search', ['books' => $books]);
    }

    // Padroniza a resposta da API do Google Books para o formato do modelo de livro
    $googleBooksFormatted = array_map(function ($book) {
        return (object) [
            'id' => $book['id'] ?? '',
            'title' => $book['volumeInfo']['title'] ?? 'Título Desconhecido',
            'author' => isset($book['volumeInfo']['authors']) ? implode(', ', $book['volumeInfo']['authors']) : 'Autor Desconhecido',
            'publisher' => $book['volumeInfo']['publisher'] ?? 'Editora Desconhecida',
            'publication_year' => $book['volumeInfo']['publishedDate'] ?? 'Data Desconhecida',
            'cover_url' => $book['volumeInfo']['imageLinks']['thumbnail'] ?? '',
            'isbn' => $this->getIsbn($book['volumeInfo']['industryIdentifiers'] ?? []),
            'page_count' => $book['volumeInfo']['pageCount'] ?? 'Número de Páginas Desconhecido'
        ];
    }, $googleBooks['items']);

    // Converte livros locais para um array simples
    $localBooksFormatted = $localBooks->map(function ($book) {
        return (object) [
            'id' => $book->id, 
            'title' => $book->title,
            'author' => $book->author ? $book->author->name : 'Autor Desconhecido',
            'publisher' => $book->publisher,
            'publication_year' => $book->publication_year,
            'cover_url' => $book->cover_url ?? '', 
            'isbn' => $book->isbn_number ?? 'ISBN não disponível',
            'page_count' => $book->pages_amount ?? 'Número de Páginas Desconhecido'
        ];
    });

    // Combina os resultados locais e da API
    $combinedBooks = array_merge($localBooksFormatted->toArray(), $googleBooksFormatted);

    $perPage = 10;
    $currentPage = $request->input('page', 1);

    $paginatedBooks = collect($combinedBooks)->forPage($currentPage, $perPage);

    $books = new \Illuminate\Pagination\LengthAwarePaginator(
        $paginatedBooks,
        count($combinedBooks),
        $perPage,
        $currentPage,
        ['path' => $request->url(), 'query' => $request->query()]
    );

    return view('books.search', ['books' => $books]);
}


    
    private function getIsbn($identifiers)
    {
        foreach ($identifiers as $identifier) {
            if ($identifier['type'] === 'ISBN_13') {
                return $identifier['identifier'];
            } elseif ($identifier['type'] === 'ISBN_10') {
                return $identifier['identifier'];
            }
        }
        return 'ISBN não disponível';
    }
    

    public function showByIsbn($isbn)
    {
        // Verifica se o livro já existe na base de dados local
        $book = Book::where('isbn_number', $isbn)->first();

        if ($book) {
            return view('books.show', compact('book'));
        }

        // Se não existir localmente, busca na API do Google Books
        $googleBookData = $this->googleBooksService->searchByIsbn($isbn);

        if (!$googleBookData) {
            return view('books.not-found');
        }

        // Cria um novo livro na base de dados local
        $book = Book::create([
            'title' => $googleBookData['title'] ?? 'Título desconhecido',
            'author_id' => null, // Você pode adicionar lógica para associar autores se necessário
            'publisher' => $googleBookData['publisher'] ?? 'Editora desconhecida',
            'publication_year' => $googleBookData['publishedDate'] ?? null,
            'pages_amount' => $googleBookData['pageCount'] ?? null,
            'isbn_number' => $isbn,
            'synopsis' => $googleBookData['description'] ?? null,
            'file_url' => null,
            'cover_url' => $googleBookData['thumbnail'] ?? null,
        ]);

        return view('books.show', compact('book'));
    }
    
    public function download($id)
    {
        $book = Book::find($id);
        if (!$book || !$book->file_url) {
            return redirect()->route('books.index')->withErrors('File not found');
        }

        if (strpos($book->file_url, 's3://') === 0) {
            $fileUrl = str_replace('s3://', env('AWS_URL') . '/', $book->file_url);

            return redirect()->away($fileUrl);
        }

    // Caso o arquivo esteja no armazenamento local
    $filePath = storage_path('app/' . $book->file_url);
    if (file_exists($filePath)) {
        return response()->download($filePath);
    }

    return redirect()->route('books.index')->withErrors('File not found');
}
public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'author_id' => 'required|exists:authors,id',
        'publisher' => 'required|string|max:255',
        'publication_year' => 'required|date_format:Y',
        'pages_amount' => 'required|integer',
        'isbn_number' => 'required|string|unique:books,isbn_number',
        'file' => 'nullable|file|mimes:pdf|max:20480', 
        'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',  
        'synopsis' => 'nullable|string',
    ]);

    $book = Book::create([
        'title' => $validated['title'],
        'author_id' => $validated['author_id'],
        'publisher' => $validated['publisher'],
        'publication_year' => $validated['publication_year'],
        'pages_amount' => $validated['pages_amount'],
        'isbn_number' => $validated['isbn_number'],
        'synopsis' => $validated['synopsis'],
    ]);

    // Manipula o arquivo PDF, se existir
    if ($request->hasFile('file')) {
        $filePath = $request->file('file')->store('gerenciador-de-livros', 's3', [
            'visibility' => 'public'
        ]);

        if ($filePath) {
            $book->file_url = env('AWS_URL') . '/' . $filePath; 
        } else {
            return back()->withErrors('Erro ao salvar o arquivo PDF no S3.');
        }
    }

    // Manipula a imagem, se existir
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('gerenciador-de-livros', 's3', [
            'visibility' => 'public'
        ]);

        if ($imagePath) {
            $book->cover_url = env('AWS_URL') . '/' . $imagePath; 
        } else {
            return back()->withErrors('Erro ao salvar a imagem no S3.');
        }
    }

    
    $book->save();
    
    return redirect()->route('books.index')->with('success', 'Livro adicionado com sucesso!');
}
       
public function update(Request $request, $id)
{
    $book = Book::find($id);
    if (!$book) {
        return redirect()->route('books.index')->withErrors('Book not found');
    }

    $validated = $request->validate([
        'title' => 'sometimes|required|string|max:255',
        'author_id' => 'sometimes|required|exists:authors,id',
        'publisher' => 'sometimes|required|string|max:255',
        'publication_year' => 'sometimes|required|date_format:Y',
        'pages_amount' => 'sometimes|required|integer',
        'isbn_number' => 'sometimes|required|string|unique:books,isbn_number,' . $id,
        'synopsis' => 'nullable|string',
        'file' => 'nullable|file|mimes:pdf|max:20480',
        'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    $book->update($validated);

    // Manipula o arquivo PDF, se existir
    if ($request->hasFile('file')) {
        if ($book->file_url) {
        
            $oldFilePath = str_replace(env('AWS_URL') . '/', '', $book->file_url);
            Storage::disk('s3')->delete($oldFilePath);
        }

        $filePath = $request->file('file')->store('gerenciador-de-livros', 's3', [
            'visibility' => 'public'
        ]);

        if ($filePath) {
            $book->file_url = env('AWS_URL') . '/' . $filePath;  
        } else {
            return back()->withErrors('Erro ao salvar o arquivo PDF no S3.');
        }
    }

    if ($request->hasFile('image')) {
        if ($book->cover_url) {

            $oldImagePath = str_replace(env('AWS_URL') . '/', '', $book->cover_url);
            Storage::disk('s3')->delete($oldImagePath);
        }

        $imagePath = $request->file('image')->store('gerenciador-de-livros', 's3', [
            'visibility' => 'public'
        ]);

        if ($imagePath) {
            $book->cover_url = env('AWS_URL') . '/' . $imagePath;  
        } else {
            return back()->withErrors('Erro ao salvar a imagem no S3.');
        }
    }

    $book->save();

    return redirect()->route('books.index')->with('success', 'Livro atualizado com sucesso!');
}

    public function edit($id)
{
    $book = Book::findOrFail($id);

    $authors = Author::all();

    return view('books.edit', ['book' => $book, 'authors' => $authors]);
}


   public function destroy($id)
{
    $book = Book::findOrFail($id);
    $book->delete();

    return redirect()->route('books.index')->with('success', 'Book deleted successfully');
}
}