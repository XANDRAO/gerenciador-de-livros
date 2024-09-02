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
            'picture_url' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('picture_url')) {
            $picturePath = $request->file('picture_url')->store('authors');
            $validated['picture_url'] = $picturePath;
        }

        Author::create($validated);
        return redirect()->route('authors.index');
    }

    public function show($id)
    {
        $author = Author::find($id);

        if (!$author) {
            return redirect()->route('authors.index')->with('error', 'Author not found');
        }

        return view('authors.show', ['author' => $author]);
    }

    public function edit($id)
    {
        $author = Author::find($id);

        if (!$author) {
            return redirect()->route('authors.index')->with('error', 'Author not found');
        }

        return view('authors.edit', ['author' => $author]);
    }

    public function update(Request $request, $id)
    {
        $author = Author::find($id);

        if (!$author) {
            return redirect()->route('authors.index')->with('error', 'Author not found');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'street_address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'picture_url' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('picture_url')) {
            // Delete old picture if exists
            if ($author->picture_url) {
                \Storage::delete($author->picture_url);
            }

            $picturePath = $request->file('picture_url')->store('authors');
            $validated['picture_url'] = $picturePath;
        }

        $author->update($validated);
        return redirect()->route('authors.index');
    }

    public function destroy($id)
    {
        $author = Author::find($id);

        if (!$author) {
            return redirect()->route('authors.index')->with('error', 'Author not found');
        }

        // Delete picture if exists
        if ($author->picture_url) {
            \Storage::delete($author->picture_url);
        }

        $author->delete();
        return redirect()->route('authors.index')->with('success', 'Author deleted successfully');
    }
}
