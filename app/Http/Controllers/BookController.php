<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Author;
use App\Http\Services\GoogleBooksService;
use App\Http\Services\BrasilAPIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
    
        // Retornar a view com os dados dos livros
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
    // Tenta encontrar o livro no banco de dados
    $book = Book::with('author')->find($id);

    // Se o livro não for encontrado no banco de dados, buscar na API do Google Books
    if (!$book) {
        $googleBook = $this->googleBooksService->getBookById($id);
        if (isset($googleBook['error'])) {
            return redirect()->route('books.index')->withErrors('Erro ao buscar na API do Google Books: ' . $googleBook['error']);
        }

        if (!isset($googleBook['volumeInfo'])) {
            return redirect()->route('books.index')->with('message', 'Nenhum livro encontrado na API do Google Books.');
        }

        // Padronizar a resposta da API do Google Books para o formato do modelo de livro
        $book = (object) [
            'id' => $googleBook['id'] ?? '',
            'title' => $googleBook['volumeInfo']['title'] ?? 'Título Desconhecido',
            'author' => (object) [
                'name' => isset($googleBook['volumeInfo']['authors']) ? implode(', ', $googleBook['volumeInfo']['authors']) : 'Autor Desconhecido'
            ],
            'publisher' => $googleBook['volumeInfo']['publisher'] ?? 'Editora Desconhecida',
            'publication_year' => $googleBook['volumeInfo']['publishedDate'] ?? 'Data Desconhecida',
            'image_url' => $googleBook['volumeInfo']['imageLinks']['thumbnail'] ?? '',
            'synopsis' => $googleBook['volumeInfo']['description'] ?? 'Sinopse não disponível',
            'isbn_number' => $this->getIsbn($googleBook['volumeInfo']['industryIdentifiers'] ?? []),
            'pages_amount' => $googleBook['volumeInfo']['pageCount'] ?? 'Número de Páginas Desconhecido'
        ];
    }

    // Retornar a view com os dados do livro
    return view('books.show', compact('book'));
}

    
public function searchBooks(Request $request)
{
    $query = $request->input('query', '');

    // Buscar livros no banco de dados local
    $localBooks = Book::with('author')->where('title', 'like', '%' . $query . '%')->get();

    // Buscar livros na API do Google Books
    $googleBooks = $this->googleBooksService->searchBooks($query);

    // Verificar a resposta da API do Google Books
    if (isset($googleBooks['error'])) {
        return redirect()->route('books.index')->withErrors('Erro ao buscar na API do Google Books: ' . $googleBooks['error']);
    }

    if (!isset($googleBooks['items'])) {
        return redirect()->route('books.index')->with('message', 'Nenhum livro encontrado na API do Google Books.');
    }

    // Padronizar a resposta da API do Google Books para o formato do modelo de livro
    $googleBooksFormatted = array_map(function ($book) {
        return (object) [
            'id' => $book['id'] ?? '', // Usar o ID real do livro da API
            'title' => $book['volumeInfo']['title'] ?? 'Título Desconhecido',
            'author' => isset($book['volumeInfo']['authors']) ? implode(', ', $book['volumeInfo']['authors']) : 'Autor Desconhecido',
            'publisher' => $book['volumeInfo']['publisher'] ?? 'Editora Desconhecida',
            'publication_year' => $book['volumeInfo']['publishedDate'] ?? 'Data Desconhecida',
            'image_url' => $book['volumeInfo']['imageLinks']['thumbnail'] ?? '',
            'isbn' => $this->getIsbn($book['volumeInfo']['industryIdentifiers'] ?? []),
            'page_count' => $book['volumeInfo']['pageCount'] ?? 'Número de Páginas Desconhecido'
        ];
    }, $googleBooks['items']);

    // Converter livros locais para um array simples
    $localBooksFormatted = $localBooks->map(function ($book) {
        return (object) [
            'id' => $book->id, // Inclui o ID real dos livros locais
            'title' => $book->title,
            'author' => $book->author ? $book->author->name : 'Autor Desconhecido',
            'publisher' => $book->publisher,
            'publication_year' => $book->publication_year,
            'image_url' => $book->image_url ?? '', // Inclui a imagem se disponível
            'isbn' => $book->isbn_number ?? 'ISBN não disponível',
            'page_count' => $book->pages_amount ?? 'Número de Páginas Desconhecido'
        ];
    });

    // Combinar os resultados locais e da API
    $combinedBooks = array_merge($localBooksFormatted->toArray(), $googleBooksFormatted);

    // Número de itens por página
    $perPage = 10;
    $currentPage = $request->input('page', 1);

    // Converter para uma coleção Laravel para poder usar o método paginate
    $paginatedBooks = collect($combinedBooks)->forPage($currentPage, $perPage);

    // Criar uma instância de LengthAwarePaginator
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

    public function download($id)
    {
        $book = Book::find($id);
        if (!$book || !$book->file_url) {
            return redirect()->route('books.index')->withErrors('File not found');
        }
        return response()->download(storage_path('app/' . $book->file_url));
    }

    public function store(Request $request)
{
    // Validação dos dados recebidos
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
        'description' => 'nullable|string',
    ]);

    // Criação do novo livro
    $book = Book::create($validated);

    // Manipulação de arquivo PDF, se existir
    if ($request->hasFile('file')) {
        $filePath = $request->file('file')->store('books');
        $book->file_url = $filePath;
    }

    // Manipulação de imagem, se existir
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('images', 's3');
        $book->image_name = $imagePath;
    }

    // Salvar as alterações no banco de dados
    $book->save();

    // Redirecionar para a lista de livros com mensagem de sucesso
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

    // Atualizar o livro com os dados validados
    $book->update($validated);

    // Manipulação de arquivo PDF, se existir
    if ($request->hasFile('file')) {
        // Excluir o arquivo antigo, se existir
        if ($book->file_url) {
            Storage::delete($book->file_url);
        }
        $filePath = $request->file('file')->store('books');
        $book->file_url = $filePath;
    }

    // Manipulação de imagem, se existir
    if ($request->hasFile('image')) {
        // Excluir a imagem antiga, se existir
        if ($book->image_name) {
            Storage::delete($book->image_name);
        }
        $imagePath = $request->file('image')->store('images');
        $book->image_name = $imagePath;
    }

    // Salvar as alterações no banco de dados
    $book->save();

    return redirect()->route('books.index')->with('success', 'Book updated successfully');
}


    public function edit($id)
{
    // Obtém o livro a ser editado
    $book = Book::findOrFail($id);

    // Obtém a lista de autores
    $authors = Author::all();

    // Passa o livro e a lista de autores para a view
    return view('books.edit', ['book' => $book, 'authors' => $authors]);
}

 
public function destroy($id)
{
    $book = Book::findOrFail($id);
    $book->delete();

    return redirect()->route('books.index')->with('success', 'Book deleted successfully');
}
}
