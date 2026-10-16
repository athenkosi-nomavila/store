<?php

namespace App\Support;

use Illuminate\Database\DetectsLostConnections;
use Throwable;

/**
 * Tells a database outage (server down, connection dropped) apart from a
 * bug in a query, using Laravel's list of lost-connection messages.
 */
final class DatabaseOutage
{
    use DetectsLostConnections;

    public static function causedBy(Throwable $e): bool
    {
        return (new self)->causedByLostConnection($e);
    }
}
