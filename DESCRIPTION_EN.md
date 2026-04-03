# 🛡️ Asset Guard — Total Protection for Web Assets

## What is it?

**Asset Guard** is a security system for PHP applications that protects your CSS and JavaScript files from copying, inspecting, and reverse engineering. It works as a "digital fortress" that wraps your assets in **14 independent layers of protection**, making it nearly impossible for someone to steal or replicate your source code through the browser.

---

## The Problem It Solves

Every website published on the internet has a fundamental flaw: **CSS and JavaScript code is exposed to anyone who opens the browser's Developer Tools (Inspect Element).** This means that:

- A competitor can copy your entire design in minutes
- A curious user can download your proprietary scripts
- A bot can automate the theft of your entire frontend
- Any scraping tool can clone your whole site

Asset Guard was created to solve exactly this. It doesn't just hide the code; it makes the reverse engineering process **so complex and time-consuming** that it simply isn't worth the effort for an attacker.

---

## How Does It Protect?

The system operates on 4 simultaneous fronts:

### 🔐 Real-Time Encryption
Your CSS and JS files are never delivered in plain text. They go through a **polymorphic XOR cipher** on the server, transforming the content into a seemingly random data sequence. The "key" to decrypt this content is sent separately, via an ephemeral cookie that expires in **60 seconds**. Each file receives a different key derived from its name plus a master secret that never leaves your server.

### 👻 Browser Invisibility
Protected CSS is injected directly into the browser's **rendering engine** using the modern `adoptedStyleSheets` API. This means:
- In the **Elements** tab: there is no `<style>` tag containing your code
- In the **Sources** tab: the original CSS file is completely absent
- In the **Network** tab: the content appears as a soup of encrypted characters

JavaScript, after being decrypted and executed, **removes its own `<script>` tag from the HTML**, leaving zero traces in the DOM tree.

### 🛑 Active Defense against Inspection
Asset Guard detects when someone tries to open the browser's DevTools (Inspect Element) and reacts defensively by clearing the page content. Furthermore, it blocks all inspection-related keyboard shortcuts:
- **F12** (open DevTools)
- **Ctrl+U** (view source code)
- **Ctrl+S** (save page)
- **Ctrl+Shift+I** (open DevTools)
- **Ctrl+Shift+J** (open Console)
- **Right-click** (context menu)

For more sophisticated attacks, the system freezes native JavaScript prototypes (`Object.prototype`, `Array.prototype`), preventing a hacker from redefining internal functions to intercept data.

### 📊 Security Intelligence
Every unauthorized access attempt is logged in detail, including:
- Attacker's IP (Cloudflare compatible)
- Browser's User-Agent
- Block reason
- Attempted file path
- Date and time

**Fully configurable:** Logs can be toggled on or off at any time via a `.env` file. The system's architecture also seamlessly allows logs to be saved to databases (MySQL, Postgres) instead of local files.

If a bot attempts to download a file directly (bypassing the security token), it will receive a **"Junk Bomb"**: 100MB of random binary data designed to crash or overwhelm the download tool.

---

## Who is it for?

- **PHP Developers** selling templates, themes, or web systems who want to protect their code
- **Digital Agencies** avoiding clients passing proprietary code to third parties
- **SaaS and web platforms** with proprietary frontend business logic
- **Any website** looking to harden its design and scripts against unauthorized copying

---

## Technical Edge

| Feature | Asset Guard | Common Solutions |
|---------------|-------------|-----------------|
| Per-file encryption | ✅ Unique key per file | ❌ Same obfuscation for all |
| Invisible CSS in DOM | ✅ `adoptedStyleSheets` | ❌ `<style>` tag remains visible |
| Script self-destruction | ✅ Removes tag after execution | ❌ Script stays in HTML |
| Anti-DevTools | ✅ Detection + defensive action | ❌ Not included |
| Dynamic CSP Nonce | ✅ Unique Nonce per page | ❌ Static CSP or none |
| Attack logging | ✅ Logs IP + reason + file | ❌ No logs kept |
| Anti-bot bomb | ✅ 100MB binary junk payload | ❌ Simply returns 403 error |
| Zero dependencies | ✅ Pure PHP, no npm/composer | ❌ Requires Node.js or extra libs |
| Database required | ✅ Not needed | ❌ Often required |

---

## Installation

Asset Guard is a standalone kit. Simply:

1. **Copy the folder** `asset_guard_standalone/` to your project
2. **Create a `.env` file** with your secret key
3. **Include 3 PHP files** into your pages

No Composer, npm, databases, or external services are needed. It works on any PHP 8.1+ server.

---

## Compatibility

- **PHP**: 8.1 or higher
- **Servers**: Apache, Nginx, LiteSpeed, OpenLiteSpeed
- **Browsers**: Chrome 73+, Firefox 75+, Edge 79+, Safari 16.4+
- **CDNs**: Cloudflare, AWS CloudFront (compatible with IP proxying)

---

## License

MIT — Use freely in personal and commercial projects.

---

> **Note:** No client-side protection is 100% impenetrable. The goal of Asset Guard is to make reverse engineering **so expensive and time-consuming** that it is not worth it for the attacker. For truly sensitive data (API keys, commercial secrets), always keep them exclusively on the server.
