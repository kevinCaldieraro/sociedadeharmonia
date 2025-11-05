<?php

use App\Exceptions\ErrorToastException;
use App\Http\Middleware\AdminOnly;
use App\Http\Middleware\EncryptCookies;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Cookie\Middleware\EncryptCookies as BaseEncryptCookies;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;
use Tighten\Ziggy\Ziggy;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => AdminOnly::class
        ]);
        $middleware->web(
            append: [
                HandleInertiaRequests::class,
                AddLinkHeadersForPreloadedAssets::class
            ],
            replace: [
                BaseEncryptCookies::class => EncryptCookies::class
            ],
        );
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('app:update-subscription-statuses')->monthlyOn(16, '4:00');
        $schedule->command('app:start-month-tasks')->monthlyOn(1, '4:00');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->respond(function (Response $response, Throwable $exception, Request $request) {
            $statusCode = $response->getStatusCode();
            $errorTitles = [
                403 => 'Acesso negado',
                404 => 'Não encontrado',
                500 => 'Erro interno',
                503 => 'Serviço indisponível',
            ];
            $errorDetails = [
                403 => 'Desculpe, você não está autorizado para acessar esse conteúdo.',
                404 => 'Desculpe, o que você está procurando não foi encontrado.',
                500 => 'Ops, algo deu errado. Por favor tente novamente.',
                503 => 'Desculpe, nós estamos realizando a manutenção. Por favor tente novamente depois.',
            ];

            if (in_array($statusCode, [500, 503, 404, 403])) {
                if (
                    $statusCode === 500
                    && app()->hasDebugModeEnabled()
                    && get_class($exception) !== ErrorToastException::class
                ) {
                    return $response;
                } elseif (!$request->inertia()) {
                    // Show error page component for standard visits
                    return Inertia::render('Error', [
                        'errorTitles' => $errorTitles,
                        'errorDetails' => $errorDetails,
                        'status' => $statusCode,
                        'homepageRoute' => route('home'),
                        'ziggy' => fn () => [
                            ...(new Ziggy())->toArray(),
                            'location' => $request->url(),
                        ],
                    ])
                        ->toResponse($request)
                        ->setStatusCode($statusCode);
                } else {
                    // Return JSON response for PrimeVue toast to display, handled by Inertia router event listener
                    $errorSummary = "$statusCode - $errorTitles[$statusCode]";
                    $errorDetail = $errorDetails[$statusCode];
                    if (get_class($exception) === ErrorToastException::class) {
                        $errorSummary = "$statusCode - Error";
                        $errorDetail = $exception->getMessage();
                    }
                    return response()->json([
                        'error_summary' => $errorSummary,
                        'error_detail' => $errorDetail,
                    ], $statusCode);
                }
            } elseif ($statusCode === 419) {
                return back()->with([
                    'flash_warn' => 'The page expired, please try again.',
                ]);
            }

            return $response;
        });
    })->create();
