<?php
/**
 * 403 - Custom Forbidden (Standalone)
 */
http_response_code(403);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Acesso Negado</title>
    <style>
        body { background: #0a0a0a; color: #fff; font-family: sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .box { text-align: center; padding: 40px; border: 1px solid #333; border-radius: 20px; background: #111; }
        h1 { color: #ef4444; font-size: 3rem; margin-bottom: 10px; }
        p { color: #888; margin-bottom: 30px; }
        a { color: #fff; text-decoration: none; border: 1px solid #444; padding: 10px 20px; border-radius: 10px; transition: 0.3s; }
        a:hover { background: #fff; color: #000; }
    </style>
</head>
<body>
    <div class="box">
        <h1>403</h1>
        <p>Ação não permitida. Este recurso está sob proteção do Asset Guard.</p>
        <a href="/">Voltar ao Início</a>
    </div>
</body>
</html>
