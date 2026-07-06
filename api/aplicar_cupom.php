<?php
require_once __DIR__ . '/../includes/config.php';
exigirLogin();

if (!isset($_SESSION['reserva_pendente'])) {
    header('Location: ' . BASE_URL . 'carros.php');
    exit;
}

$codigo = trim($_POST['codigo_cupom'] ?? '');

if ($codigo === '') {
    header('Location: ' . BASE_URL . 'pagamento.php');
    exit;
}

$pdo = getConexao();
$stmt = $pdo->prepare('SELECT * FROM cupons WHERE codigo = ? AND ativo = 1');
$stmt->execute([$codigo]);
$cupom = $stmt->fetch();

if (!$cupom) {
    $_SESSION['erro_cupom'] = 'Cupom inválido ou expirado.';
    header('Location: ' . BASE_URL . 'pagamento.php');
    exit;
}

$r = $_SESSION['reserva_pendente'];

// Recalcula o total já sem o cupom antigo (caso já tivesse um aplicado)
$totalSemCupom = $r['subtotal_diarias'] - $r['desconto_promocao'] + $r['subtotal_protecao'] + $r['subtotal_adicionais'] + $r['taxa_locacao'];

$r['cupom_id'] = $cupom['id'];
$r['cupom_codigo'] = $cupom['codigo'];
$r['desconto_cupom'] = (float) $cupom['valor_desconto'];
$r['valor_total'] = max(0, $totalSemCupom - $r['desconto_cupom']);

$_SESSION['reserva_pendente'] = $r;

header('Location: ' . BASE_URL . 'pagamento.php');
exit;
