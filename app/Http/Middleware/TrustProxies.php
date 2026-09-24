<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

/**
 * The app runs behind the host's NGINX (see compose.yaml: the RoadRunner port is
 * published on 127.0.0.1 only), so the real scheme/client IP arrive as
 * X-Forwarded-* headers.
 *
 * The framework's middleware defaults $proxies to null, which trusts nobody and
 * makes Laravel see every request as plain http - hence this subclass, the same
 * shape Laravel skeletons shipped before the config-file variant.
 *
 * '*' means "trust the immediate caller", which is the NGINX on this host; the
 * container port is not reachable from anywhere else.
 */
class TrustProxies extends Middleware
{
    /** @var array<int, string>|string|null */
    protected $proxies = '*';

    protected $headers = Request::HEADER_X_FORWARDED_FOR
        | Request::HEADER_X_FORWARDED_HOST
        | Request::HEADER_X_FORWARDED_PORT
        | Request::HEADER_X_FORWARDED_PROTO
        | Request::HEADER_X_FORWARDED_PREFIX;
}
