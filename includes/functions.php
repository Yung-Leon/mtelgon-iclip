<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

/** Escape for HTML output. */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function config(string $group, ?string $key = null)
{
    static $cfg = null;
    $cfg ??= require __DIR__ . '/config.php';
    return $key === null ? $cfg[$group] : $cfg[$group][$key];
}

function start_secure_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => !empty($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function csrf_token(): string
{
    start_secure_session();
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_valid(?string $token): bool
{
    start_secure_session();
    return is_string($token)
        && !empty($_SESSION['csrf'])
        && hash_equals($_SESSION['csrf'], $token);
}

function ip_hash(): string
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    return hash('sha256', $ip . config('ip_salt') ?? '');
}

function json_response(array $payload, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/**
 * Latest published news. Reads MySQL; falls back to built-in items if the DB is down or empty.
 *
 * @return array<int, array<string, string>>
 */
function get_news(int $limit = 4): array
{
    $content = require __DIR__ . '/content.php';
    $conn = db();

    if ($conn) {
        try {
            $stmt = $conn->prepare(
                'SELECT title, category, excerpt, image_url, url, published_at
                   FROM news
                  WHERE is_published = 1
               ORDER BY published_at DESC
                  LIMIT ?'
            );
            $stmt->bind_param('i', $limit);
            $stmt->execute();
            $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            if ($rows) {
                return $rows;
            }
        } catch (mysqli_sql_exception $e) {
            error_log('[mtelgon] news query failed: ' . $e->getMessage());
        }
    }

    return array_slice($content['fallback_news'], 0, $limit);
}

function format_date(string $date): string
{
    $ts = strtotime($date);
    return $ts ? date('j F Y', $ts) : '';
}
