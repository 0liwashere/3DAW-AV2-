<?php
require_once __DIR__ . '/includes/config.php';
exigirLogin();

$usuario = usuarioAtual();

$erro = $_SESSION['erro_perfil'] ?? null;
unset($_SESSION['erro_perfil']);

$sucesso = $_SESSION['sucesso_perfil'] ?? null;
unset($_SESSION['sucesso_perfil']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meu Perfil - Rent a Car</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/perfil.css">
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<div class="perfil-conteudo">

    <?php if ($erro): ?>
        <div class="alerta alerta-erro"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>
    <?php if ($sucesso): ?>
        <div class="alerta alerta-sucesso"><?= htmlspecialchars($sucesso) ?></div>
    <?php endif; ?>

    <form action="api/atualizar_perfil.php" method="POST" enctype="multipart/form-data">

        <div class="perfil-avatar-area">
            <div class="perfil-avatar-wrapper">
                <div class="perfil-avatar" id="previewAvatar">
                    <?php if ($usuario['foto_perfil']): ?>
                        <img src="<?= htmlspecialchars($usuario['foto_perfil']) ?>" alt="Foto de perfil" style="width:100%; height:100%; object-fit:cover;">
                    <?php else: ?>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.7 0 4.9-2.2 4.9-4.9S14.7 2.2 12 2.2 7.1 4.4 7.1 7.1 9.3 12 12 12zm0 2.4c-3.3 0-9.8 1.6-9.8 4.9v2.5h19.6v-2.5c0-3.3-6.5-4.9-9.8-4.9z"/></svg>
                    <?php endif; ?>
                </div>
                <label class="perfil-camera-btn" title="Alterar foto de perfil">
                    &#128247;
                    <input type="file" name="foto_perfil" id="inputFotoPerfil" accept="image/*">
                </label>
            </div>
        </div>

        <div class="perfil-linha">
            <div class="campo-grupo">
                <label>Nome Completo</label>
                <input type="text" name="nome_completo" value="<?= htmlspecialchars($usuario['nome_completo']) ?>" required>
            </div>
            <div class="campo-grupo">
                <label>Data de Nascimento</label>
                <input type="date" name="data_nascimento" value="<?= htmlspecialchars($usuario['data_nascimento']) ?>" required>
            </div>
            <div class="campo-grupo">
                <label>CPF</label>
                <input type="text" value="<?= htmlspecialchars($usuario['cpf']) ?>" disabled title="O CPF não pode ser alterado">
            </div>
        </div>

        <div class="perfil-linha">
            <div class="campo-grupo">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>
            </div>
            <div class="campo-grupo">
                <label>Telefone</label>
                <input type="text" name="telefone" value="<?= htmlspecialchars($usuario['telefone']) ?>" required>
            </div>
        </div>

        <div class="perfil-linha">
            <div class="campo-grupo">
                <label>CEP</label>
                <input type="text" name="cep" value="<?= htmlspecialchars($usuario['cep']) ?>" required>
            </div>
            <div class="campo-grupo">
                <label>Endereço</label>
                <input type="text" name="endereco" value="<?= htmlspecialchars($usuario['endereco']) ?>" required>
            </div>
            <div class="campo-grupo">
                <label>Número</label>
                <input type="text" name="numero" value="<?= htmlspecialchars($usuario['numero']) ?>" required>
            </div>
        </div>

        <div class="perfil-linha">
            <div class="campo-grupo">
                <label>Bairro</label>
                <input type="text" name="bairro" value="<?= htmlspecialchars($usuario['bairro']) ?>" required>
            </div>
            <div class="campo-grupo">
                <label>País</label>
                <input type="text" name="pais" value="<?= htmlspecialchars($usuario['pais']) ?>" required>
            </div>
            <div class="campo-grupo">
                <label>Estado</label>
                <input type="text" name="estado" value="<?= htmlspecialchars($usuario['estado']) ?>" required>
            </div>
        </div>

        <div class="perfil-linha">
            <div class="campo-grupo">
                <label>Município</label>
                <input type="text" name="municipio" value="<?= htmlspecialchars($usuario['municipio']) ?>" required>
            </div>
            <div class="campo-grupo">
                <label>CNH</label>
                <input type="text" name="cnh" value="<?= htmlspecialchars($usuario['cnh'] ?? '') ?>" placeholder="Digite o número da sua CNH">
            </div>
        </div>

        <button type="submit" class="btn-primario">Alterar</button>
    </form>

    <p style="text-align:center; margin-top:16px;">
        <a href="api/logout.php" style="color:#c62828; font-size:0.9rem;">Sair da conta</a>
    </p>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script>
    document.getElementById('inputFotoPerfil').addEventListener('change', function (e) {
        const arquivo = e.target.files[0];
        if (!arquivo) return;

        const leitor = new FileReader();
        leitor.onload = function (evento) {
            document.getElementById('previewAvatar').innerHTML =
                '<img src="' + evento.target.result + '" style="width:100%;height:100%;object-fit:cover;">';
        };
        leitor.readAsDataURL(arquivo);
    });
</script>

</body>
</html>
