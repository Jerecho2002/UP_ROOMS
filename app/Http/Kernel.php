<?php
protected $routeMiddleware = [
    // ... existing middleware
    'auth' => \App\Http\Middleware\Authenticate::class,
    'role' => \App\Http\Middleware\RoleMiddleware::class,
];
?>
