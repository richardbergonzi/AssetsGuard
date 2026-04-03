<?php
/**
 * ShieldWall - Camada de Proteção Anti-Inspeção
 * Versão 1.0
 * 
 * Gera código JavaScript de proteção que:
 * 1. Detecta e bloqueia DevTools (Anti-Debugging)
 * 2. Bloqueia atalhos de teclado (F12, Ctrl+U, Ctrl+S, etc.)
 * 3. Bloqueia botão direito do mouse
 * 4. Congela protótipos nativos contra tampering
 */

class ShieldWall
{
    /**
     * Gera o script de proteção completo (ofuscado)
     */
    public static function render(): string
    {
        $script = self::generateShieldScript();
        return "<script>{$script}</script>";
    }

    private static function generateShieldScript(): string
    {
        // Todo o código é gerado com hex-encoding para dificultar leitura
        return "(function(_w,_d){
            
            /* ===== CAMADA 1: ANTI-DEVTOOLS ===== */
            var _dt = false;
            var _th = 160;
            
            // Detecção por tamanho da janela (DevTools dockado)
            var _ck = function(){
                var _wT = _w['\\x6f\\x75\\x74\\x65\\x72\\x57\\x69\\x64\\x74\\x68'] - _w['\\x69\\x6e\\x6e\\x65\\x72\\x57\\x69\\x64\\x74\\x68'] > _th;
                var _hT = _w['\\x6f\\x75\\x74\\x65\\x72\\x48\\x65\\x69\\x67\\x68\\x74'] - _w['\\x69\\x6e\\x6e\\x65\\x72\\x48\\x65\\x69\\x67\\x68\\x74'] > _th;
                if(_wT || _hT) { _onDetect(); }
            };
            
            // Detecção por tempo de execução do debugger
            var _dbg = function(){
                var _s = new Date();
                (function(){}['\\x63\\x6f\\x6e\\x73\\x74\\x72\\x75\\x63\\x74\\x6f\\x72']('\\x64\\x65\\x62\\x75\\x67\\x67\\x65\\x72'))();
                var _e = new Date();
                if(_e - _s > 100) { _onDetect(); }
            };
            
            // Detecção por getter trap (console.log aciona getters no DevTools)
            var _gt = function(){
                var _el = new Image();
                Object['\\x64\\x65\\x66\\x69\\x6e\\x65\\x50\\x72\\x6f\\x70\\x65\\x72\\x74\\x79'](_el, '\\x69\\x64', {
                    get: function(){ _dt = true; _onDetect(); }
                });
                _w['\\x63\\x6f\\x6e\\x73\\x6f\\x6c\\x65']['\\x6c\\x6f\\x67']('%c', _el);
            };
            
            // Ação quando DevTools é detectado
            var _onDetect = function(){
                if(_dt) return;
                _dt = true;
                try {
                    _d['\\x62\\x6f\\x64\\x79']['\\x69\\x6e\\x6e\\x65\\x72\\x48\\x54\\x4d\\x4c'] = '';
                    _d['\\x68\\x65\\x61\\x64']['\\x69\\x6e\\x6e\\x65\\x72\\x48\\x54\\x4d\\x4c'] = '';
                    _w['\\x6c\\x6f\\x63\\x61\\x74\\x69\\x6f\\x6e']['\\x72\\x65\\x70\\x6c\\x61\\x63\\x65']('about:blank');
                } catch(e){}
            };
            
            // Loop de monitoramento (a cada 2 segundos)
            _w['\\x73\\x65\\x74\\x49\\x6e\\x74\\x65\\x72\\x76\\x61\\x6c'](function(){
                _ck();
                _dbg();
            }, 2000);
            
            // Getter trap inicial (executa uma vez)
            try { _gt(); } catch(e){}
            
            /* ===== CAMADA 2: BLOQUEIO DE ATALHOS ===== */
            _d['\\x61\\x64\\x64\\x45\\x76\\x65\\x6e\\x74\\x4c\\x69\\x73\\x74\\x65\\x6e\\x65\\x72']('\\x6b\\x65\\x79\\x64\\x6f\\x77\\x6e', function(e){
                // F12
                if(e['\\x6b\\x65\\x79\\x43\\x6f\\x64\\x65'] === 123) { e['\\x70\\x72\\x65\\x76\\x65\\x6e\\x74\\x44\\x65\\x66\\x61\\x75\\x6c\\x74'](); return false; }
                // Ctrl+Shift+I (DevTools)
                if(e['\\x63\\x74\\x72\\x6c\\x4b\\x65\\x79'] && e['\\x73\\x68\\x69\\x66\\x74\\x4b\\x65\\x79'] && e['\\x6b\\x65\\x79\\x43\\x6f\\x64\\x65'] === 73) { e['\\x70\\x72\\x65\\x76\\x65\\x6e\\x74\\x44\\x65\\x66\\x61\\x75\\x6c\\x74'](); return false; }
                // Ctrl+Shift+J (Console)
                if(e['\\x63\\x74\\x72\\x6c\\x4b\\x65\\x79'] && e['\\x73\\x68\\x69\\x66\\x74\\x4b\\x65\\x79'] && e['\\x6b\\x65\\x79\\x43\\x6f\\x64\\x65'] === 74) { e['\\x70\\x72\\x65\\x76\\x65\\x6e\\x74\\x44\\x65\\x66\\x61\\x75\\x6c\\x74'](); return false; }
                // Ctrl+Shift+C (Element Picker)
                if(e['\\x63\\x74\\x72\\x6c\\x4b\\x65\\x79'] && e['\\x73\\x68\\x69\\x66\\x74\\x4b\\x65\\x79'] && e['\\x6b\\x65\\x79\\x43\\x6f\\x64\\x65'] === 67) { e['\\x70\\x72\\x65\\x76\\x65\\x6e\\x74\\x44\\x65\\x66\\x61\\x75\\x6c\\x74'](); return false; }
                // Ctrl+U (View Source)
                if(e['\\x63\\x74\\x72\\x6c\\x4b\\x65\\x79'] && e['\\x6b\\x65\\x79\\x43\\x6f\\x64\\x65'] === 85) { e['\\x70\\x72\\x65\\x76\\x65\\x6e\\x74\\x44\\x65\\x66\\x61\\x75\\x6c\\x74'](); return false; }
                // Ctrl+S (Save Page)
                if(e['\\x63\\x74\\x72\\x6c\\x4b\\x65\\x79'] && e['\\x6b\\x65\\x79\\x43\\x6f\\x64\\x65'] === 83) { e['\\x70\\x72\\x65\\x76\\x65\\x6e\\x74\\x44\\x65\\x66\\x61\\x75\\x6c\\x74'](); return false; }
                // Ctrl+A (Select All)  
                if(e['\\x63\\x74\\x72\\x6c\\x4b\\x65\\x79'] && e['\\x6b\\x65\\x79\\x43\\x6f\\x64\\x65'] === 65) { e['\\x70\\x72\\x65\\x76\\x65\\x6e\\x74\\x44\\x65\\x66\\x61\\x75\\x6c\\x74'](); return false; }
            });
            
            /* ===== CAMADA 3: BLOQUEIO DO BOTÃO DIREITO ===== */
            _d['\\x61\\x64\\x64\\x45\\x76\\x65\\x6e\\x74\\x4c\\x69\\x73\\x74\\x65\\x6e\\x65\\x72']('\\x63\\x6f\\x6e\\x74\\x65\\x78\\x74\\x6d\\x65\\x6e\\x75', function(e){
                e['\\x70\\x72\\x65\\x76\\x65\\x6e\\x74\\x44\\x65\\x66\\x61\\x75\\x6c\\x74']();
                return false;
            });
            
            /* ===== CAMADA 4: CONGELAMENTO DE PROTÓTIPOS ===== */
            try {
                Object['\\x66\\x72\\x65\\x65\\x7a\\x65'](Object['\\x70\\x72\\x6f\\x74\\x6f\\x74\\x79\\x70\\x65']);
                Object['\\x66\\x72\\x65\\x65\\x7a\\x65'](Array['\\x70\\x72\\x6f\\x74\\x6f\\x74\\x79\\x70\\x65']);
                Object['\\x66\\x72\\x65\\x65\\x7a\\x65'](Function['\\x70\\x72\\x6f\\x74\\x6f\\x74\\x79\\x70\\x65']);
            } catch(e){}
            
        })(window,document);";
    }
}
