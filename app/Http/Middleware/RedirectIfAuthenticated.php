<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                // Vérifie si le mot de passe est encore le mot de passe par défaut
                if (Hash::check('DefaultPassword!', $user->password)) {
                    return redirect()->route('password.reset.prompt');
                }

                return redirect('/dashboard'); // Redirige vers le tableau de bord si déjà authentifié
            }
        }

        return $next($request);
    }
}