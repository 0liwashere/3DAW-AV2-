<?php
require_once __DIR__ . '/../includes/config.php';
exigirLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['reserva_pendente'])) {
    header('Location: ' . BASE_URL . 'carros.php');
    exit;
}

$r = $_SESSION['reserva_pendente'];

// --- Validação simples: como é um projeto ilustrativo, qualquer valor nos campos do
//     cartão é aceito. Só exigimos que os campos e os aceites de termos existam. ---
$numeroCartao = trim($_POST['numero_cartao'] ?? '');
$nomeTitular = trim($_POST['nome_titular'] ?? '');
$expiracao = trim($_POST['expiracao'] ?? '');
$cvc = trim($_POST['cvc'] ?? '');
$aceiteTermos = isset($_POST['aceite_termos']);
$aceitePrivacidade = isset($_POST['aceite_privacidade']);

if (!$numeroCartao || !$nomeTitular || !$expiracao || !$cvc || !$aceiteTermos || !$aceitePrivacidade) {
    $_SESSION['erro_pagamento'] = 'Preencha todos os campos do cartão e aceite os termos para continuar.';
    header('Location: ' . BASE_URL . 'pagamento.php');
    exit;
}

$pdo = getConexao();
$usuarioId = $_SESSION['usuario_id'];

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare('
        INSERT INTO reservas (
            usuario_id, carro_id, loja_retirada_id, loja_devolucao_id,
            data_hora_retirada, data_hora_devolucao, protecao_id, cupom_id,
            subtotal_diarias, desconto_promocao, subtotal_protecao,
            subtotal_adicionais, desconto_cupom, taxa_locacao, valor_total, status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ');

    $stmt->execute([
        $usuarioId,
        $r['carro_id'],
        $r['loja_retirada_id'],
        $r['loja_devolucao_id'],
        $r['data_retirada'],
        $r['data_devolucao'],
        $r['protecao_id'],
        $r['cupom_id'],
        $r['subtotal_diarias'],
        $r['desconto_promocao'],
        $r['subtotal_protecao'],
        $r['subtotal_adicionais'],
        $r['desconto_cupom'],
        $r['taxa_locacao'],
        $r['valor_total'],
        'confirmada',
    ]);

    $reservaId = $pdo->lastInsertId();

    if (!empty($r['adicionais'])) {
        $stmtAdicional = $pdo->prepare('
            INSERT INTO reserva_adicionais (reserva_id, adicional_id, quantidade, preco_diaria_aplicado)
            VALUES (?, ?, ?, ?)
        ');
        foreach ($r['adicionais'] as $ad) {
            $stmtAdicional->execute([$reservaId, $ad['id'], $ad['quantidade'], $ad['preco_diaria']]);
        }
    }

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['erro_pagamento'] = 'Ocorreu um erro ao processar o pagamento. Tente novamente.';
    header('Location: ' . BASE_URL . 'pagamento.php');
    exit;
}

// Limpa a reserva pendente e guarda o id da reserva finalizada
unset($_SESSION['reserva_pendente']);
$_SESSION['ultima_reserva_id'] = $reservaId;

header('Location: ' . BASE_URL . 'reserva_confirmada.php');
exit;
