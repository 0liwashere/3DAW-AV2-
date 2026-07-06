document.addEventListener('DOMContentLoaded', function () {

    function criarCarrossel(containerSeletor, slideSeletor, prevSeletor, nextSeletor) {
        const container = document.querySelector(containerSeletor);
        if (!container) return;

        const slides = container.querySelectorAll(slideSeletor);
        let indiceAtual = 0;

        function mostrar(indice) {
            slides.forEach(function (slide, i) {
                slide.classList.toggle('ativo', i === indice);
            });
        }

        const btnPrev = container.querySelector(prevSeletor);
        const btnNext = container.querySelector(nextSeletor);

        if (btnPrev) {
            btnPrev.addEventListener('click', function () {
                indiceAtual = (indiceAtual - 1 + slides.length) % slides.length;
                mostrar(indiceAtual);
            });
        }

        if (btnNext) {
            btnNext.addEventListener('click', function () {
                indiceAtual = (indiceAtual + 1) % slides.length;
                mostrar(indiceAtual);
            });
        }

        mostrar(indiceAtual);
    }

    // Carrossel do banner promocional (Black Novembro)
    criarCarrossel('#bannerCarrossel', '.banner-slide', '.carrossel-seta.esquerda', '.carrossel-seta.direita');

    // Carrossel de pontos turísticos do RJ
    criarCarrossel('#turismoCarrossel', '.turismo-slide', '.carrossel-seta.esquerda', '.carrossel-seta.direita');

});
