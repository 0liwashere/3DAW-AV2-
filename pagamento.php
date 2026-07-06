<?php
require_once __DIR__ . '/includes/config.php';
exigirLogin();

if (!isset($_SESSION['reserva_pendente'])) {
    header('Location: ' . BASE_URL . 'carros.php');
    exit;
}

$r = $_SESSION['reserva_pendente'];
$usuario = usuarioAtual();

$erroCupom = $_SESSION['erro_cupom'] ?? null;
unset($_SESSION['erro_cupom']);

$erroPagamento = $_SESSION['erro_pagamento'] ?? null;
unset($_SESSION['erro_pagamento']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Pagamento - Rent a Car</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/detalhe.css">
    <link rel="stylesheet" href="assets/css/pagamento.css">
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<div class="pagamento-layout">

    <!-- Lado esquerdo: resumo -->
    <div class="pagamento-resumo-lado">
        <div class="resumo-caixa">
            <div class="resumo-titulo">Resumo da Reserva</div>
            <div class="resumo-corpo">
                <p class="carro-nome"><?= htmlspecialchars($r['carro_nome']) ?></p>

                <div class="resumo-linha">
                    <span>Diárias <span class="sub"><?= $r['dias'] ?> x R$ <?= number_format($r['preco_diaria'], 2, ',', '.') ?></span></span>
                    <span>R$ <?= number_format($r['subtotal_diarias'], 2, ',', '.') ?></span>
                </div>

                <?php if ($r['desconto_promocao'] > 0): ?>
                    <div class="resumo-linha resumo-desconto">
                        <span>Promoção <span class="sub">Black Nov</span></span>
                        <span>-R$ <?= number_format($r['desconto_promocao'], 2, ',', '.') ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($r['protecao_id']): ?>
                    <div class="resumo-linha">
                        <span><?= htmlspecialchars($r['protecao_nome']) ?> <span class="sub"><?= $r['dias'] ?> x R$ <?= number_format($r['protecao_preco_diaria'], 2, ',', '.') ?></span></span>
                        <span>R$ <?= number_format($r['subtotal_protecao'], 2, ',', '.') ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($r['adicionais'])): ?>
                    <p style="font-weight:600; margin: 10px 0 4px;">Opcionais</p>
                    <?php foreach ($r['adicionais'] as $ad): ?>
                        <div class="resumo-linha">
                            <span><?= htmlspecialchars($ad['nome']) ?> <span class="sub"><?= $r['dias'] ?> x R$ <?= number_format($ad['preco_diaria'], 2, ',', '.') ?><?= $ad['quantidade'] > 1 ? ' x ' . $ad['quantidade'] : '' ?></span></span>
                            <span>R$ <?= number_format($ad['subtotal'], 2, ',', '.') ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <div class="resumo-linha">
                    <span>Taxa de Locação <span class="sub">Taxa fixa</span></span>
                    <span>R$ <?= number_format($r['taxa_locacao'], 2, ',', '.') ?></span>
                </div>

                <!-- Cupom -->
                <div class="cupom-area">
                    <label>Adicionar cupom:</label>
                    <form class="cupom-linha" action="api/aplicar_cupom.php" method="POST">
                        <input type="text" name="codigo_cupom" placeholder="Digite o código do cupom" value="<?= htmlspecialchars($r['cupom_codigo'] ?? '') ?>">
                        <button type="submit">Aplicar</button>
                    </form>
                    <?php if ($erroCupom): ?>
                        <div class="cupom-mensagem cupom-erro"><?= htmlspecialchars($erroCupom) ?></div>
                    <?php elseif (!empty($r['cupom_codigo'])): ?>
                        <div class="cupom-mensagem cupom-aplicado">
                            Cupom "<?= htmlspecialchars($r['cupom_codigo']) ?>" aplicado (-R$ <?= number_format($r['desconto_cupom'], 2, ',', '.') ?>)
                            — <a href="api/remover_cupom.php">remover</a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="resumo-total">
                    <span>Valor Total</span>
                    <span>R$ <?= number_format($r['valor_total'], 2, ',', '.') ?></span>
                </div>

                <p class="parcelamento-texto">
                    Parcelam em: 3x de R$ <?= number_format($r['valor_total'] / 3, 2, ',', '.') ?> sem juros
                </p>
            </div>
        </div>
    </div>

    <!-- Lado direito: dados do cartão -->
    <div class="pagamento-cartao-lado">
        <div class="cartao-visual">
            <div class="cartao-topo">
                <span>&#128246;</span>
                <span>VISA</span>
            </div>
            <div class="cartao-numero" id="previewNumero">•••• •••• •••• ••••</div>
            <div class="cartao-rodape">
                <span id="previewNome">NOME DO TITULAR</span>
                <div class="validade-cvc">
                    <span id="previewValidade">--/--</span>
                    <span id="previewCvc">---</span>
                </div>
            </div>
        </div>

        <form action="api/processar_pagamento.php" method="POST" id="formPagamento">
            <?php if ($erroPagamento): ?>
                <div class="alerta alerta-erro" style="max-width:380px; margin:0 auto 10px;"><?= htmlspecialchars($erroPagamento) ?></div>
            <?php endif; ?>
            <div class="campo-grupo-pagamento">
                <label>Número do Cartão</label>
                <input type="text" id="numeroCartao" name="numero_cartao" placeholder="1234 5678 9012 3456" maxlength="19" required>

                <label>Nome do Titular</label>
                <input type="text" id="nomeTitular" name="nome_titular" placeholder="<?= htmlspecialchars(strtoupper($usuario['nome_completo'])) ?>" required>

                <div class="campo-linha-dupla">
                    <div>
                        <label>Expiração</label>
                        <input type="text" id="expiracao" name="expiracao" placeholder="MM/AA" maxlength="5" required>
                    </div>
                    <div>
                        <label>CVC</label>
                        <input type="text" id="cvc" name="cvc" placeholder="123" maxlength="4" required>
                    </div>
                </div>

                <div class="termos-area" style="margin-top:20px;">
                    <label>
                        <input type="checkbox" name="aceite_termos" required>
                        Li e aceito os Termos e Condições de aluguel.
                    </label>
                    <label>
                        <input type="checkbox" name="aceite_privacidade" required>
                        Eu concordo com a Política de Privacidade.
                    </label>
                </div>
            </div>

            <button type="submit" class="btn-primario">Pagar</button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script>
    const numeroCartao = document.getElementById('numeroCartao');
    const nomeTitular = document.getElementById('nomeTitular');
    const expiracao = document.getElementById('expiracao');
    const cvc = document.getElementById('cvc');

    numeroCartao.addEventListener('input', function () {
        let v = numeroCartao.value.replace(/\D/g, '').slice(0, 16);
        v = v.replace(/(\d{4})(?=\d)/g, '$1 ');
        numeroCartao.value = v;
        document.getElementById('previewNumero').textContent = v || '•••• •••• •••• ••••';
    });

    nomeTitular.addEventListener('input', function () {
        document.getElementById('previewNome').textContent = nomeTitular.value.toUpperCase() || 'NOME DO TITULAR';
    });

    expiracao.addEventListener('input', function () {
        let v = expiracao.value.replace(/\D/g, '').slice(0, 4);
        if (v.length > 2) v = v.slice(0, 2) + '/' + v.slice(2);
        expiracao.value = v;
        document.getElementById('previewValidade').textContent = v || '--/--';
    });

    cvc.addEventListener('input', function () {
        document.getElementById('previewCvc').textContent = cvc.value || '---';
    });
</script>

</body>
</html>
