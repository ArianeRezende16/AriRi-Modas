
<?php
session_start();

require_once '../config/conexao.php';

// Aceita apenas requisições POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: colecao.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header('Location: colecao.php');
    exit;
}

// Consulta o produto no banco de dados
$consulta = $pdo->prepare(
    "SELECT id, estoque FROM produtos WHERE id = :id"
);

$consulta->execute(['id' => $id]);
$produto = $consulta->fetch();

if (!$produto) {
    header('Location: colecao.php');
    exit;
}

// Cria o carrinho caso ainda não exista
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

$quantidadeAtual = (int) ($_SESSION['carrinho'][$id] ?? 0);

// Impede adicionar mais unidades do que o estoque
if (
    (int) $produto['estoque'] <= 0 ||
    $quantidadeAtual >= (int) $produto['estoque']
) {
    header('Location: produto.php?id=' . $id . '&estoque=1');
    exit;
}

// Adiciona uma unidade ao carrinho
$_SESSION['carrinho'][$id] = $quantidadeAtual + 1;

header('Location: carrinho.php');
exit;
