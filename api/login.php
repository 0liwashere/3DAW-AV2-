<?php
require_once __DIR__ . '/../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'index.php');
    exit;
}

$identificador = trim($_POST['identificador'] ?? '');
$senha = $_POST['senha'] ?? '';

if ($identificador === '' || $senha === '') {
    $_SESSION['erro_login'] = 'Preencha e-mail/CPF e senha.';
    header('Location: ' . BASE_URL . 'index.php');
    exit;
}

$pdo = getConexao();

// Permite login tanto por e-mail quanto por CPF
$stmt = $pdo->prepare('SELECT * FROM usuarios WHERE email = ? OR cpf = ? LIMIT 1');
$stmt->execute([$identificador, $identificador]);
$usuario = $stmt->fetch();

if (!$usuario || !password_verify($senha, $usuario['senha_hash'])) {
    $_SESSION['erro_login'] = 'E-mail/CPF ou senha inválidos.';
    header('Location: ' . BASE_URL . 'index.php');
    exit;
}

// Login OK
$_SESSION['usuario_id'] = $usuario['id'];
$_SESSION['usuario_nome'] = $usuario['nome_completo'];

// "Manter conectado" - estende a duração do cookie de sessão
if (!empty($_POST['manter_conectado'])) {
    $trintaDias = 60 * 60 * 24 * 30;
    session_set_cookie_params($trintaDias);
}

header('Location: ' . BASE_URL . 'home.php');
exit;
