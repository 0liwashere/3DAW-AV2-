<?php
require_once __DIR__ . '/includes/config.php';

// Se já estiver logado, vai direto pra Home
if (usuarioEstaLogado()) {
    header('Location: ' . BASE_URL . 'home.php');
    exit;
}

$erro = $_SESSION['erro_login'] ?? null;
unset($_SESSION['erro_login']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Entrar - Rent a Car</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body class="auth-body">

<div class="login-wrapper">
    <div class="login-form-side">
        <a href="<?= BASE_URL ?>home.php" class="close-btn">&times;</a>
        <h1>Entrar no Rent a Car</h1>

        <?php if ($erro): ?>
            <div class="alerta alerta-erro"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <form action="api/login.php" method="POST">
            <div class="campo-grupo">
                <label for="identificador">E-mail ou CPF</label>
                <input type="text" id="identificador" name="identificador" placeholder="digite seu e-mail ou CPF" required>
            </div>

            <div class="campo-grupo">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" placeholder="digite sua senha" required>
            </div>

            <div class="linha-opcoes">
                <label><input type="checkbox" name="manter_conectado"> Manter conectado</label>
                <a href="#">Esqueci a senha</a>
            </div>

            <button type="submit" class="btn-primario" style="width:100%;">Entrar</button>
        </form>

        <div class="divisor-ou">ou</div>

        <button type="button" class="social-btn" disabled title="Não implementado neste projeto">Acesse com Google</button>
        <button type="button" class="social-btn" disabled title="Não implementado neste projeto">Acesse com Facebook</button>

        <div class="criar-conta-area">
            <a href="cadastro.php" class="btn-secundario">Criar Conta</a>
            <a href="#" class="admin-link" title="Não implementado neste projeto">Entre como administrador</a>
        </div>
    </div>

    <div class="login-image-side" style="background-image:url('assets/images/backgrounds/login-carro.png');"></div>
</div>

</body>
</html>
