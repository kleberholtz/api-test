<?php

use App\Exceptions\createException;
use App\Exceptions\deleteException;
use App\Exceptions\failException;
use App\Exceptions\updateException;
use App\goHoltz\API\Response as API;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        // web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        apiPrefix: '/',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'accessToken' => \App\Http\Middleware\accessToken::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->dontReport([]);

        $exceptions->dontFlash([
            'current_password',
            'password',
            'password_confirmation',
        ]);

        $exceptions->render(function (\Throwable $e, Request $request) {
            $exceptionMap = [
                MethodNotAllowedHttpException::class => ['Method not allowed.', API::HTTP_METHOD_NOT_ALLOWED],
                NotFoundHttpException::class => ['Not found.', API::HTTP_NOT_FOUND],
                TooManyRequestsHttpException::class => ['Too many requests.', API::HTTP_TOO_MANY_REQUESTS],
                UnauthorizedHttpException::class => ['Unauthorized.', API::HTTP_UNAUTHORIZED],

                failException::class => [$e->getMessage(), API::HTTP_BAD_REQUEST],

                createException::class => [$e->getMessage(), API::HTTP_INTERNAL_SERVER_ERROR],
                deleteException::class => [$e->getMessage(), API::HTTP_INTERNAL_SERVER_ERROR],
                updateException::class => [$e->getMessage(), API::HTTP_INTERNAL_SERVER_ERROR],

                \PDOException::class => ['Database error.', API::HTTP_INTERNAL_SERVER_ERROR],
                \Throwable::class => ['Internal server error.', API::HTTP_INTERNAL_SERVER_ERROR],
            ];

            foreach ($exceptionMap as $class => [$message, $statusCode]) {
                if ($e instanceof $class) {
                    $response = API::dataStructure();

                    if (app()->isLocal()) {
                        $response->addMessage($e->getMessage(), $response::MESSAGE_DEBUG);
                        $response->addMessage($e->getFile() . ':' . $e->getLine(), $response::MESSAGE_DEBUG);
                    }

                    return API::fail($response, $message, $statusCode);
                }
            }
        });
    })->create();
