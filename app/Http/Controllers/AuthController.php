<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return redirect()->intended('books')->with('success', 'Login successful');
        }
        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
{
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|confirmed|min:8',
    ]);

    $user = User::create([
        'name' => $validatedData['name'],
        'email' => $validatedData['email'],
        'password' => Hash::make($validatedData['password']),
    ]);

    Auth::login($user);

    return redirect()->route('books.index')->with('success', 'Registration successful, you are now logged in.');
}

// AuthController.php

public function home()
{
    if (Auth::check()) {  // Verifica se o usuário está autenticado
        // Se o usuário estiver logado, redireciona para a lista de livros
        return redirect()->route('books.index');
    }

    // Se não estiver logado, exibe a página home
    return view('home'); // Certifique-se de ter a view 'home.blade.php' em resources/views/
}



    public function logout()
    {
        Auth::logout();
        return redirect('/')->with('success', 'Logged out successfully');
    }
}
