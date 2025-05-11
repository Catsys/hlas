<?php
namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Http\Server\MiddlewareInterface;

class CorsMiddleware implements MiddlewareInterface
{
    public function process(Request $request, Handler $handler): Response
    {
        $origin = $request->getHeaderLine('Origin') ?: '*';

        $response = new \Slim\Psr7\Response();

        // Preflight request — без выполнения handler
        if ($request->getMethod() === 'OPTIONS') {
            return $response
                ->withHeader('Access-Control-Allow-Origin', $origin)
                ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->withHeader('Access-Control-Allow-Credentials', 'true')
                ->withStatus(200);
        }

        // Обычные запросы
        $response = $handler->handle($request);

        return $response
            ->withHeader('Access-Control-Allow-Origin', $origin)
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Credentials', 'true');
    }
}
