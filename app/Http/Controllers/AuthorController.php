<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;
use App\Http\Services\BrasilAPIService;

class AuthorController extends Controller
{
    private $brasilAPIService;
    
    public function __construct(BrasilAPIService $brasilAPIService)
    {
        $this->brasilAPIService = $brasilAPIService;
    }

    public function index()
    {
        $authors = Author::paginate(10);
        return view('authors.index', ['authors' => $authors]);
    }

    public function create()
    {
        return view('authors.create');
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
        'name' => 'required|string|max:255',
        'street_address' => 'nullable|string|max:255',
        'city' => 'nullable|string|max:255',
        'state' => 'nullable|string|max:255',
        'country' => 'nullable|string|max:255',
        'cep' => 'nullable|string|max:9',
        'picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    $author = new Author($validated);

    // Manipula a imagem do autor, se existir
    if ($request->hasFile('picture')) {
        $picturePath = $request->file('picture')->store('authors', 's3', [
            'visibility' => 'public',
        ]);

        if ($picturePath) {
            $author->picture_url = env('AWS_URL') . '/' . $picturePath;  // Constrói a URL completa do S3
        } else {
            return back()->withErrors('Erro ao salvar a imagem no S3.');
        }
    }

    $author->save();

    return redirect()->route('authors.index')->with('success', 'Autor adicionado com sucesso!');
}


    public function show($id)
    {
        $author = Author::find($id);

        if (!$author) {
            return redirect()->route('authors.index')->with('error', 'Autor não encontrado');
        }

        return view('authors.show', ['author' => $author]);
    }

    public function edit($id)
    {
        $author = Author::find($id);

        if (!$author) {
            return redirect()->route('authors.index')->with('error', 'Autor não encontrado');
        }

        return view('authors.edit', ['author' => $author]);
    }

    public function update(Request $request, $id)
    {
        $author = Author::find($id);

        if (!$author) {
            return redirect()->route('authors.index')->with('error', 'Autor não encontrado');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'street_address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'cep' => 'nullable|string|max:9',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('picture')) {
            if ($author->picture_url) {
                \Storage::delete($author->picture_url);
            }

            $picturePath = $request->file('picture')->store('authors');
            $validated['picture_url'] = $picturePath;
        }

        $author->update($validated);
        return redirect()->route('authors.index')->with('success', 'Autor atualizado com sucesso');
    }

    public function destroy($id)
    {
        $author = Author::find($id);

        if (!$author) {
            return redirect()->route('authors.index')->with('error', 'Autor não encontrado');
        }
        
        if ($author->picture_url) {
            \Storage::delete($author->picture_url);
        }

        $author->delete();
        return redirect()->route('authors.index')->with('success', 'Autor excluído com sucesso');
    }
}
