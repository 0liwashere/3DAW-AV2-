<?php
require_once __DIR__ . '/../includes/config.php';
exigirLogin();

if (isset($_SESSION['reserva_pendente'])) {
    $r = $_SESSION['reserva_pendente'];
    $r['cupom_id'] = null;
    $r['cupom_codigo'] = null;
    $r['desconto_cupom'] = 0;
    $r['valor_total'] = $r['subtotal_diarias'] - $r['desconto_promocao'] + $r['subtotal_protecao'] + $r['subtotal_adicionais'] + $r['taxa_locacao'];
    $_SESSION['reserva_pendente'] = $r;
}

header('Location: ' . BASE_URL . 'pagamento.php');
exit;
