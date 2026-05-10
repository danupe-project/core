<?php

namespace Danupe\Core\Middlewares;

use Danupe\Core\Classes\Request;

class CsrfMiddleware
{
    public function handle(Request $request, callable $next): void
    {
        // Zugriff auf die HTTP-Methode des Requests
        if ($request->getMethod() === 'POST') {
            
            $parsedBody = $request->getParsedBody();
            
            // 1. Versuch: Klassisches POST-Array (Normale Formulare)
            $csrfToken = $parsedBody['csrf_token'] ?? '';

            // 2. Versuch: HTTP-Header prüfen (Standard für Fetch/AJAX)
            if (empty($csrfToken)) {
                // PHP wandelt Header in $_SERVER um: X-CSRF-TOKEN wird zu HTTP_X_CSRF_TOKEN
                $csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
            }

            // 3. Versuch: Raw JSON Body auslesen (falls Token im JSON-Payload geschickt wurde)
            if (empty($csrfToken)) {
                $rawBody = file_get_contents('php://input');
                if (!empty($rawBody)) {
                    $jsonBody = json_decode($rawBody, true);
                    $csrfToken = $jsonBody['csrf_token'] ?? '';
                }
            }

            // Zugriff auf die Session-Daten
            $sessionToken = danupe()->session()->get('csrf_token', null);

            // CSRF-Token prüfen
            if (empty($csrfToken) || $csrfToken !== $sessionToken) {
                http_response_code(403);
                
                $requestedWith = strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '');
                $contentType = strtolower($_SERVER['CONTENT_TYPE'] ?? '');
                
                // Wir erweitern die Fehler-Ausgabe, damit auch unser JSON-Fetch eine saubere JSON-Antwort bekommt
                if ($requestedWith === 'littlebigtable' || strpos($contentType, 'application/json') !== false) {
                    header('Content-Type: application/json');
                    echo json_encode(['error' => 'invalid_csrf', 'message' => 'Ungültiges CSRF-Token.']);
                } else {
                    echo "🚫 Ungültiges CSRF-Token.";
                }
                exit;
            }
        }

        // Falls noch kein Token generiert wurde, generieren wir einen
        $csrfToken = danupe()->session()->get('csrf_token');
        if (empty($csrfToken)) {
            $csrfToken = bin2hex(random_bytes(32));
            danupe()->session()->set('csrf_token', $csrfToken);
        }

        // Token im Request mitgeben für spätere Verwendung
        $request->set('_csrf_token', $csrfToken);

        // Middleware Chain fortsetzen
        $next($request);
    }
}