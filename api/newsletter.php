<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    json_response(['ok' => false, 'message' => 'Method not allowed.'], 405);
}

if (!csrf_valid($_POST['csrf'] ?? null)) {
    json_response(['ok' => false, 'message' => 'Your session expired. Refresh the page and try again.'], 419);
}

if (!empty($_POST['website'])) { // honeypot
    json_response(['ok' => true, 'message' => 'You are subscribed.']);
}

$email = strtolower(trim((string) ($_POST['email'] ?? '')));
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) {
    json_response(['ok' => false, 'message' => 'Enter a valid email address.'], 422);
}

$conn = db();
if (!$conn) {
    json_response(['ok' => false, 'message' => 'We could not subscribe you right now. Please try again later.'], 503);
}

try {
    $hash = ip_hash();
    // INSERT IGNORE keeps repeat sign-ups from erroring and does not reveal whether an address is already on the list.
    $stmt = $conn->prepare('INSERT IGNORE INTO subscribers (email, ip_hash) VALUES (?, ?)');
    $stmt->bind_param('ss', $email, $hash);
    $stmt->execute();
    $stmt->close();
} catch (mysqli_sql_exception $ex) {
    error_log('[mtelgon] subscribe failed: ' . $ex->getMessage());
    json_response(['ok' => false, 'message' => 'Something went wrong. Please try again shortly.'], 500);
}

json_response(['ok' => true, 'message' => 'You are subscribed. Watch your inbox for our next update.']);
