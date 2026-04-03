<?php
/**
 * SecurityHelper - Utilitários de Integridade (Master Edition)
 * Versão 2.0
 * 
 * Inclui:
 * - Geração de tags CSS/JS protegidas
 * - CSP Nonce dinâmico por requisição
 * - Validação de integridade SRI
 */

class SecurityHelper
{
    private static $ALLOWED_EXTENSIONS = [
        'css' => 'application/javascript',
        'js'  => 'application/javascript',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg'=> 'image/jpeg',
        'gif' => 'image/gif',
        'webp'=> 'image/webp',
        'svg' => 'image/svg+xml',
        'woff2'=> 'font/woff2',
        'woff' => 'font/woff',
        'ttf'  => 'font/ttf',
        'ico'  => 'image/x-icon',
    ];

    private static ?string $nonce = null;

    /**
     * Gera ou retorna o Nonce CSP da requisição atual.
     * O nonce é único por página carregada.
     */
    public static function getNonce(): string
    {
        if (self::$nonce === null) {
            self::$nonce = base64_encode(random_bytes(16));
        }
        return self::$nonce;
    }

    /**
     * Envia o header Content-Security-Policy com o nonce atual.
     * Deve ser chamado ANTES de qualquer output HTML.
     */
    public static function sendCSPHeaders(): void
    {
        $nonce = self::getNonce();
        header("Content-Security-Policy: script-src 'nonce-{$nonce}' 'strict-dynamic' 'unsafe-eval'; style-src 'unsafe-inline' 'self'; object-src 'none'; base-uri 'self';");
    }

    public static function isAllowed(string $ext): bool {
        return isset(self::$ALLOWED_EXTENSIONS[strtolower(trim($ext))]);
    }

    public static function getMimeType(string $ext): string {
        return self::$ALLOWED_EXTENSIONS[strtolower(trim($ext))] ?? 'application/octet-stream';
    }

    /**
     * 🌑 HELPER STEALTH PARA CSS 🌑
     * Gera tag <script> com nonce CSP para carregar CSS criptografado.
     */
    public static function css(string $url): string {
        $nonce = self::getNonce();
        return "<script nonce=\"{$nonce}\" src=\"{$url}\" id=\"asg-s\"></script>";
    }

    /**
     * HELPER PARA JS com nonce CSP
     */
    public static function js(string $url): string {
        $nonce = self::getNonce();
        return "<script nonce=\"{$nonce}\" src=\"{$url}\" id=\"asg-j\" defer></script>";
    }

    public static function getIntegrity(string $content): string {
        $hash = base64_encode(hash('sha384', $content, true));
        return "sha384-" . $hash;
    }
}
