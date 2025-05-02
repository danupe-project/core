<?php

namespace Danupe\Core\Middlewares;

use Danupe\Core\Classes\Request;

class CsrfMiddleware
{
    public function handle(Request $request, callable $next): void
    {
        // Zugriff auf die HTTP-Methode des Requests
        if ($request->getMethod() === 'POST') {
            // Zugriff auf den Body des Requests
            $parsedBody = $request->getParsedBody();
            $csrfToken = $parsedBody['csrf_token'] ?? '';

            // Zugriff auf die Session-Daten (Session muss vorher gesetzt werden)
            $sessionToken = d()->session()->get('csrf_token', null);

            // CSRF-Token prüfen
            if (empty($csrfToken) || $csrfToken !== $sessionToken) {
                http_response_code(403);
                echo "🚫 Ungültiges CSRF-Token.";
                exit;
            }
        }

        // Falls noch kein Token generiert wurde, generieren wir einen
        if (empty(d()->session()->get('csrf_token'))) {
            $csrfToken = bin2hex(random_bytes(32));
            d()->session()->set('csrf_token', $csrfToken);
        }

        // Ensure $csrfToken is defined before use
        $csrfToken = $csrfToken ?? '';

        // Token im Request mitgeben für spätere Verwendung (z. B. in Views)
        $request->set('_csrf_token', $csrfToken);

        // Middleware Chain fortsetzen
        $next($request);
    }
}