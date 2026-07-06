<?php
require_once __DIR__ . '/includes/config.php';

if (usuarioEstaLogado()) {
    header('Location: ' . BASE_URL . 'home.php');
    exit;
}

$erro = $_SESSION['erro_cadastro'] ?? null;
unset($_SESSION['erro_cadastro']);


$pdo = getConexao();
$lojas = $pdo->query('SELECT id, nome FROM lojas ORDER BY nome')->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar-se - Rent a Car</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>

<div class="cadastro-wrapper" style="background-image:url('assets/images/backgrounds/cadastro-bg.png');">
    <div class="cadastro-header">
        <a href="<?= BASE_URL ?>index.php" class="close-btn" style="position:static;">&times;</a>
        <h1>Cadastrar-se no Rent a Car</h1>
    </div>

    <div class="cadastro-conteudo">

        <?php if ($erro): ?>
            <div class="alerta alerta-erro"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <form action="api/cadastro.php" method="POST" enctype="multipart/form-data">

            <div class="cadastro-secao">
                <h3>Informações pessoais</h3>
                <div class="cadastro-linha">
                    <div class="campo-grupo">
                        <input type="text" name="nome_completo" placeholder="Digite seu nome completo" required>
                    </div>
                    <div class="campo-grupo">
                        <input type="text" name="cpf" placeholder="Digite seu CPF" maxlength="14" required>
                    </div>
                    <div class="campo-grupo">
                        <input type="date" name="data_nascimento" placeholder="Digite sua data de nascimento" required>
                    </div>
                </div>
            </div>

            <div class="cadastro-secao">
                <h3>Contato</h3>
                <div class="cadastro-linha">
                    <div class="campo-grupo">
                        <input type="email" name="email" placeholder="Digite seu email" required>
                    </div>
                    <div class="campo-grupo">
                        <input type="text" name="telefone" placeholder="Digite seu telefone" required>
                    </div>
                </div>
            </div>

            <div class="cadastro-secao">
                <h3>Senha de Acesso</h3>
                <div class="cadastro-linha">
                    <div class="campo-grupo">
                        <input type="password" name="senha" placeholder="Crie uma senha" minlength="6" required>
                    </div>
                    <div class="campo-grupo">
                        <input type="password" name="confirmar_senha" placeholder="Confirme sua senha" minlength="6" required>
                    </div>
                </div>
            </div>

            <div class="cadastro-secao">
                <h3>Endereço de Residência</h3>
                <div class="cadastro-linha">
                    <div class="campo-grupo">
                        <input type="text" name="cep" placeholder="Digite seu CEP" required>
                    </div>
                    <div class="campo-grupo">
                        <input type="text" name="endereco" placeholder="Digite eu endereço" required>
                    </div>
                    <div class="campo-grupo">
                        <input type="text" name="numero" placeholder="Número" required>
                    </div>
                </div>
                <div class="cadastro-linha">
                    <div class="campo-grupo">
                        <input type="text" name="bairro" placeholder="Digite seu bairro" required>
                    </div>
                    <div class="campo-grupo">
                        <input type="text" name="pais" placeholder="Digite seu país" value="Brasil" required>
                    </div>
                    <div class="campo-grupo">
                        <input type="text" name="estado" placeholder="Digite seu estado" required>
                    </div>
                    <div class="campo-grupo">
                        <input type="text" name="municipio" placeholder="Município" required>
                    </div>
                </div>
            </div>

            <div class="cadastro-secao">
                <h3>Foto de Habilitação ou Identidade</h3>
                <label class="upload-box" id="uploadBoxLabel">
                    <span id="uploadTexto">&uarr; Upload de imagem</span>
                    <input type="file" name="foto_habilitacao" id="fotoHabilitacao" accept="image/*" required>
                </label>
            </div>

            <div class="cadastro-secao">
                <h3>Local de Partida</h3>
                <div class="campo-grupo" style="max-width:400px;">
                    <select name="loja_partida_id" required>
                        <option value="">Selecione o local de partida</option>
                        <?php foreach ($lojas as $loja): ?>
                            <option value="<?= $loja['id'] ?>"><?= htmlspecialchars($loja['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn-primario">Enviar</button>
        </form>
    </div>
</div>

<script src="assets/js/cadastro.js"></script>
</body>
</html>
