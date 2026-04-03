<?php
/**
 * ResourceBoot - Motor de Proteção de Ativos em Tempo Real
 * Versão 1.7 (Deep Obfuscation - Fixed Hex)
 */

require_once __DIR__ . '/env_loader.php';

class ResourceBoot
{
    private static string $resource_script = '/resource.php';
    private static ?string $env_path = null;

    public static function setResourceScript(string $url): void { self::$resource_script = $url; }
    public static function setEnvPath(string $path): void { self::$env_path = $path; }

    public static function getKey(): string
    {
        if (!isset($_ENV['ASSET_GUARD_SECRET'])) {
            if (self::$env_path) {
                EnvLoader::load(self::$env_path);
            } else {
                $envPaths = [
                    __DIR__ . '/.env',
                    dirname(__DIR__) . '/.env',
                    dirname(__DIR__, 2) . '/.env'
                ];
                foreach ($envPaths as $path) {
                    if (file_exists($path)) {
                        EnvLoader::load($path);
                        break;
                    }
                }
            }
        }
        return $_ENV['ASSET_GUARD_SECRET'] ?? getenv('ASSET_GUARD_SECRET') ?? 'MASTER_KEY_STAY_SAFE';
    }

    public static function url(string $path): string
    {
        $baseDir = dirname(__DIR__, 2);
        $fullPath = realpath($baseDir . '/' . $path);
        if (!$fullPath || !file_exists($fullPath)) {
            $fullPath = realpath($_SERVER['DOCUMENT_ROOT'] . '/' . $path);
        }
        if (!$fullPath || !file_exists($fullPath)) return $path . '?v=error';

        $sessionId = (session_status() === PHP_SESSION_ACTIVE) ? session_id() : 'a';
        $expiry = time() + 3600;
        $mtime = filemtime($fullPath);
        $vHash = substr(hash('sha1', $mtime . self::getKey() . $sessionId), 0, 10);
        $msg = $path . '|' . $sessionId . '|' . $expiry;
        $sig = hash_hmac('sha256', $msg, self::getKey());

        return self::$resource_script . '?file=' . urlencode($path) . '&token=' . $sig . '&expires=' . $expiry . '&gv=' . $vHash;
    }

    public static function validate(string $path, string $token, int $expires): bool
    {
        if (time() > $expires) return false;
        $sessionId = (session_status() === PHP_SESSION_ACTIVE) ? session_id() : 'a';
        $msg = $path . '|' . $sessionId . '|' . $expires;
        $expected = hash_hmac('sha256', $msg, self::getKey());
        return hash_equals($expected, $token);
    }

    public static function processContent(string $content, string $ext, string $filepath): string
    {
        if ($ext !== 'js' && $ext !== 'css') return $content;

        // Limpeza e Minificação Básica antes de criptografar (Remove logs e comentários)
        $content = self::minifyContent($content, $ext);

        $filename = basename($filepath);
        $masterSecret = self::getKey();
        $seed = hash('sha256', $filename . $masterSecret);
        $rawKey = substr($seed, 0, 16);
        
        $payload = base64_encode($content);
        $finalB64 = self::xorBlockCipher($payload, $rawKey);

        return self::generateDeepLoader($finalB64, $ext);
    }

