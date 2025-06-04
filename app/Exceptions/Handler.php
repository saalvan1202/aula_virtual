<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Inertia\Inertia;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {

    }
    public function render($request, Throwable $e)
    {
        $response = parent::render($request, $e);

        if (in_array($response->status(), [503, 404, 403])) {
            Inertia::setRootView('layouts.app');
            return Inertia::render('Errors/Error'.$response->status(), [
                    'message' => $e->getMessage(),
                ])
                ->toResponse($request)
                ->setStatusCode($response->status());
        } else if ($response->status() === 419) {
            return back()->with([
                'message' => 'La página ha caducado, inténtelo de nuevo',
            ]);
        }
        return $response;
    }

    protected function context(): array
    {
        return array_merge(parent::context(), [
            'tenant' => schema_name()??'',
        ]);
    }
}
