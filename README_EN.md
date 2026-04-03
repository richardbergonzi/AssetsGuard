<p align="center">
  <img src="https://img.shields.io/badge/Asset_Guard-Master_Edition-blueviolet?style=for-the-badge&logo=shield" alt="Asset Guard">
  <img src="https://img.shields.io/badge/Version-2.0-blue?style=for-the-badge" alt="Version">
  <img src="https://img.shields.io/badge/PHP-8.1+-purple?style=for-the-badge&logo=php" alt="PHP">
  <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" alt="License">
</p>

<h1 align="center">🛡️ Asset Guard — Master Edition</h1>

<p align="center">
  <strong>Advanced CSS and JavaScript asset protection system for PHP applications.</strong><br>
  Real-time obfuscation, polymorphic encryption, and complete browser invisibility.
</p>

<p align="center">
  <a href="#-quick-installation">Installation</a> •
  <a href="#-protection-layers">Layers</a> •
  <a href="#-how-it-works">How It Works</a> •
  <a href="#-advanced-usage">Advanced Usage</a> •
  <a href="#-security-logs">Logs</a>
</p>

---

## 🎯 The Problem

When you publish a website, all your CSS and JavaScript code is **exposed** in the browser. Anyone can:

- Open **Inspect Element** and copy your entire design
- Access the **Sources** tab and download your proprietary scripts
- Press **Ctrl+S** and save a complete replica of your site
- Use bots and scrapers to steal your code automatically

**Asset Guard solves this.** It converts your files into encrypted data payloads that only exist inside the browser's RAM for milliseconds, making copying and reverse engineering extremely difficult.

---

## 🔥 Protection Layers

Asset Guard employs **14 independent security layers** working in unison:

### 🔐 Encryption and Obfuscation
| Layer | What it does |
|--------|-----------|
| **XOR Block Cipher** | Every file is encrypted using a unique key derived from the filename + a master secret |
| **Hex Obfuscation** | The payload loader uses mangled variables like `_0x1a` and strings like `\x63\x75\x72...` — completely unreadable to humans |
| **Self-Destruction** | Once decrypted and injected, the code automatically removes its own script tag from the HTML |

### 👻 Invisibility
| Layer | What it does |
|--------|-----------|
| **Ghost CSS** | CSS is injected natively via `adoptedStyleSheets` — it does not show up in the Elements tab |
| **Stealth Delivery** | CSS is delivered with `application/javascript` headers — bypassing the Styles tab detectors |
| **UTF-8 TextDecoder** | Flawless native decoding of accents and special characters through modern browser APIs |

### 🔒 Authentication and Tokens
| Layer | What it does |
|--------|-----------|
| **HMAC-SHA256 Token** | Each resource URL gets a cryptographic signature with time-based expiration |
| **Ephemeral Cookie** | The decryption key is sent via a volatile cookie that expires in 60 seconds |
| **Referer Check** | Blocks direct access (pasting the URL directly to the browser), requiring requests originating from your own site |

### 🛑 Anti-Inspection Defense
| Layer | What it does |
|--------|-----------|
| **Anti-DevTools** | Detects if Inspect Element is open (via window resizing, `debugger` loops, and getter traps) and clears the page |
| **Shortcut Blocking** | Intercepts keys like F12, Ctrl+U, Ctrl+S, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+A, and Right-Click |
| **CSP Nonce** | Dynamically issues `Content-Security-Policy` with cryptographic nonces locking out external script injections (XSS) |
| **Prototype Freeze** | Freezes `Object.prototype`, `Array.prototype`, and `Function.prototype` stopping tampering attempts |

### 📊 Intelligence and Defense
| Layer | What it does |
|--------|-----------|
| **Guard Log** | Registers all blocked attempts tracking IP, User-Agent, reason, and timestamp |
| **Junk Bomb** | Drops 100MB of random binary garbage directly onto bots and scrapers trying a brute-force download |
| **Custom 403** | Stylish and discrete "Access Denied" page |

---

## 🧠 How It Works

```
User accesses your website
        │
        ▼
   index.php generates signed URLs (HMAC)
        │
        ▼
   Browser requests resource → resource.php
        │
        ├── Checks Token ✓
        ├── Checks Referer ✓
        ├── Checks Session ✓
        ├── Issues Key Cookie (60s) ✓
        │
        ▼
   ResourceBoot.php encrypts the file
        │
        ├── Auto-Minifies Code (Removes logs/comments)
        ├── Wraps Base64 of original content
        ├── XOR encryption using derived key
        ├── Packs into obfuscated (hex) loader
        │
        ▼
   Browser receives the loader
        │
        ├── Reads ephemeral cookie (unlock key)
        ├── Cleans key noise
        ├── Decrypts XOR → Base64 → UTF-8
        ├── JS: native eval() of clean code
        ├── CSS: adoptedStyleSheets (Ghost layout)
        └── Self-removes the <script> tag from DOM
```

---

## ⚡ Quick Installation

### 1. Copy the folder
```bash
cp -r asset_guard_standalone/ /your/project/
```

### 2. Create your `.env` in the server root
```env
ASSET_GUARD_SECRET=my_ultra_strong_secret_key_here

# Enable/Disable blocked attempts logs (true | false)
ASSET_GUARD_LOG_ENABLED=true
```

