<?php
require_once __DIR__ . '/includes/config.php';
exigirLogin();

$pdo = getConexao();
$lojas = $pdo->query('SELECT nome, endereco FROM lojas ORDER BY nome')->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Rent a Car - Home</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/home.css">
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<!-- Barra de busca -->
<form class="busca-barra" action="carros.php" method="GET">
    <div class="busca-campo" style="flex:2;">
        <label for="local_retirada">Retirar o carro em</label>
        <input type="text" id="local_retirada" name="local_retirada" placeholder="Digite o local de retirada">
    </div>
    <div class="busca-campo">
        <label for="data_retirada">Data e hora de retirada</label>
        <input type="datetime-local" id="data_retirada" name="data_retirada">
    </div>
    <div class="busca-campo">
        <label for="data_devolucao">Data e hora de devolução</label>
        <input type="datetime-local" id="data_devolucao" name="data_devolucao">
    </div>
    <div class="busca-campo" style="max-width:100px;">
        <label for="estado">Estado</label>
        <select id="estado" name="estado">
            <option value="RJ" selected>RJ</option>
        </select>
    </div>
    <button type="submit" class="btn-primario">Pesquisar</button>
</form>

<!-- Banner promocional -->
<div class="banner-carrossel" id="bannerCarrossel">
    <button type="button" class="carrossel-seta esquerda">&#10094;</button>

    <div class="banner-slide ativo">
        <div class="banner-texto">
            <h2>BLACK</h2>
            <h3>Novembro</h3>
            <p>Uma oferta especial para quem quer aproveitar mais tempo na estrada!</p>
        </div>
        <div class="banner-carros">
            <img src="assets/images/carros/jeep-renegade.png" alt="Jeep Renegade" class="banner-carro-tras">
            <img src="assets/images/carros/hyundai-hb20.png" alt="Hyundai HB20" class="banner-carro-frente">
        </div>
        <div class="banner-destaque">
            <p>Alugue por 7 dias,<br><strong style="font-size:1.4rem;">pague 6</strong></p>
        </div>
    </div>

    <div class="banner-slide">
        <div class="banner-texto">
            <h2>Fim de Ano</h2>
            <h3>Chegando!</h3>
            <p>Garanta seu carro com antecedência para as festas de fim de ano.</p>
        </div>
        <div class="banner-carros">
            <img src="assets/images/carros/bmw-x3.png" alt="BMW X3" class="banner-carro-tras">
            <img src="assets/images/carros/kwid-zen.png" alt="Kwid Zen" class="banner-carro-frente">
        </div>
        <div class="banner-destaque">
            <p>Reserve com<br><strong style="font-size:1.4rem;">10% off</strong></p>
        </div>
    </div>

    <button type="button" class="carrossel-seta direita">&#10095;</button>
</div>

<!-- Texto institucional -->
<section class="secao-institucional">
    <h2>Descubra o Encanto do Rio de Janeiro</h2>
    <p>
        Bem-vindo ao Rio de Janeiro, a cidade maravilhosa que encanta moradores e turistas do mundo inteiro!
        Explore praias paradisíacas como Copacabana e Ipanema, descubra a vista deslumbrante do Cristo Redentor
        e aventure-se na Floresta da Tijuca, um verdadeiro pulmão verde no coração da cidade.
        Aqui no nosso site, você pode alugar o carro ideal para explorar o melhor do Rio de Janeiro com total
        liberdade e conforto. Planeje sua viagem com facilidade e conte conosco para tornar sua experiência
        ainda mais incrível!
    </p>
</section>

<!-- Carrossel de pontos turísticos -->
<div class="turismo-carrossel" id="turismoCarrossel">
    <button type="button" class="carrossel-seta esquerda">&#10094;</button>

    <div class="turismo-slide ativo" data-legenda="PRAIA DE COPACABANA">
        <img src="assets/images/turismo/copacabana.png" alt="Praia de Copacabana">
    </div>
    <div class="turismo-slide" data-legenda="PRAIA DE IPANEMA">
        <img src="assets/images/turismo/ipanema.png" alt="Praia de Ipanema">
    </div>
    <div class="turismo-slide" data-legenda="CRISTO REDENTOR">
        <img src="assets/images/turismo/cristo-redentor.png" alt="Cristo Redentor">
    </div>
    <div class="turismo-slide" data-legenda="FLORESTA DA TIJUCA">
        <img src="assets/images/turismo/floresta-tijuca.png" alt="Floresta da Tijuca">
    </div>

    <button type="button" class="carrossel-seta direita">&#10095;</button>
</div>
<p class="turismo-legenda" id="turismoLegenda">PRAIA DE COPACABANA</p>

<!-- Lista de Lojas -->
<div class="lojas-lista">
    <?php foreach ($lojas as $loja): ?>
        <div class="loja-item">
            <strong><?= htmlspecialchars($loja['nome']) ?></strong>
            <span><?= htmlspecialchars($loja['endereco']) ?></span>
        </div>
    <?php endforeach; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script src="assets/js/home.js"></script>
<script>
    // Atualiza a legenda do carrossel de turismo conforme o slide ativo
    document.addEventListener('DOMContentLoaded', function () {
        const carrossel = document.getElementById('turismoCarrossel');
        const legenda = document.getElementById('turismoLegenda');
        const slides = carrossel.querySelectorAll('.turismo-slide');

        function atualizarLegenda() {
            slides.forEach(function (slide) {
                if (slide.classList.contains('ativo')) {
                    legenda.textContent = slide.dataset.legenda;
                }
            });
        }

        carrossel.querySelectorAll('.carrossel-seta').forEach(function (botao) {
            botao.addEventListener('click', atualizarLegenda);
        });
    });
</script>

</body>
</html>
