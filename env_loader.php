<?php
/**
 * EnvLoader - Carregador leve de variáveis de ambiente (.env)
 * Versão Standalone - Parte do Kit Asset Guard
 */

class EnvLoader {
    private static $loaded = false;

    /**
     * Carregar o arquivo .env para $_ENV e putenv()
     * @param string $path Caminho completo para o arquivo .env
     */
    public static function load(string $path): bool {
        if (self::$loaded) return true;

        if (!file_exists($path)) {
            error_log("EnvLoader Error: .env file not found at " . $path);
            return false;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            
            // Ignorar comentários e linhas mal formatadas
            if (empty($line) || strpos($line, '#') === 0 || strpos($line, '=') === false) {
                continue;
            }

            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            // Remover aspas se existirem
            $value = trim($value, '"\'');

            $_ENV[$name] = $value;
            putenv("$name=$value");
        }

        self::$loaded = true;
        return true;
    }

    /**
     * Obter valor de ambiente com fallback
     */
    public static function get(string $key, $default = null) {
        return $_ENV[$key] ?? getenv($key) ?? $default;
    }
}
