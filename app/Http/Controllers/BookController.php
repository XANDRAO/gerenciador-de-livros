<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Author;
use App\Services\GoogleBooksService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    private $googleBooksService;

    public function __construct(GoogleBooksService $googleBooksService)
    {
        $this->googleBooksService = $googleBooksService;
    }

    public function index()
    {
        // Paginar todos os livros
        $books = Book::paginate(10); // Paginação com 10 itens por página
        return response()->json($books);
    }
    
    public function show($id)
    {
        // Buscar livro específico por ID ou ISBN
        $book = Book::where('id', $id)->orWhere('isbn_number', $id)->firstOrFail();
        return response()->json($book);
    }
    
    public function searchBooks(Request $request)
    {
        $query = $request->input('query');
    
        // Pesquisar livro por nome ou nome do autor
        $books = Book::where('title', 'like', '%' . $query . '%')
                     ->orWhereHas('author', function ($q) use ($query) {
                         $q->where('name', 'like', '%' . $query . '%');
                     })
                     ->get();
    
        return response()->json($books);
    }
    
    public function download($id)
    {
        // Baixar um livro específico por ID
        $book = Book::where('id', $id)->orWhere('isbn_number', $id)->firstOrFail();
    
        if ($book->file_url && Storage::exists($book->file_url)) {
            return response()->download(storage_path('app/' . $book->file_url));
        }
    
        return response()->json(['error' => 'File not found.'], 404);
    }
    
    public function store(Request $request)
    {
        // Validar campos
        $validatedData = $request->validate([
            'title' => 'required|string',
            'author_id' => 'required|exists:authors,id',
            'publication_year' => 'required|integer',
            'isbn_number' => 'required|string|unique:books,isbn_number',
            'file' => 'required|file|mimes:pdf',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif'
        ]);
    
        // Fazer upload de fotos e livros.pdf
        $filePath = $request->file('file')->store('books');
        $imagePath = $request->file('image') ? $request->file('image')->store('images') : null;
    
        $book = Book::create(array_merge($validatedData, [
            'file_url' => $filePath,
            'image_name' => $imagePath
        ]));
    
        return response()->json($book, 201);
    }
    
    public function update(Request $request, $id)
    {
        // Atualizar campos com validação
        $book = Book::findOrFail($id);
    
        $validatedData = $request->validate([
            'title' => 'sometimes|required|string',
            'author_id' => 'sometimes|required|exists:authors,id',
            'publication_year' => 'sometimes|required|integer',
            'isbn_number' => 'sometimes|required|string|unique:books,isbn_number,' . $id,
            'file' => 'nullable|file|mimes:pdf',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif'
        ]);
    
        if ($request->hasFile('file')) {
            // Atualizar arquivo PDF
            Storage::delete($book->file_url);
            $validatedData['file_url'] = $request->file('file')->store('books');
        }
    
        if ($request->hasFile('image')) {
            // Atualizar imagem
            Storage::delete($book->image_name);
            $validatedData['image_name'] = $request->file('image')->store('images');
        }
    
        $book->update($validatedData);
    
        return response()->json($book);
    }
    
    public function destroy($id)
    {
        // Deletar livro por ID ou ISBN
        $book = Book::where('id', $id)->orWhere('isbn_number', $id)->firstOrFail();
        
        Storage::delete($book->file_url);
        Storage::delete($book->image_name);
        
        $book->delete();
    
        return response()->json(['message' => 'Book deleted successfully.']);
    } 
}  