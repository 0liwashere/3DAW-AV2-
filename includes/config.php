<?php
/**
 * Configurações gerais do sistema Rent a Car
 * Ajuste os dados de conexão conforme seu ambiente (ex: XAMPP)
 */

// --- Caminho base do site ---
// Se você acessar o site em http://localhost/rent-a-car/, deixe como está.
// Se colocar o projeto direto na raiz (http://localhost/), troque para '/'.
define('BASE_URL', '/rent-a-car/');

// --- Dados de conexão com o MySQL ---
define('DB_HOST', 'localhost');
define('DB_NAME', 'rent_a_car');
define('DB_USER', 'root');
define('DB_PASS', ''); // no XAMPP o padrão costuma ser senha vazia

// --- Sessão (necessária em toda página que usa $_SESSION) ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- Conexão PDO ---
function getConexao() {
    static $pdo = null;

    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            die("Erro ao conectar ao banco de dados: " . $e->getMessage());
        }
    }

    return $pdo;
}

// --- Funções auxiliares de autenticação ---
function usuarioEstaLogado() {
    return isset($_SESSION['usuario_id']);
}

function exigirLogin() {
    if (!usuarioEstaLogado()) {
        header('Location: ' . BASE_URL . 'index.php');
        exit;
    }
}

function usuarioAtual() {
    if (!usuarioEstaLogado()) {
        return null;
    }
    $pdo = getConexao();
    $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE id = ?');
    $stmt->execute([$_SESSION['usuario_id']]);
    return $stmt->fetch();
}
