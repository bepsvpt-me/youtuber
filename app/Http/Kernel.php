<?php

namespace App\Http;

use App\Http\Middleware\TrustProxies;
use Bepsvpt\SecureHeaders\SecureHeadersMiddleware;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Routing\Middleware\ValidateSignature;

final class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        TrustProxies::class,
        CheckForMaintenanceMode::class,
        SecureHeadersMiddleware::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth.basic' => AuthenticateWithBasicAuth::class,
        'cache.headers' => SetCacheHeaders::class,
        'signed' => ValidateSignature::class,
    ];

    /**
     * The priority-sorted list of middleware.
     *
     * This forces non-global middleware to always be in the given order.
     *
     * @var array
     */
    protected $middlewarePriority = [
        ThrottleRequests::class,
        SubstituteBindings::class,
    ];
}