    private static function generateDeepLoader(string $payload, string $type): string
    {
        $mode = ($type === 'js') ? 'eval' : 'ghost';
        
        // Loader com Escape Duplo e Variáveis Mangled
        // Note: Usamos \\x para que o PHP não traduza o hex antes de enviar ao navegador
        return "(function(_0x1,_0x2){
            try {
                let _0x3=(10*2+5),_0x4=(_0x3-5);
                var _0x5 = (_0x2['\\x63\\x75\\x72\\x72\\x65\\x6e\\x74\\x53\\x63\\x72\\x69\\x70\\x74']||{})['\\x73\\x72\\x63'];
                var _0x6 = (_0x5['\\x6d\\x61\\x74\\x63\\x68'](/[&?]gv=([^&]+)/)||[])[1];
                var _0x7 = _0x2['\\x63\\x6f\\x6f\\x6b\\x69\\x65']['\\x6d\\x61\\x74\\x63\\x68'](new RegExp('_ak_'+_0x6+'=([^;]+)'));
                var _0x8 = (_0x7?_0x7[1]:'')['\\x73\\x75\\x62\x73\\x74\\x72'](4);
                _0x8 = _0x8['\\x73\\x75\\x62\\x73\\x74\\x72'](0,_0x8['\\x6c\\x65\\x6e\\x67\\x74\\x68']-4)['\\x73\\x70\\x6c\\x69\\x74']('')['\x72\x65\x76\x65\x72\x73\x65']()['\x6a\x6f\x69\x6e']('');
                var _0x9 = '';
                for(var _0xa=0;_0xa<_0x8['\\x6c\\x65\\x6e\\x67\\x74\\x68'];_0xa+=2) _0x9 += String['\\x66\\x72\\x6f\x6d\\x43\\x68\\x61\x72\\x43\\x6f\\x64\\x65'](parseInt(_0x8['\\x73\\x75\\x62\\x73\\x74\\x72'](_0xa,2), 16));
                var _0xb = _0x1['\\x61\\x74\\x6f\\x62']('$payload');
                var _0xc = '';
                for(var _0xd=0;_0xd<_0xb['\\x6c\\x65\x6e\\x67\\x74\\x68'];_0xd++) _0xc += String['\\x66\\x72\x6f\\x6d\\x43\\x68\\x61\\x72\x43\\x6f\\x64\\x65'](_0xb['\\x63\\x68\\x61\\x72\\x43\\x6f\\x64\\x65\\x41\\x74'](_0xd) ^ _0x9['\\x63\\x68\x61\\x72\\x43\\x6f\\x64\\x65\\x41\\x74'](_0xd % _0x9['\\x6c\\x65\\x6e\\x67\\x74\x68']));
                _0xc = _0x1['\\x61\\x74\\x6f\\x62'](_0xc);
                const _0xe = new Uint8Array(_0xc['\\x6c\\x65\\x6e\\x67\\x74\\x68']);
                for(var _0xf=0;_0xf<_0xc['\\x6c\\x65\x6e\\x67\\x74\\x68'];_0xf++) _0xe[_0xf]=_0xc['\\x63\\x68\\x61\\x72\\x43\\x6f\\x64\\x65\\x41\\x74'](_0xf);
                var _0x10 = new TextDecoder()['\\x64\\x65\x63\\x6f\\x64\\x65'](_0xe);
                if('$mode' === 'eval') { (1,_0x1['\\x65\\x76\\x61\\x6c'])(_0x10); }
                else {
                    if (_0x1['\\x43\\x53\\x53\\x53\\x74\\x79\\x6c\x65\\x53\x68\x65\\x65\\x74'] && _0x2['\\x61\\x64\\x6f\\x70\x74\\x65\\x64\\x53\\x74\x79\\x6c\\x65\\x53\x68\\x65\\x65\\x74\\x73']) {
                        const _0x11 = new CSSStyleSheet();
                        _0x11['\\x72\x65\\x70\\x6c\\x61\\x63\\x65\\x53\\x79\x6e\\x63'](_0x10);
                        _0x2['\\x61\\x64\\x6f\\x70\\x74\\x65\\x64\\x53\\x74\x79\\x6c\\x65\\x53\x68\\x65\\x65\\x74\\x73'] = [..._0x2['\\x61\\x64\\x6f\\x70\\x74\x65\\x64\\x53\\x74\x79\\x6c\\x65\\x53\x68\x65\\x65\\x74\\x73'], _0x11];
                    } else {
                        var _0x12 = _0x2['\\x63\\x72\\x65\\x61\\x74\\x65\\x45\\x6c\x65\\x6d\x65\\x6e\x74']('\\x73\\x74\\x79\\x6c\\x65');
                        _0x12['\\x74\\x65\\x78\\x74\\x43\x6f\\x6e\\x74\\x65\\x6e\x74'] = _0x10;
                        (_0x2['\\x68\\x65\\x61\\x64']||_0x2['\\x64\\x6f\x63\\x75\\x6d\x65\\x6e\x74\\x45\x6c\\x65\x6d\\x65\x6e\x74'])['\\x61\x70\\x70\\x65\x6e\x64\\x43\x68\\x69\\x6c\\x64'](_0x12);
                    }
                }
                if(_0x2['\\x63\\x75\\x72\\x72\\x65\\x6e\\x74\\x53\x63\x72\\x69\\x70\\x74']) _0x2['\\x63\\x75\x72\\x72\\x65\\x6e\\x74\\x53\x63\\x72\\x69\\x70\\x74']['\\x72\\x65\\x6d\x6f\\x76\\x65']();
            } catch(e) {}
        })(window,document);";
    }

    private static function xorBlockCipher(string $data, string $key): string
    {
        $dataLen = strlen($data);
        $keyLen = strlen($key);
        $output = $data;
        for ($i = 0; $i < $dataLen; $i++) {
            $output[$i] = $data[$i] ^ $key[$i % $keyLen];
        }
        return base64_encode($output);
    }

    /**
     * Remove comentários, console.logs e compacta espaços do código original.
     * Assim, se alguém extrair da memória, não verá informações vitais.
     */
    private static function minifyContent(string $content, string $ext): string
    {
        if ($ext === 'js') {
            // Remove console.log(), console.info(), console.warn(), etc.
            $content = preg_replace('/console\.(log|info|warn|error|trace|debug|dir)\s*\([^;]+;?/s', '', $content);
            // Remove comentários de bloco /* ... */
            $content = preg_replace('!/\*.*?\*/!s', '', $content);
            // Remove comentários de linha // ...
            $content = preg_replace('/^\s*\/\/.*$/m', '', $content);
            // Remove múltiplas quebras de linha
            $content = preg_replace("/\n\s*\n/", "\n", $content);
        } elseif ($ext === 'css') {
            // Remove comentários /* ... */ do CSS
            $content = preg_replace('!/\*.*?\*/!s', '', $content);
            // Compacta múltiplas linhas e espaços vazios
            $content = preg_replace("/\s{2,}/", " ", $content);
            $content = str_replace(["\n", "\r", "\t"], "", $content);
        }

        return trim($content);
    }
}
