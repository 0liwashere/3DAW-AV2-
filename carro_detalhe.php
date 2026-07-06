<?php
require_once __DIR__ . '/includes/config.php';
exigirLogin();

$pdo = getConexao();

$carroId = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM carros WHERE id = ? AND ativo = 1');
$stmt->execute([$carroId]);
$carro = $stmt->fetch();

if (!$carro) {
    header('Location: ' . BASE_URL . 'carros.php');
    exit;
}

$lojas = $pdo->query('SELECT id, nome FROM lojas ORDER BY nome')->fetchAll();
$protecoes = $pdo->query('SELECT * FROM protecoes ORDER BY id')->fetchAll();
$adicionais = $pdo->query('SELECT * FROM adicionais ORDER BY id')->fetchAll();
$usuario = usuarioAtual();

$hoje = date('Y-m-d\TH:i', strtotime('+1 day 10:00'));
$emSeteDias = date('Y-m-d\TH:i', strtotime('+8 days 10:00'));
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($carro['nome']) ?> - Rent a Car</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/detalhe.css">
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<form class="detalhe-layout" id="formReserva" action="api/iniciar_reserva.php" method="POST">
    <input type="hidden" name="carro_id" value="<?= $carro['id'] ?>">

    <div class="detalhe-principal">

        <div class="detalhe-carro-topo">
            <img src="<?= htmlspecialchars($carro['imagem']) ?>" alt="<?= htmlspecialchars($carro['nome']) ?>">
            <div>
                <h1><?= htmlspecialchars($carro['nome']) ?></h1>
                <?php if ($carro['selo']): ?><span class="tag-verde"><?= htmlspecialchars($carro['selo']) ?></span><?php endif; ?>

                <div class="carro-specs">
                    <span>&#10052; AC</span>
                    <span>&#128100; <?= $carro['passageiros'] ?></span>
                    <span>&#128188; <?= $carro['capacidade_bagagem_litros'] ?>L</span>
                    <span>&#9881; <?= htmlspecialchars($carro['cambio']) ?></span>
                </div>

                <div class="carro-tags">
                    &#10003; Proteção veicular &nbsp;&nbsp; &#10003; Proteção contra roubo<br>
                    <span class="tag-info">&#9432; Informações importantes!</span>
                </div>

                <p style="margin-top:10px; font-size:0.85rem; color:#555;">
                    Ano: <?= $carro['ano_fabricacao'] ?> &nbsp;|&nbsp;
                    Combustível: <?= htmlspecialchars($carro['combustivel']) ?> &nbsp;|&nbsp;
                    Potência: <?= $carro['potencia_cv'] ?> cv
                </p>
            </div>
        </div>

        <div class="detalhe-local-datas">
            <div class="campo-box">
                <label>Local de retirada</label>
                <select name="loja_retirada_id" id="lojaRetirada">
                    <?php foreach ($lojas as $loja): ?>
                        <option value="<?= $loja['id'] ?>"><?= htmlspecialchars($loja['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo-box">
                <label>Local de devolução</label>
                <select name="loja_devolucao_id" id="lojaDevolucao">
                    <?php foreach ($lojas as $loja): ?>
                        <option value="<?= $loja['id'] ?>"><?= htmlspecialchars($loja['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo-box">
                <label>Data e hora de retirada</label>
                <input type="datetime-local" name="data_retirada" id="dataRetirada" value="<?= $hoje ?>">
            </div>
            <div class="campo-box">
                <label>Data e hora de devolução</label>
                <input type="datetime-local" name="data_devolucao" id="dataDevolucao" value="<?= $emSeteDias ?>">
            </div>
        </div>

        <div class="detalhe-secao">
            <h3>Proteções</h3>

            <label class="opcao-protecao" data-preco="0">
                <div class="opcao-cabecalho">
                    <span class="opcao-nome"><input type="radio" name="protecao_id" value="" checked> Sem proteção adicional</span>
                    <span class="opcao-preco">R$ 0,00/dia</span>
                </div>
            </label>

            <?php foreach ($protecoes as $protecao): ?>
                <label class="opcao-protecao" data-preco="<?= $protecao['preco_diaria'] ?>">
                    <div class="opcao-cabecalho">
                        <span class="opcao-nome">
                            <input type="radio" name="protecao_id" value="<?= $protecao['id'] ?>">
                            <?= htmlspecialchars($protecao['nome']) ?>
                        </span>
                        <span class="opcao-preco">R$ <?= number_format($protecao['preco_diaria'], 2, ',', '.') ?>/dia</span>
                    </div>
                    <div class="opcao-descricao"><?= htmlspecialchars($protecao['descricao']) ?></div>
                </label>
            <?php endforeach; ?>
        </div>

        <div class="detalhe-secao">
            <h3>Adicionais Opcionais</h3>

            <?php foreach ($adicionais as $adicional): ?>
                <div class="opcao-adicional adicional-linha" data-preco="<?= $adicional['preco_diaria'] ?>">
                    <span><?= htmlspecialchars($adicional['nome']) ?> — R$ <?= number_format($adicional['preco_diaria'], 2, ',', '.') ?>/dia</span>

                    <?php if ($adicional['permite_quantidade']): ?>
                        <div class="seletor-quantidade">
                            <button type="button" class="btn-menos">-</button>
                            <span class="qtd-valor">0</span>
                            <button type="button" class="btn-mais">+</button>
                            <input type="hidden" name="adicionais[<?= $adicional['id'] ?>]" value="0" class="qtd-input">
                        </div>
                    <?php else: ?>
                        <label class="switch-adicional">
                            <input type="checkbox" name="adicionais_simples[]" value="<?= $adicional['id'] ?>">
                            Adicionar
                        </label>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="detalhe-lateral">
        <div class="resumo-caixa">
            <div class="resumo-titulo">Resumo da Reserva</div>
            <div class="resumo-corpo" id="resumoCorpo">
                <p style="font-size:0.85rem; color:#666;">Diário do veículo: R$ <?= number_format($carro['preco_diaria'], 2, ',', '.') ?>/dia</p>

                <div class="resumo-linha">
                    <span>Diárias <span class="sub" id="diasTexto">7 x R$ <?= number_format($carro['preco_diaria'], 2, ',', '.') ?></span></span>
                    <span id="subtotalDiarias">R$ 0,00</span>
                </div>
                <div class="resumo-linha resumo-desconto" id="linhaPromocao" style="display:none;">
                    <span>Promoção <span class="sub">Black Nov</span></span>
                    <span id="valorPromocao">-R$ 0,00</span>
                </div>
                <div class="resumo-linha" id="linhaProtecao" style="display:none;">
                    <span>Proteção <span class="sub" id="protecaoTexto"></span></span>
                    <span id="valorProtecao">R$ 0,00</span>
                </div>
                <div class="resumo-linha" id="linhaAdicionais" style="display:none;">
                    <span>Opcionais</span>
                    <span id="valorAdicionais">R$ 0,00</span>
                </div>

                <div class="resumo-total">
                    <span>Total (sem taxas)</span>
                    <span id="valorTotal">R$ 0,00</span>
                </div>
            </div>
        </div>

        <div class="seus-dados-caixa">
            <h4>Seus Dados</h4>
            <p><?= htmlspecialchars($usuario['nome_completo']) ?></p>
            <p><?= date('d/m/Y', strtotime($usuario['data_nascimento'])) ?></p>
            <p><?= htmlspecialchars($usuario['cpf']) ?></p>
            <p><?= htmlspecialchars($usuario['telefone']) ?></p>
            <p><?= htmlspecialchars($usuario['email']) ?></p>
            <?php if ($usuario['cnh']): ?><p>CNH: <?= htmlspecialchars($usuario['cnh']) ?></p><?php endif; ?>
        </div>

        <button type="submit" class="btn-primario" style="width:100%; margin-top:16px;">Continuar</button>
    </div>
</form>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script>
    const precoDiaria = <?= $carro['preco_diaria'] ?>;

    function formatarMoeda(valor) {
        return 'R$ ' + valor.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    function calcularDias() {
        const retirada = new Date(document.getElementById('dataRetirada').value);
        const devolucao = new Date(document.getElementById('dataDevolucao').value);
        if (isNaN(retirada) || isNaN(devolucao) || devolucao <= retirada) return 1;
        const diffMs = devolucao - retirada;
        return Math.max(1, Math.ceil(diffMs / (1000 * 60 * 60 * 24)));
    }

    function atualizarResumo() {
        const dias = calcularDias();
        document.getElementById('diasTexto').textContent = dias + ' x ' + formatarMoeda(precoDiaria);

        let subtotalDiarias = precoDiaria * dias;
        document.getElementById('subtotalDiarias').textContent = formatarMoeda(subtotalDiarias);

        const linhaPromocao = document.getElementById('linhaPromocao');
        let desconto = 0;
        if (dias >= 7) {
            desconto = precoDiaria;
            linhaPromocao.style.display = 'flex';
            document.getElementById('valorPromocao').textContent = '-' + formatarMoeda(desconto);
        } else {
            linhaPromocao.style.display = 'none';
        }


        const protecaoSelecionada = document.querySelector('input[name="protecao_id"]:checked');
        const linhaProtecao = document.getElementById('linhaProtecao');
        let valorProtecao = 0;
        if (protecaoSelecionada && protecaoSelecionada.value !== '') {
            const box = protecaoSelecionada.closest('.opcao-protecao');
            const precoDia = parseFloat(box.dataset.preco);
            valorProtecao = precoDia * dias;
            linhaProtecao.style.display = 'flex';
            document.getElementById('protecaoTexto').textContent = dias + ' x ' + formatarMoeda(precoDia);
            document.getElementById('valorProtecao').textContent = formatarMoeda(valorProtecao);
        } else {
            linhaProtecao.style.display = 'none';
        }

        document.querySelectorAll('.opcao-protecao').forEach(el => el.classList.remove('selecionada'));
        if (protecaoSelecionada) {
            protecaoSelecionada.closest('.opcao-protecao').classList.add('selecionada');
        }

        // Adicionais
        let valorAdicionais = 0;
        document.querySelectorAll('.opcao-adicional').forEach(function (linha) {
            const precoDia = parseFloat(linha.dataset.preco);
            const checkboxSimples = linha.querySelector('input[type="checkbox"]');
            const qtdInput = linha.querySelector('.qtd-input');

            if (checkboxSimples && checkboxSimples.checked) {
                valorAdicionais += precoDia * dias;
            }
            if (qtdInput) {
                const qtd = parseInt(qtdInput.value, 10) || 0;
                valorAdicionais += precoDia * dias * qtd;
            }
        });

        const linhaAdicionais = document.getElementById('linhaAdicionais');
        if (valorAdicionais > 0) {
            linhaAdicionais.style.display = 'flex';
            document.getElementById('valorAdicionais').textContent = formatarMoeda(valorAdicionais);
        } else {
            linhaAdicionais.style.display = 'none';
        }

        const total = subtotalDiarias - desconto + valorProtecao + valorAdicionais;
        document.getElementById('valorTotal').textContent = formatarMoeda(total);
    }

    document.querySelectorAll('.seletor-quantidade').forEach(function (seletor) {
        const input = seletor.querySelector('.qtd-input');
        const valorSpan = seletor.querySelector('.qtd-valor');

        seletor.querySelector('.btn-mais').addEventListener('click', function () {
            let qtd = parseInt(input.value, 10) || 0;
            if (qtd < 3) qtd++;
            input.value = qtd;
            valorSpan.textContent = qtd;
            atualizarResumo();
        });

        seletor.querySelector('.btn-menos').addEventListener('click', function () {
            let qtd = parseInt(input.value, 10) || 0;
            if (qtd > 0) qtd--;
            input.value = qtd;
            valorSpan.textContent = qtd;
            atualizarResumo();
        });
    });

    document.getElementById('dataRetirada').addEventListener('change', atualizarResumo);
    document.getElementById('dataDevolucao').addEventListener('change', atualizarResumo);
    document.querySelectorAll('input[name="protecao_id"]').forEach(el => el.addEventListener('change', atualizarResumo));
    document.querySelectorAll('input[name="adicionais_simples[]"]').forEach(el => el.addEventListener('change', atualizarResumo));

    atualizarResumo();
</script>

</body>
</html>
