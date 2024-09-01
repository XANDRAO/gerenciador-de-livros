<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Author;
use App\http\Services\BrasilAPIService;

class AuthorController extends Controller
{
    private $brasilApiService;

    public function __construct(BrasilAPIService $brasilApiService)
    {
        $this->brasilApiService = $brasilApiService;
    }

    // Método para listar todos os autores
    public function index()
    {
        $authors = Author::all();
        return response()->json($authors);
    }

    // Método para exibir um autor específico
    public function show($id)
    {
        $author = Author::find($id);
        if (!$author) {
            return response()->json(['error' => 'Author not found'], 404);
        }
        return response()->json($author);
    }

    // Método para criar um novo autor
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cep' => 'required|string|max:9',
        ]);

        try {
            $address = $this->brasilApiService->getAddressByCep($request->input('cep'));

            if ($request->hasFile('picture_url')) {
                $picturePath = $request->file('picture_url')->store('authors');
                $validated['picture_url'] = $picturePath;
            }

            $author = Author::create([
                'name' => $request->input('name'),
                'street_address' => $address['street'] ?? '',
                'city' => $address['city'] ?? '',
                'state' => $address['state'] ?? '',
                'country' => 'Brasil',
                'picture_url' => $validated['picture_url'] ?? null,
            ]);

            return response()->json($author, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
{
    $author = Author::find($id);

    if (!$author) {
        return response()->json(['error' => 'Author not found'], 404);
    }

    $validated = $request->validate([
        'name' => 'sometimes|required|string|max:255',
        'street_address' => 'nullable|string|max:255',
        'city' => 'nullable|string|max:255',
        'state' => 'nullable|string|max:255',
        'country' => 'nullable|string|max:255',
        'picture_url' => 'nullable|file|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    // Atualiza os campos se estiverem presentes
    if (isset($validated['name'])) $author->name = $validated['name'];
    if (isset($validated['street_address'])) $author->street_address = $validated['street_address'];
    if (isset($validated['city'])) $author->city = $validated['city'];
    if (isset($validated['state'])) $author->state = $validated['state'];
    if (isset($validated['country'])) $author->country = $validated['country'];

    // Atualiza a imagem se fornecida
    if ($request->hasFile('picture_url')) {
        if ($author->picture_url) {
            Storage::delete($author->picture_url);
        }
        $picturePath = $request->file('picture_url')->store('authors');
        $author->picture_url = $picturePath;
    }

    $author->save();
    return response()->json($author);
}

public function destroy($id)
{
    $author = Author::find($id);
    
    if (!$author) {
        return response()->json(['error' => 'Author not found'], 404);
    }

    $author->delete();

    return response()->json(['message' => 'Author deleted successfully'], 200);
}
}
