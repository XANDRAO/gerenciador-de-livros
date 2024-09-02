<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    private $brasilAPIService;
    
    public function __construct(BrasilAPIService $brasilAPIService)
    {
        $this->brasilAPIService = $brasilAPIService;
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

        $author = Author::create($validated);
        return response()->json($author, 201);
    }
}
