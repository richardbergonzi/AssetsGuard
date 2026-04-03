<p align="center">
  <img src="https://img.shields.io/badge/Asset_Guard-Master_Edition-blueviolet?style=for-the-badge&logo=shield" alt="Asset Guard">
  <img src="https://img.shields.io/badge/Version-2.0-blue?style=for-the-badge" alt="Versão">
  <img src="https://img.shields.io/badge/PHP-8.1+-purple?style=for-the-badge&logo=php" alt="PHP">
  <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" alt="Licença">
</p>

<h1 align="center">🛡️ Asset Guard — Master Edition</h1>

<p align="center">
  <strong>Sistema avançado de proteção de ativos CSS e JavaScript para aplicações PHP.</strong><br>
  Ofuscação em tempo real, criptografia polimórfica e invisibilidade total no navegador.
</p>

<p align="center">
  <a href="#-instalação-rápida">Instalação</a> •
  <a href="#-camadas-de-proteção">Camadas</a> •
  <a href="#-como-funciona">Como Funciona</a> •
  <a href="#-uso-avançado">Uso Avançado</a> •
  <a href="#-logs-de-segurança">Logs</a>
</p>

---

## 🎯 O Problema

Quando você publica um site, todo o seu código CSS e JavaScript fica **exposto** no navegador. Qualquer pessoa pode:

- Abrir o **Inspecionar Elemento** e copiar seu design inteiro
- Acessar a aba **Sources** e baixar seus scripts
- Usar **Ctrl+S** e salvar uma cópia completa do site
- Usar bots e scrapers para roubar seu código automaticamente

**O Asset Guard resolve isso.** Ele transforma seus arquivos em dados criptografados que só existem na memória RAM do navegador por milissegundos, tornando a cópia e a engenharia reversa extremamente difíceis.

---

## 🔥 Camadas de Proteção

O Asset Guard utiliza **14 camadas independentes** de segurança que funcionam em conjunto:

### 🔐 Criptografia e Ofuscação
| Camada | O que faz |
|--------|-----------|
| **XOR Block Cipher** | Cada arquivo é criptografado com uma chave única derivada do nome do arquivo + segredo mestre |
| **Hex Obfuscation** | O código do carregador usa variáveis como `_0x1a` e strings como `\x63\x75\x72...` — ilegível para humanos |
| **Auto-Destruição** | Após descriptografar e injetar o código, o script se remove do HTML automaticamente |

### 👻 Invisibilidade
| Camada | O que faz |
|--------|-----------|
| **Ghost CSS** | O CSS é injetado via `adoptedStyleSheets` — não aparece na aba Elements do Inspecionar |
| **Stealth Delivery** | CSS é entregue como `application/javascript` — não aparece na aba Styles |
| **UTF-8 TextDecoder** | Decodificação nativa de acentos e caracteres especiais via API moderna do navegador |

### 🔒 Autenticação e Tokens
| Camada | O que faz |
|--------|-----------|
| **Token HMAC-SHA256** | Cada URL de recurso recebe uma assinatura criptográfica com expiração temporal |
| **Cookie Efêmero** | A chave de descriptografia é enviada via cookie que expira em 60 segundos |
| **Referer Check** | Bloqueia acessos diretos (colar a URL no navegador) — exige que o pedido venha do seu site |

### 🛑 Proteção Anti-Inspeção
| Camada | O que faz |
|--------|-----------|
| **Anti-DevTools** | Detecta se o Inspecionar está aberto (por tamanho da janela, `debugger` e getter traps) e limpa a página |
| **Bloqueio de Atalhos** | Intercepta F12, Ctrl+U, Ctrl+S, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+A e botão direito do mouse |
| **CSP Nonce** | Header `Content-Security-Policy` com nonce criptográfico — impede injeção de scripts externos (XSS) |
| **Prototype Freeze** | Congela `Object.prototype`, `Array.prototype` e `Function.prototype` contra tampering |

### 📊 Inteligência e Defesa
| Camada | O que faz |
|--------|-----------|
| **Guard Log** | Registra todas as tentativas bloqueadas com IP, User-Agent, motivo e horário |
| **Bomba de Lixo** | Envia 100MB de dados binários aleatórios para bots que tentam baixar os arquivos diretamente |
| **403 Personalizado** | Página de erro estilizada com identidade visual do projeto |

---

## 🧠 Como Funciona

