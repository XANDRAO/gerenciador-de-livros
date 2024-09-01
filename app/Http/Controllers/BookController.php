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

        // Paginar todos os livros
        $books = Book::paginate($limit, ['*'], 'page', $page);
        
        return response()->json($books);
    }
    
    public function show($id)
    {
        $book = Book::where('id', $id)->orWhere('isbn_number', $id)->first();
        if (!$book) {
            return response()->json(['error' => 'Book not found'], 404);
        }
        return response()->json($book);
    }

    public function searchBooks(Request $request)
{
    $query = $request->input('query');

    // 1. Buscar livros no banco de dados local
    $localBooks = Book::where('title', 'like', '%' . $query . '%')->get();

    // 2. Buscar livros na API do Google Books
    $googleBooks = $this->googleBooksService->searchBooks($query);

    // 3. Padronizar a resposta da API do Google Books para o formato do modelo de livro
    $googleBooksFormatted = array_map(function ($book) {
        return [
            'title' => $book['volumeInfo']['title'] ?? '',
            'author' => isset($book['volumeInfo']['authors']) ? implode(', ', $book['volumeInfo']['authors']) : '',
            'publisher' => $book['volumeInfo']['publisher'] ?? '',
            'published_date' => $book['volumeInfo']['publishedDate'] ?? '',
            'description' => $book['volumeInfo']['description'] ?? '',
            'thumbnail' => $book['volumeInfo']['imageLinks']['thumbnail'] ?? '',
        ];
    }, $googleBooks['items'] ?? []);

    // 4. Combina os resultados locais e da API
    $combinedBooks = $localBooks->toArray(); // Converte os livros locais para array
    $combinedBooks = array_merge($combinedBooks, $googleBooksFormatted); // Mescla com os livros da API
    
    return response()->json($combinedBooks);
}

    public function download($id)
    {
        $book = Book::find($id);
        if (!$book || !$book->file_url) {
            return response()->json(['error' => 'File not found'], 404);
        }
        return response()->download(storage_path('app/' . $book->file_url));
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
            'cep' => 'required|string|max:9',
        ]);

        $book = new Book();
        $book->title = $validated['title'];
        $book->author_id = $validated['author_id'];
        $book->publisher = $validated['publisher'];
        $book->publication_year = $validated['publication_year'];
        $book->pages_amount = $validated['pages_amount'];
        $book->isbn_number = $validated['isbn_number'];

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('books');
            $book->file_url = $filePath;
        }

        if ($request->hasFile('image')) {
            // Fazer o upload da imagem para o S3
            $imagePath = $request->file('image')->store('images', 's3');
            $book->image_name = $imagePath;

            // Obter a URL pública da imagem
            $book->image_url = Storage::disk('s3')->url($imagePath);
        }

        $book->save();
        return response()->json($book, 201);
    }

    public function update(Request $request, $id)
    {
        $book = Book::find($id);
        if (!$book) {
            return response()->json(['error' => 'Book not found'], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'author_id' => 'sometimes|required|exists:authors,id',
            'publisher' => 'sometimes|required|string|max:255',
            'publication_year' => 'sometimes|required|date_format:Y',
            'pages_amount' => 'sometimes|required|integer',
            'isbn_number' => 'sometimes|required|string|unique:books,isbn_number,' . $id,
            'file' => 'nullable|file|mimes:pdf|max:20480',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'cep' => 'sometimes|required|string|max:9',
        ]);

        if (isset($validated['cep'])) {
            $address = $this->brasilAPIService->getAddressByCep($validated['cep']);
            if ($address) {
                $book->city = $address['city'] ?? null;
                $book->state = $address['state'] ?? null;
                $book->neighborhood = $address['neighborhood'] ?? null;
                $book->street = $address['street'] ?? null;
            } else {
                return response()->json(['error' => 'Invalid CEP or unable to fetch address'], 400);
            }
        }

        $book->update($validated);

        if ($request->hasFile('file')) {
            Storage::delete($book->file_url);
            $filePath = $request->file('file')->store('books');
            $book->file_url = $filePath;
        }

        if ($request->hasFile('image')) {
            Storage::delete($book->image_name);
            $imagePath = $request->file('image')->store('images');
            $book->image_name = $imagePath;
        }

        $book->save();
        return response()->json($book);
    }

    public function destroy($id)
    {
        $book = Book::find($id);
        if (!$book) {
            return response()->json(['error' => 'Book not found'], 404);
        }

        Storage::delete($book->file_url);
        Storage::delete($book->image_name);
        $book->delete();
        return response()->json(['message' => 'Book deleted successfully']);
    }
}
