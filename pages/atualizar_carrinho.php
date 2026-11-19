
<?php
session_start();

require_once '../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: carrinho.php');
    exit;
}

if (empty($_SESSION['csrf_carrinho'])) {
    http_response_code(403);
    exit('Solicitação inválida.');
}

$token = $_POST['csrf_token'] ?? '';

if (
    !is_string($token) ||
    !hash_equals($_SESSION['csrf_carrinho'], $token)
) {
    http_response_code(403);
    exit('Solicitação inválida.');
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$acao = $_POST['acao'] ?? '';

if (!$id || $id < 1) {
    header('Location: carrinho.php');
    exit;
}

if (!isset($_SESSION['carrinho'][$id])) {
    header('Location: carrinho.php');
    exit;
}

if ($acao === 'remover') {
    unset($_SESSION['carrinho'][$id]);
} elseif ($acao === 'diminuir') {

    $quantidade = (int) $_SESSION['carrinho'][$id] - 1;

    if ($quantidade <= 0) {
        unset($_SESSION['carrinho'][$id]);
    } else {
        $_SESSION['carrinho'][$id] = $quantidade;
    }

} elseif ($acao === 'aumentar') {

    $consulta = $pdo->prepare(
        "SELECT estoque FROM produtos WHERE id = :id"
    );

    $consulta->execute(['id' => $id]);
    $produto = $consulta->fetch();

    if ($produto) {
        $quantidadeAtual = (int) $_SESSION['carrinho'][$id];

        if ($quantidadeAtual < (int) $produto['estoque']) {
            $_SESSION['carrinho'][$id] = $quantidadeAtual + 1;
        }
    }
}

header('Location: carrinho.php');
exit;
