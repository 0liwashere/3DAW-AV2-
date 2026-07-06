<?php
require_once __DIR__ . '/includes/config.php';
exigirLogin();

if (!isset($_SESSION['ultima_reserva_id'])) {
    header('Location: ' . BASE_URL . 'home.php');
    exit;
}

$pdo = getConexao();
$stmt = $pdo->prepare('
    SELECT r.*, c.nome AS carro_nome, c.imagem AS carro_imagem,
           lr.nome AS loja_retirada_nome, ld.nome AS loja_devolucao_nome
    FROM reservas r
    JOIN carros c ON c.id = r.carro_id
    JOIN lojas lr ON lr.id = r.loja_retirada_id
    JOIN lojas ld ON ld.id = r.loja_devolucao_id
    WHERE r.id = ? AND r.usuario_id = ?
');
$stmt->execute([$_SESSION['ultima_reserva_id'], $_SESSION['usuario_id']]);
$reserva = $stmt->fetch();

if (!$reserva) {
    header('Location: ' . BASE_URL . 'home.php');
    exit;
}

$usuario = usuarioAtual();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Reserva Confirmada - Rent a Car</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/confirmacao.css">
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<div class="confirmacao-conteudo">

    <div class="confirmacao-topo">
        <h1>RESERVA<br>CONFIRMADA</h1>
        <img src="<?= htmlspecialchars($reserva['carro_imagem']) ?>" alt="<?= htmlspecialchars($reserva['carro_nome']) ?>">
    </div>

    <p>Prezado <strong><?= htmlspecialchars($usuario['nome_completo']) ?></strong>,</p>
    <p>Agradecemos por escolher nossos serviços. Seguem os detalhes da sua reserva:</p>

    <ul>
        <li>Veículo reservado: <strong><?= htmlspecialchars($reserva['carro_nome']) ?></strong></li>
        <li>
            Período de aluguel:
            <strong>
                De <?= date('d/m', strtotime($reserva['data_hora_retirada'])) ?>,
                às <?= date('H\hi', strtotime($reserva['data_hora_retirada'])) ?>,
                até <?= date('d/m', strtotime($reserva['data_hora_devolucao'])) ?>,
                às <?= date('H\hi', strtotime($reserva['data_hora_devolucao'])) ?>
            </strong>
        </li>
        <li>Local de retirada: <strong><?= htmlspecialchars($reserva['loja_retirada_nome']) ?></strong></li>
        <li>Local de devolução: <strong><?= htmlspecialchars($reserva['loja_devolucao_nome']) ?></strong></li>
        <li>Valor total pago: <strong>R$ <?= number_format($reserva['valor_total'], 2, ',', '.') ?></strong></li>
    </ul>

    <p>Por favor, guarde este comprovante. Um e-mail com os detalhes completos foi enviado para você.</p>
    <p>A equipe está à disposição para qualquer dúvida ou suporte necessário. Agradecemos a preferência e desejamos uma ótima experiência!</p>

    <a href="home.php" class="btn-primario" style="display:inline-block; margin-top:10px;">Voltar para a Home</a>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

</body>
</html>
