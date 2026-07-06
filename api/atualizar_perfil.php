<?php
require_once __DIR__ . '/../includes/config.php';
exigirLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'perfil.php');
    exit;
}

function voltarComErro($mensagem) {
    $_SESSION['erro_perfil'] = $mensagem;
    header('Location: ' . BASE_URL . 'perfil.php');
    exit;
}

$nome_completo = trim($_POST['nome_completo'] ?? '');
$data_nascimento = $_POST['data_nascimento'] ?? '';
$email = trim($_POST['email'] ?? '');
$telefone = trim($_POST['telefone'] ?? '');
$cep = trim($_POST['cep'] ?? '');
$endereco = trim($_POST['endereco'] ?? '');
$numero = trim($_POST['numero'] ?? '');
$bairro = trim($_POST['bairro'] ?? '');
$pais = trim($_POST['pais'] ?? '');
$estado = trim($_POST['estado'] ?? '');
$municipio = trim($_POST['municipio'] ?? '');
$cnh = trim($_POST['cnh'] ?? '');

if (!$nome_completo || !$data_nascimento || !$email || !$telefone || !$cep
    || !$endereco || !$numero || !$bairro || !$pais || !$estado || !$municipio) {
    voltarComErro('Preencha todos os campos obrigatórios.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    voltarComErro('E-mail inválido.');
}

$pdo = getConexao();
$usuarioId = $_SESSION['usuario_id'];

// Impede usar um e-mail que já pertence a outro usuário
$stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ? AND id != ?');
$stmt->execute([$email, $usuarioId]);
if ($stmt->fetch()) {
    voltarComErro('Este e-mail já está em uso por outra conta.');
}

// --- Upload de nova foto de perfil (opcional) ---
$caminhoFoto = null;
if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
    $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];
    $extensao = strtolower(pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION));

    if (!in_array($extensao, $extensoesPermitidas)) {
        voltarComErro('Formato de imagem inválido para a foto de perfil. Use JPG, PNG ou WEBP.');
    }

    $nomeArquivo = 'perfil_' . $usuarioId . '_' . uniqid() . '.' . $extensao;
    $caminhoDestino = __DIR__ . '/../uploads/perfil/' . $nomeArquivo;

    if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $caminhoDestino)) {
        $caminhoFoto = 'uploads/perfil/' . $nomeArquivo;
    }
}

if ($caminhoFoto) {
    $stmt = $pdo->prepare('
        UPDATE usuarios SET
            nome_completo = ?, data_nascimento = ?, email = ?, telefone = ?,
            cep = ?, endereco = ?, numero = ?, bairro = ?, pais = ?, estado = ?,
            municipio = ?, cnh = ?, foto_perfil = ?
        WHERE id = ?
    ');
    $stmt->execute([
        $nome_completo, $data_nascimento, $email, $telefone,
        $cep, $endereco, $numero, $bairro, $pais, $estado,
        $municipio, $cnh, $caminhoFoto, $usuarioId
    ]);
} else {
    $stmt = $pdo->prepare('
        UPDATE usuarios SET
            nome_completo = ?, data_nascimento = ?, email = ?, telefone = ?,
            cep = ?, endereco = ?, numero = ?, bairro = ?, pais = ?, estado = ?,
            municipio = ?, cnh = ?
        WHERE id = ?
    ');
    $stmt->execute([
        $nome_completo, $data_nascimento, $email, $telefone,
        $cep, $endereco, $numero, $bairro, $pais, $estado,
        $municipio, $cnh, $usuarioId
    ]);
}

// Atualiza o nome na sessão (usado em outras páginas)
$_SESSION['usuario_nome'] = $nome_completo;
$_SESSION['sucesso_perfil'] = 'Dados atualizados com sucesso!';

header('Location: ' . BASE_URL . 'perfil.php');
exit;
