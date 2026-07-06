<?php
require_once __DIR__ . '/../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'cadastro.php');
    exit;
}

function voltarComErro($mensagem) {
    $_SESSION['erro_cadastro'] = $mensagem;
    header('Location: ' . BASE_URL . 'cadastro.php');
    exit;
}

// --- Coleta e limpeza dos campos ---
$nome_completo = trim($_POST['nome_completo'] ?? '');
$cpf = preg_replace('/\D/', '', $_POST['cpf'] ?? '');
$data_nascimento = $_POST['data_nascimento'] ?? '';
$email = trim($_POST['email'] ?? '');
$telefone = trim($_POST['telefone'] ?? '');
$senha = $_POST['senha'] ?? '';
$confirmar_senha = $_POST['confirmar_senha'] ?? '';
$cep = trim($_POST['cep'] ?? '');
$endereco = trim($_POST['endereco'] ?? '');
$numero = trim($_POST['numero'] ?? '');
$bairro = trim($_POST['bairro'] ?? '');
$pais = trim($_POST['pais'] ?? 'Brasil');
$estado = trim($_POST['estado'] ?? '');
$municipio = trim($_POST['municipio'] ?? '');
$loja_partida_id = $_POST['loja_partida_id'] ?? '';

// --- Validações básicas ---
if (!$nome_completo || !$cpf || !$data_nascimento || !$email || !$telefone
    || !$senha || !$confirmar_senha || !$cep || !$endereco || !$numero
    || !$bairro || !$estado || !$municipio || !$loja_partida_id) {
    voltarComErro('Preencha todos os campos obrigatórios.');
}

if (strlen($cpf) !== 11) {
    voltarComErro('CPF inválido.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    voltarComErro('E-mail inválido.');
}

if ($senha !== $confirmar_senha) {
    voltarComErro('As senhas não coincidem.');
}

if (strlen($senha) < 6) {
    voltarComErro('A senha deve ter no mínimo 6 caracteres.');
}

if (!isset($_FILES['foto_habilitacao']) || $_FILES['foto_habilitacao']['error'] !== UPLOAD_ERR_OK) {
    voltarComErro('Envie a foto da sua habilitação ou identidade.');
}

$pdo = getConexao();

// --- Verifica duplicidade de e-mail/CPF ---
$stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ? OR cpf = ?');
$stmt->execute([$email, $cpf]);
if ($stmt->fetch()) {
    voltarComErro('Já existe uma conta com este e-mail ou CPF.');
}

// --- Upload da foto de habilitação ---
$extensoesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];
$extensao = strtolower(pathinfo($_FILES['foto_habilitacao']['name'], PATHINFO_EXTENSION));

if (!in_array($extensao, $extensoesPermitidas)) {
    voltarComErro('Formato de imagem inválido. Use JPG, PNG ou WEBP.');
}

$nomeArquivo = 'hab_' . uniqid() . '.' . $extensao;
$caminhoDestino = __DIR__ . '/../uploads/habilitacao/' . $nomeArquivo;

if (!move_uploaded_file($_FILES['foto_habilitacao']['tmp_name'], $caminhoDestino)) {
    voltarComErro('Erro ao enviar a imagem. Tente novamente.');
}

// --- Insere o usuário no banco ---
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

$stmt = $pdo->prepare('
    INSERT INTO usuarios (
        nome_completo, cpf, data_nascimento, email, telefone, senha_hash,
        cep, endereco, numero, bairro, pais, estado, municipio,
        foto_habilitacao, loja_partida_id
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
');

$stmt->execute([
    $nome_completo, $cpf, $data_nascimento, $email, $telefone, $senha_hash,
    $cep, $endereco, $numero, $bairro, $pais, $estado, $municipio,
    'uploads/habilitacao/' . $nomeArquivo, $loja_partida_id
]);

$novoUsuarioId = $pdo->lastInsertId();

// --- Loga o usuário automaticamente após o cadastro ---
$_SESSION['usuario_id'] = $novoUsuarioId;
$_SESSION['usuario_nome'] = $nome_completo;

header('Location: ' . BASE_URL . 'home.php');
exit;
