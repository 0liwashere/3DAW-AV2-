document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('fotoHabilitacao');
    const texto = document.getElementById('uploadTexto');

    if (input && texto) {
        input.addEventListener('change', function () {
            if (input.files && input.files.length > 0) {
                texto.textContent = input.files[0].name;
            } else {
                texto.textContent = '↑ Upload de imagem';
            }
        });
    }

    // Máscara simples de CPF (000.000.000-00)
    const cpfInput = document.querySelector('input[name="cpf"]');
    if (cpfInput) {
        cpfInput.addEventListener('input', function () {
            let v = cpfInput.value.replace(/\D/g, '').slice(0, 11);
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            cpfInput.value = v;
        });
    }
});
