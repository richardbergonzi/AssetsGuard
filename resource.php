<?php
/**
 * Resource Gatekeeper - O "Filtro" de Segurança (Master Edition v2.0)
 * 
 * Integra: Token HMAC, Referer Check, Bomba de Lixo, Cookie Handshake,
 *          Logging de Bloqueios (GuardLog), Página 403 Personalizada.
 */

require_once __DIR__ . '/env_loader.php';
require_once __DIR__ . '/ResourceBoot.php';
require_once __DIR__ . '/SecurityHelper.php';
require_once __DIR__ . '/GuardLog.php';

error_reporting(0);
ini_set('display_errors', 0);

$envPaths = [
    __DIR__ . '/.env',                           // Local (standalone)
    dirname(__DIR__) . '/.env',                  // public_html
    dirname(__DIR__, 2) . '/.env'                // root
];

foreach ($envPaths as $path) {
    if (file_exists($path)) {
        EnvLoader::load($path);
        break;
    }
}

$file = filter_input(INPUT_GET, 'file', FILTER_SANITIZE_SPECIAL_CHARS);
$token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_SPECIAL_CHARS);
$expires = (int) filter_input(INPUT_GET, 'expires', FILTER_SANITIZE_NUMBER_INT);
$gv = filter_input(INPUT_GET, 'gv', FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * Função para renderizar as páginas de erro bonitas + log
 */
function renderError($code, $reason = 'unknown', $file = null) {
    // IMPORTANTE: Gravar o log ANTES de enviar qualquer header
    // (o servidor pode interceptar o 403 e matar o processo)
    GuardLog::logBlock($reason, $file);

    if ($code == 404) {
        header("HTTP/1.1 404 Not Found");
        echo "404 Not Found";
    } else {
        // Enviar o conteúdo da página 403 ANTES do header
        // para garantir que o output chega ao cliente
        $errorPage = __DIR__ . '/403.php';
        if (file_exists($errorPage)) {
            http_response_code(403);
            include $errorPage;
        } else {
            header("HTTP/1.1 403 Forbidden");
            echo "403 Forbidden";
        }
    }
    exit;
}


if (!$file || !$token || !$expires) {
    renderError(403, 'missing_params');
}

if (session_status() === PHP_SESSION_NONE) session_start();

$referer = $_SERVER['HTTP_REFERER'] ?? '';
$host = $_SERVER['HTTP_HOST'] ?? '';
$refererHost = parse_url($referer, PHP_URL_HOST);

if (empty($referer) || $refererHost !== $host) {
    renderError(403, 'invalid_referer', $file);
}

if (!ResourceBoot::validate($file, $token, $expires)) {
    renderError(403, 'invalid_token', $file);
}

$basePath = realpath(__DIR__ . '/../');
$realPath = realpath($basePath . '/' . $file);

if (!$realPath || strpos($realPath, $basePath) !== 0 || !file_exists($realPath)) {
    renderError(404, 'file_not_found', $file);
}

$ext = strtolower(pathinfo($realPath, PATHINFO_EXTENSION));

if (!SecurityHelper::isAllowed($ext)) {
    renderError(403, 'extension_blocked', $file);
}

// 🌑 Bomba de Lixo (Anti-Save / Anti-Bot)
$dest = $_SERVER['HTTP_SEC_FETCH_DEST'] ?? '';
if (($ext === 'js' || $ext === 'css') && ($dest === 'document' || empty($dest))) {
    GuardLog::logBlock('junk_bomb_triggered', $file);
    header("Content-Type: application/octet-stream");
    header("Content-Length: 104857600");
    for($i=0; $i<100; $i++) {
        echo str_repeat(md5(microtime()), 32768);
        flush();
    }
    exit;
}

// Cookie Handshake
if (($ext === 'js' || $ext === 'css') && $gv) {
    $masterSecret = ResourceBoot::getKey();
    $seed = hash('sha256', basename($realPath) . $masterSecret);
    $rawKey = substr($seed, 0, 16);
    $scrambled = strrev(bin2hex($rawKey));
    $noiseStart = substr(hash('crc32', uniqid()), 0, 4);
    $noiseEnd = substr(hash('crc32', microtime()), 0, 4);
    $protectedKey = $noiseStart . $scrambled . $noiseEnd;

    setcookie("_ak_" . $gv, $protectedKey, [
        'expires' => time() + 60,
        'path' => '/',
        'httponly' => false,
        'samesite' => 'Strict',
        'secure' => true 
    ]);
}

$content = file_get_contents($realPath);
$processed = ResourceBoot::processContent($content, $ext, $realPath);

$mime = ($ext === 'css') ? 'application/javascript' : (SecurityHelper::getMimeType($ext));

$etag = md5($processed);
header("Content-Type: $mime; charset=utf-8");
header("Etag: $etag");
header("Cache-Control: private, max-age=3600");

if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) == $etag) {
    header("HTTP/1.1 304 Not Modified");
    exit;
}

echo $processed;
