<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckStudent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifie si l'utilisateur est connecté
        if (auth()->check()) {
            // Récupère l'utilisateur connecté
            $utilisateur = auth()->user();

            // Vérifie si le rôle de l'utilisateur est "Etudiant"
            if ($utilisateur->role === 'Etudiant') {
                return $next($request);
            }
        }

        // Redirige ou retourne une réponse d'erreur si l'utilisateur n'est pas Etudiant
        return response()->json(['error' => 'Accès non autorisé'], 403);
    }
}