```
Usuário acessa seu site
        │
        ▼
   index.php gera URLs assinadas (HMAC)
        │
        ▼
   Navegador pede o recurso → resource.php
        │
        ├── Verifica Token ✓
        ├── Verifica Referer ✓
        ├── Verifica Sessão ✓
        ├── Envia Cookie com chave (60s) ✓
        │
        ▼
   ResourceBoot.php criptografa o arquivo
        │
        ├── Base64 do conteúdo original
        ├── XOR com chave derivada
        ├── Empacota em loader ofuscado (hex)
        │
        ▼
   Navegador recebe o loader
        │
        ├── Lê o cookie efêmero (chave)
        ├── Remove ruído da chave
        ├── Descriptografa XOR → Base64 → UTF-8
        ├── JS: eval() do código limpo
        ├── CSS: adoptedStyleSheets (Ghost)
        └── Remove a própria tag <script> do DOM
```

---

## ⚡ Instalação Rápida

### 1. Copie a pasta
```bash
cp -r asset_guard_standalone/ /seu/projeto/
```

### 2. Crie o `.env` na raiz do servidor
```env
ASSET_GUARD_SECRET=minha_chave_secreta_muito_forte_aqui

# Habilitar/Desabilitar logs de tentativas bloqueadas (true | false)
ASSET_GUARD_LOG_ENABLED=true
```

### 3. Use no seu PHP
```php
<?php
require_once 'asset_guard_standalone/ResourceBoot.php';
require_once 'asset_guard_standalone/SecurityHelper.php';
require_once 'asset_guard_standalone/ShieldWall.php';

ResourceBoot::setResourceScript('asset_guard_standalone/resource.php');
if (session_status() === PHP_SESSION_NONE) session_start();

// IMPORTANTE: Enviar CSP Headers ANTES de qualquer HTML
SecurityHelper::sendCSPHeaders();
?>
<!DOCTYPE html>
<html>
<head>
    <!-- Escudo Anti-Inspeção -->
    <?php
        $nonce = SecurityHelper::getNonce();
        echo str_replace('<script>', "<script nonce=\"{$nonce}\">", ShieldWall::render());
    ?>

    <!-- CSS Protegido (Ghost Mode - Invisível no Elements) -->
    <?php echo SecurityHelper::css(ResourceBoot::url('caminho/para/style.css')); ?>

    <!-- JS Protegido (XOR + Hex Obfuscation) -->
    <?php echo SecurityHelper::js(ResourceBoot::url('caminho/para/script.js')); ?>
</head>
<body>
    <h1>Meu site protegido!</h1>
</body>
</html>
```

---

## 🔧 Uso Avançado

### Proteger múltiplos arquivos
```php
<!-- Vários CSS -->
<?php echo SecurityHelper::css(ResourceBoot::url('assets/css/reset.css')); ?>
<?php echo SecurityHelper::css(ResourceBoot::url('assets/css/main.css')); ?>
<?php echo SecurityHelper::css(ResourceBoot::url('assets/css/components.css')); ?>

<!-- Vários JS -->
<?php echo SecurityHelper::js(ResourceBoot::url('assets/js/app.js')); ?>
<?php echo SecurityHelper::js(ResourceBoot::url('assets/js/utils.js')); ?>
```

### Usar sem o ShieldWall (apenas criptografia)
Se você não quiser bloquear F12 e DevTools (ex: em ambiente de desenvolvimento):
```php
<?php
// Apenas criptografia + Ghost CSS, sem Anti-DevTools
SecurityHelper::sendCSPHeaders();
echo SecurityHelper::css(ResourceBoot::url('assets/css/style.css'));
echo SecurityHelper::js(ResourceBoot::url('assets/js/app.js'));
// NÃO incluir: ShieldWall::render()
?>
```

### Configurar caminho customizado do `.env`
```php
ResourceBoot::setEnvPath('/caminho/customizado/.env');
```

### Configurar diretório de logs
```php
GuardLog::setLogDir('/var/log/asset_guard/');
```

---

## 📊 Logs de Segurança

### Habilitar / Desabilitar
Os logs podem ser ativados ou desativados via `.env`:
```env
ASSET_GUARD_LOG_ENABLED=true   # Ativar logs
ASSET_GUARD_LOG_ENABLED=false  # Desativar logs
```

Quando habilitados, os logs são salvos automaticamente em arquivo (por padrão em `asset_guard_standalone/logs/` ou no diretório temporário do sistema como fallback).

> **⚠️ IMPORTANTE:** A página de visualização de logs (`examples/logs.php`) é fornecida **apenas para testes e desenvolvimento**. Em ambiente de produção, recomendamos implementar sua própria interface de monitoramento ou integrar com ferramentas profissionais (Grafana, ELK Stack, etc.).

