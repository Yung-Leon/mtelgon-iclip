<?php
declare(strict_types=1);

/**
 * Returns a shared mysqli connection, or null when the database is unreachable.
 * The public page keeps working (with built-in fallback content) if this returns null.
 */
function db(): ?mysqli
{
    static $conn = false;

    if ($conn !== false) {
        return $conn;
    }

    $cfg = (require __DIR__ . '/config.php')['db'];
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        $conn = new mysqli($cfg['host'], $cfg['user'], $cfg['pass'], $cfg['name'], $cfg['port']);
        $conn->set_charset('utf8mb4');
    } catch (mysqli_sql_exception $e) {
        error_log('[mtelgon] DB connection failed: ' . $e->getMessage());
        $conn = null;
    }

    return $conn;
}
