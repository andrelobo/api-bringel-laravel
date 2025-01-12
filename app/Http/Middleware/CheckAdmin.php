<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class CheckAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('Middleware CheckAdmin: Iniciando verificação', [
            'user_id' => $request->user() ? $request->user()->id : null,
            'route' => $request->route()->getName(),
            'method' => $request->method(),
            'url' => $request->fullUrl()
        ]);

        if (!$request->user()) {
            Log::warning('Middleware CheckAdmin: Usuário não autenticado');
            return response()->json(['message' => 'Usuário não autenticado.'], 401);
        }

        Log::info('Middleware CheckAdmin: Verificando role do usuário', [
            'user_id' => $request->user()->id,
            'user_role' => $request->user()->role,
            'is_admin' => $request->user()->hasRole('admin')
        ]);

        if (!$request->user()->hasRole('admin')) {
            Log::warning('Middleware CheckAdmin: Usuário não é admin', [
                'user_id' => $request->user()->id,
                'user_role' => $request->user()->role
            ]);
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }

        Log::info('Middleware CheckAdmin: Acesso autorizado', [
            'user_id' => $request->user()->id
        ]);
        return $next($request);
    }
}

