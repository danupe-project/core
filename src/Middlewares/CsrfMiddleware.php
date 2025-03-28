<?php

namespace Danupe\Core\Middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class CsrfMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        if ($request->getMethod() === 'POST') {
            $parsedBody = $request->getParsedBody();
            $csrfToken = $parsedBody['csrf_token'] ?? '';

            if (empty($csrfToken) || $csrfToken !== ($_SESSION['csrf_token'] ?? '')) {
                $response = new \Slim\Psr7\Response();
                $response->getBody()->write("🚫 Ungültiges CSRF-Token.");
                return $response->withStatus(403);
            }
        }

        return $handler->handle($request);
    }
}