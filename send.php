<?php
declare(strict_types=1);

/**
 * send.php — riceve il form di contatto e invia la richiesta via email ad Aurora.
 * Usa PHPMailer con SMTP autenticato Aruba (le credenziali stanno in config.php).
 *
 * Requisiti hosting: PHP 7.4+ con estensione OpenSSL attiva (standard su Aruba Linux).
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/lib/PHPMailer/Exception.php';
require __DIR__ . '/lib/PHPMailer/PHPMailer.php';
require __DIR__ . '/lib/PHPMailer/SMTP.php';

/** Il client vuole una risposta JSON? (fetch/AJAX) */
function wants_json(): bool {
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    $xrw    = strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '');
    return strpos($accept, 'application/json') !== false || $xrw === 'xmlhttprequest';
}

/** Risponde e termina. JSON per il JS, redirect per chi ha JS disattivato. */
function respond(bool $ok, string $error = '', int $code = 200): void {
    if (wants_json()) {
        http_response_code($ok ? 200 : $code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($ok ? ['ok' => true] : ['ok' => false, 'error' => $error]);
    } else {
        header('Location: index.html?' . ($ok ? 'sent=1' : 'err=1') . '#prenota', true, 302);
    }
    exit;
}

/** Pulizia campo breve (rimuove a-capo per prevenire header injection). */
function clean(string $key, int $max = 2000): string {
    $v = trim((string)($_POST[$key] ?? ''));
    $v = substr($v, 0, $max);
    return str_replace(["\r", "\n"], [' ', ' '], $v);
}

// --- Solo POST ---
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    respond(false, 'Metodo non consentito.', 405);
}

// --- Honeypot: se il campo nascosto è compilato, è un bot: fingiamo successo ---
if (trim((string)($_POST['website'] ?? '')) !== '') {
    respond(true);
}

// --- Raccolta dati ---
$nome      = clean('nome', 120);
$email     = trim((string)($_POST['email'] ?? ''));
$sede      = clean('sede', 120);
$servizio  = clean('servizio', 120);
$messaggio = substr(trim((string)($_POST['messaggio'] ?? '')), 0, 4000);

// --- Validazione ---
if ($nome === '') {
    respond(false, 'Il nome è obbligatorio.', 422);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond(false, 'Indirizzo email non valido.', 422);
}

// --- Config (credenziali SMTP) ---
$cfgFile = __DIR__ . '/config.php';
if (!is_file($cfgFile)) {
    error_log('Aurora form: config.php mancante.');
    respond(false, 'Servizio momentaneamente non disponibile.', 500);
}
$cfg = require $cfgFile;

// --- Corpo email (testo semplice) ---
$corpo = implode("\n", [
    'Nuova richiesta dal sito Aurora Personal Pilates',
    str_repeat('-', 46),
    'Nome:      ' . $nome,
    'Email:     ' . $email,
    'Sede:      ' . ($sede !== '' ? $sede : '—'),
    'Servizio:  ' . ($servizio !== '' ? $servizio : '—'),
    '',
    'Messaggio:',
    ($messaggio !== '' ? $messaggio : '—'),
    '',
    str_repeat('-', 46),
    'Inviato il ' . date('d/m/Y H:i'),
]);

// --- Invio ---
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = $cfg['smtp_host'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $cfg['smtp_user'];
    $mail->Password   = $cfg['smtp_pass'];
    $mail->SMTPSecure = (($cfg['smtp_secure'] ?? 'ssl') === 'tls')
        ? PHPMailer::ENCRYPTION_STARTTLS
        : PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = (int)($cfg['smtp_port'] ?? 465);
    $mail->CharSet    = 'UTF-8';

    // Il mittente DEVE essere la casella Aruba (per SPF/anti-spam), non la Gmail.
    $mail->setFrom($cfg['from_email'], $cfg['from_name'] ?? 'Sito Aurora');
    $mail->addAddress($cfg['to_email'], $cfg['to_name'] ?? '');
    $mail->addReplyTo($email, $nome); // rispondendo si risponde al cliente

    $mail->Subject = 'Richiesta dal sito — ' . $nome;
    $mail->Body    = $corpo;

    $mail->send();
    respond(true);
} catch (Exception $e) {
    error_log('Aurora form error: ' . $mail->ErrorInfo);
    respond(false, 'Invio non riuscito. Riprova o scrivimi su WhatsApp.', 500);
}