> **💡 Extensibilidade:** O sistema de logs foi projetado com uma interface simples (`GuardLog::logBlock()`). Você pode facilmente substituir a implementação de arquivo por um banco de dados (MySQL, PostgreSQL, MongoDB) sobrescrevendo o método `logBlock()` ou criando uma classe filha. O formato dos dados permanece o mesmo.

### Ver estatísticas via PHP
```php
require_once 'asset_guard_standalone/GuardLog.php';

// Verificar se o log está habilitado
if (GuardLog::isEnabled()) {
    $stats = GuardLog::getStats();
    echo "Bloqueios hoje: " . $stats['today'] . "\n";
    print_r($stats['top_ips']);
}
```

### Formato do log (JSON por linha)
```json
{
  "time": "2025-01-15 14:32:10",
  "ip": "45.67.89.123",
  "ua": "Mozilla/5.0 (Windows NT 10.0; Win64; x64)...",
  "reason": "invalid_referer",
  "file": "assets/js/app.js",
  "referer": "none",
  "method": "GET",
  "uri": "/resource.php?file=assets/js/app.js&token=..."
}
```

### Motivos de bloqueio registrados
| Código | Significado |
|--------|-------------|
| `missing_params` | URL sem token ou parâmetros obrigatórios |
| `invalid_referer` | Acesso direto (sem referer do site) |
| `invalid_token` | Token expirado ou adulterado |
| `file_not_found` | Arquivo não existe no servidor |
| `extension_blocked` | Extensão de arquivo não permitida |
| `junk_bomb_triggered` | Bot tentou baixar o arquivo diretamente |

### Migrar para Banco de Dados
Para usar um banco de dados em vez de arquivo, crie uma classe que estenda ou substitua `GuardLog`:
```php
class GuardLogDB extends GuardLog
{
    public static function logBlock(string $reason, ?string $file = null): void
    {
        if (!self::isEnabled()) return;
        
        $pdo = new PDO('mysql:host=localhost;dbname=seu_banco', 'user', 'pass');
        $stmt = $pdo->prepare('INSERT INTO guard_logs (time, ip, ua, reason, file) VALUES (NOW(), ?, ?, ?, ?)');
        $stmt->execute([
            $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
            substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 200),
            $reason,
            $file
        ]);
    }
}
```

---

## 📁 Estrutura do Kit

```
asset_guard_standalone/
├── ResourceBoot.php      # Motor de criptografia XOR + Loader ofuscado
├── SecurityHelper.php     # Tags inteligentes + CSP Nonce dinâmico
├── ShieldWall.php         # Anti-DevTools + Bloqueio de Atalhos + Freeze
├── GuardLog.php           # Sistema de logs de tentativas bloqueadas
├── resource.php           # Gatekeeper (Porteiro de Segurança)
├── env_loader.php         # Carregador de variáveis .env
├── 403.php                # Página de erro personalizada
├── .env.example           # Template de configuração
├── README.md              # Documentação
├── logs/                  # Logs de bloqueios (criado automaticamente)
│   ├── .htaccess          # Proteção contra acesso web
│   └── blocked_YYYY-MM-DD.log
└── examples/
    ├── index.php           # Vitrine funcional com todas as camadas
    └── assets/
        ├── css/style.css   # CSS de exemplo (protegido)
        └── js/script.js    # JS de exemplo (protegido)
```

---

## ⚙️ Requisitos

| Requisito | Versão |
|-----------|--------|
| **PHP** | 8.1+ |
| **Extensões PHP** | `openssl`, `mbstring` |
| **Servidor** | Apache / Nginx / LiteSpeed |
| **Banco de Dados** | Nenhum |
| **Dependências externas** | Nenhuma |

---

## ⚠️ Observações Importantes

1. **Nenhuma proteção client-side é 100% inviolável.** O objetivo é tornar a engenharia reversa tão cara e demorada que não valha a pena para o atacante.

2. **O ShieldWall pode ser agressivo.** Em ambiente de desenvolvimento, considere desativá-lo para depurar seu código normalmente.

3. **A chave do `.env` é o coração do sistema.** Se alguém obtiver sua `ASSET_GUARD_SECRET`, toda a criptografia é comprometida. Proteja esse arquivo.

4. **Performance:** A criptografia em tempo real consome recursos do servidor. Para sites de altíssimo tráfego, considere implementar cache das respostas criptografadas.

---

## 📝 Licença

MIT — Use livremente em projetos pessoais e comerciais.
