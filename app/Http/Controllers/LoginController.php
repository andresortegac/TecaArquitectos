<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class LoginController extends Controller
{
    // ? Muestra el formulario login
    public function show()
    {
        // Si ya esta logueado, redirige segun rol
        if (Auth::check()) {

            // ? Admins a usuario-panel
            if (in_array(Auth::user()->role, ['admin', 'superadmin'])) {
                return redirect()->route('usuario.panel');
            }

            // ? Developers (y cualquier otro) a register
            return redirect()->route('dashboard');
        }

        // Si no esta logueado muestra login
        return view('LOGIN.loginView');
    }

    // ? Procesa login


    public function login(Request $request)
{
    $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($request->only('email', 'password'))) {
        $request->session()->regenerate();

        // ✅ TODOS al dashboard
        return redirect()->route('dashboard');
    }

    return back()->withErrors([
        'email' => 'Usuario o contraseña incorrectos.'
    ])->withInput();
}



    // ? Logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Regresa al login ( / )
        return redirect()->route('login');
    }
}