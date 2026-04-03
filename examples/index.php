<?php
/**
 * Página de Demonstração - Asset Guard Master Edition (V2.0)
 * 
 * Camadas Ativas:
 * 1. CSS Ghost (adoptedStyleSheets - invisível no Elements)
 * 2. JS Ofuscado (XOR + Hex Encoding)
 * 3. Anti-DevTools (debugger + detecção de tamanho)
 * 4. Bloqueio de Atalhos (F12, Ctrl+U, Ctrl+S, botão direito)
 * 5. CSP Nonce (Content-Security-Policy)
 * 6. Congelamento de Protótipos (Object.freeze)
 * 7. Logging de Tentativas (GuardLog)
 */

require_once __DIR__ . '/../ResourceBoot.php';
require_once __DIR__ . '/../SecurityHelper.php';
require_once __DIR__ . '/../ShieldWall.php';

// Configurações
ResourceBoot::setResourceScript('../resource.php');

if (session_status() === PHP_SESSION_NONE) session_start();

// 🔒 Enviar CSP Headers ANTES de qualquer output
SecurityHelper::sendCSPHeaders();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo Master — Asset Guard 2.0</title>

    <!-- 🛡️ ESCUDO: Anti-DevTools + Bloqueio de Atalhos + Prototype Freeze -->
    <?php
        $nonce = SecurityHelper::getNonce();
        // Injetar o ShieldWall com nonce CSP
        $shieldCode = ShieldWall::render();
        // Substituir <script> por <script nonce="...">
        $shieldCode = str_replace('<script>', "<script nonce=\"{$nonce}\">", $shieldCode);
        echo $shieldCode;
    ?>

    <!-- 🌑 CSS Ghost (Invisível no Elements) -->
    <?php echo SecurityHelper::css(ResourceBoot::url('asset_guard_standalone/examples/assets/css/style.css')); ?>

    <!-- 🔒 JS Ofuscado com Proteção XOR Polimórfica -->
    <?php echo SecurityHelper::js(ResourceBoot::url('asset_guard_standalone/examples/assets/js/script.js')); ?>
</head>
<body>
    <div class="card">
        <h1>🛡️ Asset Guard - Master Edition</h1>
        <p>
            <b>Proteção Total Ativa.</b> Este site possui 7 camadas de segurança 
            contra inspeção, cópia e engenharia reversa.
        </p>

        <div id="status">Verificando Segurança...</div>

        <div class="footer-area">
            <div class="shield-badges">
                <span class="badge">🌑 Ghost CSS</span>
                <span class="badge">💀 XOR Cipher</span>
                <span class="badge">🛑 Anti-DevTools</span>
                <span class="badge">🚫 Hotkeys Block</span>
                <span class="badge">🔒 CSP Nonce</span>
                <span class="badge">🧊 Frozen Proto</span>
                <span class="badge">📊 Guard Log</span>
            </div>
            <p class="helper-text">
                Tente usar F12, Ctrl+U, Ctrl+S ou o botão direito do mouse. 
                Todos estão bloqueados por camadas de proteção.
            </p>
            <button onclick="window.assetGuardTest()" class="btn-stealth">
                Executar Código Invisível
            </button>
        </div>
    </div>
</body>
</html>
