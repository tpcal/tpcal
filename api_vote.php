<?php

require __DIR__ . '/inc/bootstrap.php';
require __DIR__ . '/inc/auth.php';


header('Content-Type: application/json');


try {
    $db = pdo();

    $raw = file_get_contents('php://input');
    
    $j = json_decode($raw, true) ?? [];
    
    $csrf = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    
    if (!hash_equals($_SESSION['csrf'] ?? '', $csrf))
        throw new Exception('CSRF');
    
    $pid = (int) ($j['policy_id'] ?? 0);
    
    $val = (int) ($j['value'] ?? 0);
    
    if ($pid <= 0 || !in_array($val, [1, 2, 3], true))
        throw new Exception('Bad input');

    $uid = current_user_id();
    
    if (!$uid) {
        $uid = ensure_anon_user($db);
    }

    // upsert vote
    $st = $db->prepare("INSERT INTO votes(user_id, policy_id, value) VALUES(?,?,?)
                               ON DUPLICATE KEY UPDATE value=VALUES(value)");
    $st->execute([$uid, $pid, $val]);

    // return updated aggregates + bayesian score
    $q = $db->prepare("SELECT COUNT(*) n, COALESCE(SUM(value),0) s FROM votes WHERE policy_id=?");
    $q->execute([$pid]);
    [$n, $s] = $q->fetch(PDO::FETCH_NUM);
    $m = 10;
    $C = 2.0;
    $score = ($s + $m * $C) / ($n + $m);
    echo json_encode(['ok' => true, 'n' => (int) $n, 'score' => $score]);
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'vote_failed']);
}
