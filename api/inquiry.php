<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    json_response(['ok' => false, 'message' => 'Method not allowed.'], 405);
}

if (!csrf_valid($_POST['csrf'] ?? null)) {
    json_response(['ok' => false, 'message' => 'Your session expired. Refresh the page and try again.'], 419);
}

// Honeypot: real visitors never fill this hidden field.
if (!empty($_POST['website'])) {
    json_response(['ok' => true, 'message' => 'Thank you. We will be in touch.']);
}

$name   = trim((string) ($_POST['name'] ?? ''));
$email  = trim((string) ($_POST['email'] ?? ''));
$org    = trim((string) ($_POST['organisation'] ?? ''));
$int    = (string) ($_POST['interest'] ?? 'other');
$msg    = trim((string) ($_POST['message'] ?? ''));

$allowed = ['volunteer', 'partner', 'donate', 'tree', 'csr', 'other'];
$errors  = [];

if (mb_strlen($name) < 2 || mb_strlen($name) > 120)  { $errors['name'] = 'Enter your name.'; }
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 190) { $errors['email'] = 'Enter a valid email address.'; }
if (mb_strlen($org) > 160)                            { $errors['organisation'] = 'Organisation name is too long.'; }
if (!in_array($int, $allowed, true))                  { $int = 'other'; }
if (mb_strlen($msg) < 10 || mb_strlen($msg) > 2000)   { $errors['message'] = 'Write a message of 10 to 2,000 characters.'; }

if ($errors) {
    json_response(['ok' => false, 'message' => 'Please check the highlighted fields.', 'errors' => $errors], 422);
}

$conn = db();
if (!$conn) {
    json_response(['ok' => false, 'message' => 'We could not save your message right now. Please email ' . config('site', 'email') . '.'], 503);
}

try {
    $hash = ip_hash();

    // Rate limit: 5 messages per visitor per hour.
    $stmt = $conn->prepare('SELECT COUNT(*) FROM inquiries WHERE ip_hash = ? AND created_at > (NOW() - INTERVAL 1 HOUR)');
    $stmt->bind_param('s', $hash);
    $stmt->execute();
    $stmt->bind_result($recent);
    $stmt->fetch();
    $stmt->close();

    if ($recent >= 5) {
        json_response(['ok' => false, 'message' => 'Too many messages in a short time. Please try again later.'], 429);
    }

    $stmt = $conn->prepare('INSERT INTO inquiries (name, email, organisation, interest, message, ip_hash) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('ssssss', $name, $email, $org, $int, $msg, $hash);
    $stmt->execute();
    $stmt->close();
} catch (mysqli_sql_exception $ex) {
    error_log('[mtelgon] inquiry insert failed: ' . $ex->getMessage());
    json_response(['ok' => false, 'message' => 'Something went wrong on our side. Please try again shortly.'], 500);
}

// Best-effort email notification. Newlines are stripped from header values to block header injection.
if (config('notify_inquiries')) {
    $clean = static fn(string $v): string => preg_replace('/[\r\n]+/', ' ', $v);
    $subject = 'New website inquiry: ' . $clean($int);
    $body = "Name: {$clean($name)}\nEmail: {$clean($email)}\nOrganisation: {$clean($org)}\nInterest: {$int}\n\n{$msg}\n";
    $headers = 'From: ' . config('site', 'email') . "\r\nReply-To: " . $clean($email) . "\r\nContent-Type: text/plain; charset=UTF-8";
    @mail(config('site', 'email'), $subject, $body, $headers);
}

json_response(['ok' => true, 'message' => 'Thank you, ' . $name . '. We received your message and will reply by email.']);
