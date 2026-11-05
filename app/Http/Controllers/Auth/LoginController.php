<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class LoginController extends Controller
{
    /**
     * Affiche le formulaire de login
     */
    public function showLoginForm()
    {
        return view('auth.login'); // On va créer cette vue
    }

    /**
     * Gère la tentative de connexion
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            Log::info('Login successful for: ' . $user->email . '. Roles: ' . json_encode($user->getRoleNames()));

            // Redirection basée sur le rôle
            if ($user->hasRole('Admin') || $user->hasRole('Personnel')) {
                Log::info('Redirecting Admin/Personnel to admin.dashboard');
                return redirect()->intended(route('admin.dashboard'));
            } elseif ($user->hasRole('Parent')) {
                Log::info('Redirecting Parent to parent.dashboard');
                return redirect()->intended(route('parent.dashboard'));
            } elseif ($user->hasRole('Enseignant')) {
                Log::info('Redirecting Enseignant to enseignant.accueil');
                return redirect()->intended(route('enseignant.accueil'));
            } else {
                Log::info('No specific role dashboard matched, redirecting to generic dashboard.');
                return redirect()->intended(route('dashboard'));
            }
        }

        Log::warning('Login failed for email: ' . $request->email);
        return back()->withErrors([
            'email' => 'Les informations d\'identification fournies ne correspondent pas à nos enregistrements.',
        ])->onlyInput('email');
    }

    /**
     * Gère la déconnexion
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Log::info('User logged out.');
        return redirect('/'); // Rediriger vers la page d'accueil
    }

    /**
     * Constructeur pour appliquer le middleware 'guest' aux méthodes de login
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}