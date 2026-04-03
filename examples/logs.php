<?php
/**
 * Dashboard de Logs - Asset Guard (Acesso Restrito)
 * Mostra as estatísticas e os últimos bloqueios registrados.
 */

require_once __DIR__ . '/../GuardLog.php';

// Proteção básica: só pode acessar do próprio servidor ou com senha
$password = 'asset_guard_admin'; // Altere essa senha!
$authenticated = false;

if (isset($_GET['key']) && $_GET['key'] === $password) {
    $authenticated = true;
}

if (!$authenticated) {
    header("HTTP/1.1 403 Forbidden");
    echo "Acesso negado. Use ?key=sua_senha";
    exit;
}

$stats = GuardLog::getStats();
$diag = GuardLog::diagnose();

// Carregar os últimos 50 registros do log de hoje (usando o diretório detectado)
$logDir = $diag['log_dir'];
$todayFile = $logDir . '/blocked_' . date('Y-m-d') . '.log';
$entries = [];

if (file_exists($todayFile)) {
    $lines = file($todayFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $lines = array_reverse($lines);
    foreach (array_slice($lines, 0, 50) as $line) {
        $entry = json_decode($line, true);
        if ($entry) $entries[] = $entry;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guard Log — Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #0a0b10; color: #c4c4c4; font-family: 'Courier New', monospace; padding: 30px; }
        h1 { color: #10b981; font-size: 1.4rem; margin-bottom: 20px; }
        .stats { display: flex; gap: 20px; margin-bottom: 30px; flex-wrap: wrap; }
        .stat-card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); padding: 20px 30px; border-radius: 12px; }
        .stat-card .number { font-size: 2rem; color: #ef4444; font-weight: bold; }
        .stat-card .label { font-size: 0.75rem; color: #666; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; font-size: 0.8rem; }
        th { text-align: left; padding: 10px 12px; background: rgba(255,255,255,0.03); color: #888; border-bottom: 1px solid rgba(255,255,255,0.06); }
        td { padding: 8px 12px; border-bottom: 1px solid rgba(255,255,255,0.03); }
        tr:hover { background: rgba(255,255,255,0.02); }
        .reason { padding: 3px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: bold; }
        .reason-invalid_referer { background: rgba(239,68,68,0.15); color: #ef4444; }
        .reason-missing_params { background: rgba(245,158,11,0.15); color: #f59e0b; }
        .reason-invalid_token { background: rgba(239,68,68,0.15); color: #ef4444; }
        .reason-junk_bomb_triggered { background: rgba(168,85,247,0.15); color: #a855f7; }
        .reason-file_not_found { background: rgba(59,130,246,0.15); color: #3b82f6; }
        .reason-extension_blocked { background: rgba(236,72,153,0.15); color: #ec4899; }
        .empty { text-align: center; padding: 40px; color: #444; }
    </style>
</head>
<body>
    <h1>🛡️ Guard Log — Dashboard de Segurança</h1>

    <div class="stats">
        <div class="stat-card">
            <div class="number"><?= $stats['today'] ?></div>
            <div class="label">Bloqueios Hoje</div>
        </div>
        <div class="stat-card">
            <div class="number"><?= count($stats['top_ips']) ?></div>
            <div class="label">IPs Únicos</div>
        </div>
        <?php if (!empty($stats['top_ips'])): ?>
        <div class="stat-card">
            <div class="number" style="font-size: 1rem;"><?= array_key_first($stats['top_ips']) ?></div>
            <div class="label">IP Mais Bloqueado (<?= reset($stats['top_ips']) ?>x)</div>
        </div>
        <?php endif; ?>
    </div>

    <?php if (empty($entries)): ?>
        <div class="empty">Nenhum bloqueio registrado hoje. ✅</div>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Hora</th>
                <th>IP</th>
                <th>Motivo</th>
                <th>Arquivo</th>
                <th>User-Agent</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($entries as $e): ?>
            <tr>
                <td><?= htmlspecialchars(substr($e['time'] ?? '', 11)) ?></td>
                <td><?= htmlspecialchars($e['ip'] ?? '?') ?></td>
                <td><span class="reason reason-<?= htmlspecialchars($e['reason'] ?? '') ?>"><?= htmlspecialchars($e['reason'] ?? '?') ?></span></td>
                <td><?= htmlspecialchars(basename($e['file'] ?? 'N/A')) ?></td>
                <td style="max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars(substr($e['ua'] ?? '', 0, 80)) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.06); font-size: 0.7rem; color: #444;">
        <strong style="color: #666;">🔧 Diagnóstico:</strong>
        Log Dir: <code><?= htmlspecialchars($diag['log_dir']) ?></code> |
        Existe: <?= $diag['dir_exists'] ? '✅' : '❌' ?> |
        Gravável: <?= $diag['dir_writable'] ? '✅' : '❌' ?> |
        Teste Escrita: <?= $diag['write_test'] ? '✅' : '❌' ?> |
        PHP User: <code><?= htmlspecialchars($diag['php_user']) ?></code>
    </div>
</body>
</html>
