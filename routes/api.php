<?php

// All real API routes live in each module's own routes/api.php (see
// Modules/*/routes/api.php + Modules/*/app/Providers/RouteServiceProvider.php
// ::mapApiRoutes()) — this root file only needs to exist so bootstrap/app.php
// can register the real 'api' middleware group via withRouting(api: ...),
// which is what makes every module's `Route::middleware('api')->prefix('api')`
// wiring resolve correctly instead of erroring on an undefined group.