### 3. Implement in PHP
```php
<?php
require_once 'asset_guard_standalone/ResourceBoot.php';
require_once 'asset_guard_standalone/SecurityHelper.php';
require_once 'asset_guard_standalone/ShieldWall.php';

ResourceBoot::setResourceScript('asset_guard_standalone/resource.php');
if (session_status() === PHP_SESSION_NONE) session_start();

// IMPORTANT: Send CSP Headers BEFORE any HTML output
SecurityHelper::sendCSPHeaders();
?>
<!DOCTYPE html>
<html>
<head>
    <!-- Anti-Inspection Shield -->
    <?php
        $nonce = SecurityHelper::getNonce();
        echo str_replace('<script>', "<script nonce=\"{$nonce}\">", ShieldWall::render());
    ?>

    <!-- Protected CSS (Ghost Mode - Invisible in Elements) -->
    <?php echo SecurityHelper::css(ResourceBoot::url('path/to/style.css')); ?>

    <!-- Protected JS (XOR + Hex Obfuscation) -->
    <?php echo SecurityHelper::js(ResourceBoot::url('path/to/script.js')); ?>
</head>
<body>
    <h1>My Protected Website!</h1>
</body>
</html>
```

---

## 🔧 Advanced Usage

### Protecting multiple files
```php
<!-- Multiple CSS -->
<?php echo SecurityHelper::css(ResourceBoot::url('assets/css/reset.css')); ?>
<?php echo SecurityHelper::css(ResourceBoot::url('assets/css/main.css')); ?>

<!-- Multiple JS -->
<?php echo SecurityHelper::js(ResourceBoot::url('assets/js/app.js')); ?>
<?php echo SecurityHelper::js(ResourceBoot::url('assets/js/utils.js')); ?>
```

### Using without ShieldWall (Encryption only)
If you do not want to block F12 and DevTools (e.g., during development workflows):
```php
<?php
// Only encryption + Ghost CSS, without Anti-DevTools
SecurityHelper::sendCSPHeaders();
echo SecurityHelper::css(ResourceBoot::url('assets/css/style.css'));
echo SecurityHelper::js(ResourceBoot::url('assets/js/app.js'));
// DO NOT include: ShieldWall::render()
?>
```

### Set Custom `.env` Path
```php
ResourceBoot::setEnvPath('/custom/path/to/.env');
```

---

## 📊 Security Logs

### Enable / Disable
Logging can be toggled on or off via `.env`:
```env
ASSET_GUARD_LOG_ENABLED=true   # Enable logs
ASSET_GUARD_LOG_ENABLED=false  # Disable logs
```

When enabled, logs are saved periodically inside files (default `asset_guard_standalone/logs/` or server’s temp folder as a fallback mechanism).

> **⚠️ IMPORTANT:** The default Log Dashboard UI (`examples/logs.php`) is provided for **testing and development purposes only**. In a production environment, we highly recommend integrating your own dashboard or parsing logs utilizing professional stacks (Grafana, ELK Stack, etc.).

> **💡 Extensibility:** The logging module uses a clean, simple interface (`GuardLog::logBlock()`). You can seamlessly swap file-based logs for a Database (MySQL, PostgreSQL, MongoDB) by easily replacing the `logBlock()` function or extending the logic class. The payload format will always stay consistent.

### View statistics via PHP
```php
require_once 'asset_guard_standalone/GuardLog.php';

// Check if logging is globally enabled
if (GuardLog::isEnabled()) {
    $stats = GuardLog::getStats();
    echo "Blocked today: " . $stats['today'] . "\n";
    print_r($stats['top_ips']);
}
```

### Database Migration Example
To leverage SQL Databases rather than flat files, simply build a wrapper extending `GuardLog`:
```php
class GuardLogDB extends GuardLog
{
    public static function logBlock(string $reason, ?string $file = null): void
    {
        if (!self::isEnabled()) return;
        
        $pdo = new PDO('mysql:host=localhost;dbname=your_db', 'user', 'pass');
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

## 📁 Kit Structure

```
asset_guard_standalone/
├── ResourceBoot.php      # XOR Encryption Engine + Obfuscated Hex Loader
├── SecurityHelper.php     # Custom Tag Handlers + Dynamic CSP Nonce Manager
├── ShieldWall.php         # Anti-DevTools + Shortcut Blocks + Prototype Freeze
├── GuardLog.php           # Block Logging Controller
├── resource.php           # Security Gatekeeper Endpoint
├── env_loader.php         # .env Environment Variable Loader
├── 403.php                # Templated custom Access Denied page
├── .env.example           # Config Template Example
├── README.md              # Documentation
├── logs/                  # Secure blocked logs directory
│   ├── .htaccess          # Web access block protection
│   └── blocked_YYYY-MM-DD.log
└── examples/
    ├── index.php           # Functional Showcase Window covering everything
    └── assets/
        ├── css/style.css   # Protected example stylesheet
        └── js/script.js    # Protected example script
```

---

## ⚙️ Requirements

| Requirement | Supported Versions |
|-----------|--------|
| **PHP** | 8.1+ |
| **PHP Extensions** | `openssl`, `mbstring` |
| **Server Engine** | Apache / Nginx / LiteSpeed |
| **Database Required** | None |
| **External Dependencies**| None |

---

## ⚠️ Important Considerations

1. **No Client-Side protection is 100% impenetrable.** The explicit purpose of Asset Guard is to make reverse engineering so grueling, frustrating, and expensive that it ceases to be worth the attacker's time.

2. **ShieldWall can be highly aggressive.** Within development ecosystems, consider disabling it entirely so that developers can debug workflows normally.

3. **Your `.env` Secret Key is the architecture's heartbeat.** If anybody obtains the `ASSET_GUARD_SECRET`, overall encryption gets compromised. Shield that file extensively properly.

4. **Performance:** Asymmetric real-time encryption consumes minimal native server processing. On ultra-high traffic apps, plan ahead regarding cache solutions applied towards dynamic responses.

---

## 📝 License

MIT — Use freely across personal and commercial project paradigms.
