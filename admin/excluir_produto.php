
<?php
session_start();

if (
    !isset($_SESSION['usuario_id']) ||
    ($_SESSION['usuario_tipo'] ?? '') !== 'admin'
) {
    header('Location: login.php');
    exit;
}

// Aceita exclusão somente por formulário POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: gerenciar_produtos.php');
    exit;
}

// Verifica o token de segurança
$token = $_POST['csrf_token'] ?? '';

if (
    !is_string($token) ||
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $token)
) {
    http_response_code(403);
    exit('Solicitação inválida.');
}

require_once '../config/conexao.php';

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header('Location: gerenciar_produtos.php');
    exit;
}

try {
    $comando = $pdo->prepare(
        "DELETE FROM produtos WHERE id = :id"
    );

    $comando->execute(['id' => $id]);

    header('Location: gerenciar_produtos.php?excluido=1');
    exit;

} catch (PDOException $erro) {
    error_log($erro->getMessage());
    http_response_code(500);
    exit('Não foi possível excluir o produto.');
}
