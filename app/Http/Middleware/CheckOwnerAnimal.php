<?php

namespace App\Http\Middleware;

use App\Models\Animal;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckOwnerAnimal
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $animal = $request->route('animal'); 

        if ($animal && $animal->user_id !== auth()->id()) {

            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return $next($request);
    }
}
