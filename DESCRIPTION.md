# 🛡️ Asset Guard — Proteção Total para Ativos Web

## O que é?

O **Asset Guard** é um sistema de segurança para aplicações PHP que protege seus arquivos CSS e JavaScript contra cópia, inspeção e engenharia reversa. Ele funciona como uma "fortaleza digital" que envolve seus ativos em **14 camadas independentes de proteção**, tornando praticamente impossível para alguém roubar ou replicar seu código através do navegador.

---

## O Problema que ele resolve

Todo site publicado na internet tem um problema fundamental: **o código CSS e JavaScript fica exposto para qualquer pessoa que abrir o Inspecionar Elemento do navegador.** Isso significa que:

- Um concorrente pode copiar todo o seu design em minutos
- Um curioso pode baixar seus scripts proprietários
- Um bot pode automatizar o roubo de todo o seu frontend
- Qualquer ferramenta de scraping pode clonar seu site inteiro

O Asset Guard foi criado para resolver exatamente isso. Ele não apenas esconde o código, mas torna o processo de engenharia reversa **tão complexo e demorado** que não vale a pena para o atacante.

---

## Como ele protege?

O sistema opera em 4 frentes simultâneas:

### 🔐 Criptografia em Tempo Real
Seus arquivos CSS e JS nunca são entregues em texto puro. Eles passam por um **cifrador XOR polimórfico** no servidor, que transforma o conteúdo em uma sequência de dados aparentemente aleatória. A "chave" para abrir esse conteúdo é enviada separadamente, via cookie temporário que expira em **60 segundos**. Cada arquivo recebe uma chave diferente, derivada do seu nome + um segredo mestre que nunca sai do servidor.

### 👻 Invisibilidade no Navegador
O CSS protegido é injetado diretamente no **motor de renderização** do navegador, usando a API moderna `adoptedStyleSheets`. Isso significa que:
- Na aba **Elements**: não existe nenhuma tag `<style>` com seu código
- Na aba **Sources**: o arquivo CSS original não aparece
- Na aba **Network**: o conteúdo aparece como uma sopa de caracteres criptografados

O JavaScript, após ser descriptografado e executado, **remove a própria tag `<script>` do HTML**, não deixando rastros na árvore DOM.

### 🛑 Defesa Ativa contra Inspeção
O Asset Guard detecta quando alguém tenta abrir o DevTools do navegador (Inspecionar Elemento) e reage defensivamente, limpando o conteúdo da página. Além disso, ele bloqueia todos os atalhos de teclado relacionados à inspeção:
- **F12** (abrir DevTools)
- **Ctrl+U** (ver código-fonte)
- **Ctrl+S** (salvar página)
- **Ctrl+Shift+I** (abrir DevTools)
- **Ctrl+Shift+J** (abrir Console)
- **Botão direito** do mouse (menu de contexto)

Para ataques mais sofisticados, o sistema também congela os protótipos nativos do JavaScript (`Object.prototype`, `Array.prototype`), impedindo que um hacker redefina funções internas para interceptar dados.

### 📊 Inteligência de Segurança
Toda tentativa de acesso indevido é registrada em logs detalhados, incluindo:
- IP do atacante (compatível com Cloudflare)
- User-Agent do navegador
- Motivo do bloqueio
- Arquivo que tentou acessar
- Data e hora

**Totalmente configurável:** Os logs podem ser ativados ou desativados a qualquer momento via arquivo `.env`. A arquitetura do sistema também permite que os logs sejam salvos facilmente em bancos de dados (MySQL, Postgres) em vez de arquivos locais.

Se um bot tentar baixar um arquivo diretamente (sem o token de segurança), ele receberá uma **"Bomba de Lixo"**: 100MB de dados binários aleatórios que travam ou sobrecarregam a ferramenta de download.


---

## Para quem é?

- **Desenvolvedores PHP** que vendem templates, themes ou sistemas web e querem proteger seu código
- **Agências digitais** que não querem que clientes repassem o código para terceiros
- **SaaS e plataformas web** que possuem lógica de frontend proprietária
- **Qualquer site** que queira dificultar a cópia do design e dos scripts

---

## Diferenciais Técnicos

| Característica | Asset Guard | Soluções comuns |
|---------------|-------------|-----------------|
| Criptografia por arquivo | ✅ Chave única por arquivo | ❌ Mesma ofuscação para tudo |
| CSS invisível no DOM | ✅ adoptedStyleSheets | ❌ Tag `<style>` visível |
| Auto-destruição de scripts | ✅ Remove a tag após uso | ❌ Script permanece no HTML |
| Anti-DevTools | ✅ Detecção + ação defensiva | ❌ Não possui |
| CSP com Nonce dinâmico | ✅ Nonce único por página | ❌ CSP estático ou ausente |
| Logging de ataques | ✅ IP + motivo + arquivo | ❌ Não registra nada |
| Bomba anti-bot | ✅ 100MB de lixo binário | ❌ Apenas retorna 403 |
| Zero dependências | ✅ PHP puro, sem npm/composer | ❌ Requer Node.js ou bibliotecas |
| Banco de dados | ✅ Não precisa | ❌ Muitos precisam |

---

## Instalação

O Asset Guard é um kit standalone (autossuficiente). Basta:

1. **Copiar a pasta** `asset_guard_standalone/` para o seu projeto
2. **Criar o arquivo `.env`** com sua chave secreta
3. **Incluir 3 arquivos PHP** nas suas páginas

Não precisa de Composer, npm, banco de dados ou qualquer serviço externo. Funciona em qualquer servidor PHP 8.1+.

---

## Compatibilidade

- **PHP**: 8.1 ou superior
- **Servidores**: Apache, Nginx, LiteSpeed, OpenLiteSpeed
- **Navegadores**: Chrome 73+, Firefox 75+, Edge 79+, Safari 16.4+
- **CDNs**: Cloudflare, AWS CloudFront (compatível com proxy de IP)

---

## Licença

MIT — Use livremente em projetos pessoais e comerciais.

---

> **Nota:** Nenhuma proteção client-side é 100% inviolável. O objetivo do Asset Guard é tornar a engenharia reversa **tão cara e demorada** que não valha a pena para o atacante. Para dados verdadeiramente sensíveis (chaves de API, segredos comerciais), mantenha-os exclusivamente no servidor.
