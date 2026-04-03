<?php
/**
 * GuardLog - Sistema de Logging e Honeypot
 * Versão 1.1 (com Fallback e Diagnóstico)
 */

class GuardLog
{
    private static string $logDir = '';

    public static function setLogDir(string $dir): void
    {
        self::$logDir = rtrim($dir, '/');
    }

    private static function getLogDir(): string
    {
        if (empty(self::$logDir)) {
            // Tenta o diretório padrão
            $default = __DIR__ . '/logs';
            if (self::ensureDir($default)) {
                self::$logDir = $default;
            } else {
                // Fallback: usa o diretório de temp do sistema
                $tmp = sys_get_temp_dir() . '/asset_guard_logs';
                self::ensureDir($tmp);
                self::$logDir = $tmp;
            }
        }
        return self::$logDir;
    }

    private static function ensureDir(string $dir): bool
    {
        if (!is_dir($dir)) {
            $created = @mkdir($dir, 0775, true);
            if (!$created)
                return false;
        }
        // Teste real de escrita (is_writable pode mentir em alguns servidores)
        $testFile = $dir . '/.write_test_' . getmypid();
        $result = @file_put_contents($testFile, '1');
        if ($result !== false) {
            @unlink($testFile);
            return true;
        }
        return false;
    }


    /**
     * Verifica se o log está habilitado via .env
     */
    public static function isEnabled(): bool
    {
        $val = $_ENV['ASSET_GUARD_LOG_ENABLED'] ?? getenv('ASSET_GUARD_LOG_ENABLED') ?: 'true';
        return strtolower(trim($val)) !== 'false';
    }

    /**
     * Registra uma tentativa de acesso bloqueado.
     */
    public static function logBlock(string $reason, ?string $file = null): void
    {
        // Toggle via .env: ASSET_GUARD_LOG_ENABLED=false desativa os logs
        if (!self::isEnabled()) return;

        try {
            $dir = self::getLogDir();

            // Criar .htaccess se não existir
            $htaccess = $dir . '/.htaccess';
            if (!file_exists($htaccess)) {
                @file_put_contents($htaccess, "Deny from all\n");
            }

            $logFile = $dir . '/blocked_' . date('Y-m-d') . '.log';

            $entry = [
                'time' => date('Y-m-d H:i:s'),
                'ip' => self::getClientIP(),
                'ua' => substr($_SERVER['HTTP_USER_AGENT'] ?? 'unknown', 0, 200),
                'reason' => $reason,
                'file' => $file ?? 'N/A',
                'referer' => substr($_SERVER['HTTP_REFERER'] ?? 'none', 0, 200),
                'method' => $_SERVER['REQUEST_METHOD'] ?? 'GET',
                'uri' => substr($_SERVER['REQUEST_URI'] ?? '', 0, 500),
            ];

            $line = json_encode($entry, JSON_UNESCAPED_UNICODE) . "\n";
            @file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
        } catch (\Throwable $e) {
            // Silêncio absoluto — log nunca deve quebrar o site
        }
    }

    /**
     * Retorna estatísticas resumidas dos logs.
     */
    public static function getStats(): array
    {
        $dir = self::getLogDir();
        $todayFile = $dir . '/blocked_' . date('Y-m-d') . '.log';

        if (!file_exists($todayFile)) {
            return ['today' => 0, 'top_ips' => [], 'top_reasons' => []];
        }

        $lines = file($todayFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $ips = [];
        $reasons = [];

        foreach ($lines as $line) {
            $entry = json_decode($line, true);
            if (!$entry)
                continue;

            $ip = $entry['ip'] ?? 'unknown';
            $reason = $entry['reason'] ?? 'unknown';
            $ips[$ip] = ($ips[$ip] ?? 0) + 1;
            $reasons[$reason] = ($reasons[$reason] ?? 0) + 1;
        }

        arsort($ips);
        arsort($reasons);

        return [
            'today' => count($lines),
            'top_ips' => array_slice($ips, 0, 10, true),
            'top_reasons' => array_slice($reasons, 0, 5, true),
        ];
    }

    /**
     * Diagnóstico: verifica se o sistema de logs está funcionando.
     */
    public static function diagnose(): array
    {
        $dir = self::getLogDir();
        $testFile = $dir . '/_test_write.tmp';
        $canWrite = @file_put_contents($testFile, 'test');
        if ($canWrite !== false)
            @unlink($testFile);

        return [
            'log_dir' => $dir,
            'dir_exists' => is_dir($dir),
            'dir_writable' => is_writable($dir),
            'write_test' => $canWrite !== false,
            'php_user' => function_exists('posix_getpwuid') ? posix_getpwuid(posix_geteuid())['name'] ?? 'N/A' : get_current_user(),
            'tmp_dir' => sys_get_temp_dir(),
        ];
    }

    private static function getClientIP(): string
    {
        $headers = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'REMOTE_ADDR',
        ];

        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ip = $_SERVER[$header];
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}
