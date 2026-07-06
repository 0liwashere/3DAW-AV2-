<?php
require_once __DIR__ . '/../includes/config.php';
exigirLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'carros.php');
    exit;
}

$pdo = getConexao();

$carroId = (int) ($_POST['carro_id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM carros WHERE id = ? AND ativo = 1');
$stmt->execute([$carroId]);
$carro = $stmt->fetch();

if (!$carro) {
    header('Location: ' . BASE_URL . 'carros.php');
    exit;
}

$lojaRetiradaId = (int) ($_POST['loja_retirada_id'] ?? 0);
$lojaDevolucaoId = (int) ($_POST['loja_devolucao_id'] ?? 0);
$dataRetirada = $_POST['data_retirada'] ?? '';
$dataDevolucao = $_POST['data_devolucao'] ?? '';
$protecaoId = $_POST['protecao_id'] ?? '';
$adicionaisSimples = $_POST['adicionais_simples'] ?? [];
$adicionaisQtd = $_POST['adicionais'] ?? [];

// --- Validação das datas ---
$tsRetirada = strtotime($dataRetirada);
$tsDevolucao = strtotime($dataDevolucao);

if (!$tsRetirada || !$tsDevolucao || $tsDevolucao <= $tsRetirada) {
    $_SESSION['erro_reserva'] = 'As datas de retirada e devolução são inválidas.';
    header('Location: ' . BASE_URL . 'carro_detalhe.php?id=' . $carroId);
    exit;
}

$dias = max(1, (int) ceil(($tsDevolucao - $tsRetirada) / 86400));

// --- Proteção escolhida ---
$protecao = null;
if ($protecaoId !== '') {
    $stmt = $pdo->prepare('SELECT * FROM protecoes WHERE id = ?');
    $stmt->execute([(int) $protecaoId]);
    $protecao = $stmt->fetch();
}

// --- Adicionais escolhidos ---
$todosAdicionais = $pdo->query('SELECT * FROM adicionais')->fetchAll();
$adicionaisSelecionados = [];

foreach ($todosAdicionais as $ad) {
    $quantidade = 0;

    if (!$ad['permite_quantidade'] && in_array($ad['id'], $adicionaisSimples)) {
        $quantidade = 1;
    }

    if ($ad['permite_quantidade'] && isset($adicionaisQtd[$ad['id']])) {
        $quantidade = max(0, min(3, (int) $adicionaisQtd[$ad['id']]));
    }

    if ($quantidade > 0) {
        $adicionaisSelecionados[] = [
            'id' => $ad['id'],
            'nome' => $ad['nome'],
            'preco_diaria' => (float) $ad['preco_diaria'],
            'quantidade' => $quantidade,
            'subtotal' => $ad['preco_diaria'] * $dias * $quantidade,
        ];
    }
}

// --- Cálculos ---
$subtotalDiarias = $carro['preco_diaria'] * $dias;
$descontoPromocao = ($dias >= 7) ? (float) $carro['preco_diaria'] : 0;
$subtotalProtecao = $protecao ? $protecao['preco_diaria'] * $dias : 0;
$subtotalAdicionais = array_sum(array_column($adicionaisSelecionados, 'subtotal'));
$taxaLocacao = 100.29;

$valorTotal = $subtotalDiarias - $descontoPromocao + $subtotalProtecao + $subtotalAdicionais + $taxaLocacao;

// --- Guarda tudo na sessão até o pagamento ser confirmado ---
$_SESSION['reserva_pendente'] = [
    'carro_id' => $carro['id'],
    'carro_nome' => $carro['nome'],
    'carro_imagem' => $carro['imagem'],
    'preco_diaria' => (float) $carro['preco_diaria'],
    'loja_retirada_id' => $lojaRetiradaId,
    'loja_devolucao_id' => $lojaDevolucaoId,
    'data_retirada' => date('Y-m-d H:i:s', $tsRetirada),
    'data_devolucao' => date('Y-m-d H:i:s', $tsDevolucao),
    'dias' => $dias,
    'protecao_id' => $protecao['id'] ?? null,
    'protecao_nome' => $protecao['nome'] ?? null,
    'protecao_preco_diaria' => $protecao ? (float) $protecao['preco_diaria'] : 0,
    'adicionais' => $adicionaisSelecionados,
    'subtotal_diarias' => $subtotalDiarias,
    'desconto_promocao' => $descontoPromocao,
    'subtotal_protecao' => $subtotalProtecao,
    'subtotal_adicionais' => $subtotalAdicionais,
    'taxa_locacao' => $taxaLocacao,
    'cupom_id' => null,
    'cupom_codigo' => null,
    'desconto_cupom' => 0,
    'valor_total' => $valorTotal,
];

header('Location: ' . BASE_URL . 'pagamento.php');
exit;
