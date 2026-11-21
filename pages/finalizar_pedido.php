
<?php
session_start();

require_once '../config/conexao.php';

// Apenas clientes logados podem finalizar compras
if (empty($_SESSION['cliente_id'])) {
    header('Location: login.php');
    exit;
}

// Apenas formulários POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: checkout.php');
    exit;
}

// Verifica o token de segurança
$token = $_POST['csrf_token'] ?? '';

if (
    !is_string($token) ||
    empty($_SESSION['csrf_checkout']) ||
    !hash_equals($_SESSION['csrf_checkout'], $token)
) {
    http_response_code(403);
    exit('Solicitação inválida.');
}

$formaPagamento = $_POST['forma_pagamento'] ?? '';

if (
    !is_string($formaPagamento) ||
    !in_array($formaPagamento, ['pix', 'boleto', 'cartao'], true)
) {
    http_response_code(400);
    exit('Forma de pagamento inválida.');
}

if (empty($_SESSION['carrinho'])) {
    header('Location: carrinho.php');
    exit;
}

try {
    // Inicia uma transação para manter os dados consistentes
    $pdo->beginTransaction();

    $itens = [];
    $totalCentavos = 0;

    // Ordena os produtos para reduzir conflitos de bloqueio
    $carrinho = $_SESSION['carrinho'];
    ksort($carrinho, SORT_NUMERIC);

    foreach ($carrinho as $id => $quantidade) {

        $id = filter_var($id, FILTER_VALIDATE_INT);
        $quantidade = filter_var(
            $quantidade,
            FILTER_VALIDATE_INT
        );

        if (
            !$id ||
            $id < 1 ||
            $quantidade === false ||
            $quantidade < 1
        ) {
            throw new Exception('O carrinho contém dados inválidos.');
        }

        // Bloqueia o produto durante a compra
        $consulta = $pdo->prepare(
            "SELECT id, nome, preco, estoque
             FROM produtos
             WHERE id = :id
             FOR UPDATE"
        );

        $consulta->execute(['id' => $id]);
        $produto = $consulta->fetch();

        if (!$produto) {
            throw new Exception('Um produto não está mais disponível.');
        }

        if ($quantidade > (int) $produto['estoque']) {
            throw new Exception(
                'Estoque insuficiente para: ' . $produto['nome']
            );
        }

        // Trabalha com centavos para evitar erros de arredondamento
        $precoCentavos = (int) round(
            (float) $produto['preco'] * 100
        );

        $totalCentavos += $precoCentavos * $quantidade;

        $itens[] = [
            'id' => (int) $produto['id'],
            'nome' => $produto['nome'],
            'quantidade' => $quantidade,
            'preco' => number_format(
                $precoCentavos / 100,
                2,
                '.',
                ''
            )
        ];
    }

    if (empty($itens)) {
        throw new Exception('O carrinho está vazio.');
    }

    $total = number_format(
        $totalCentavos / 100,
        2,
        '.',
        ''
    );

    // Salva o pedido

$comando = $pdo->prepare(
    "INSERT INTO pedidos
     (usuario_id, total, forma_pagamento, status)
     VALUES
     (:usuario_id, :total, :forma_pagamento, 'confirmado')"
);


    $comando->execute([
        'usuario_id' => $_SESSION['cliente_id'],
        'total' => $total,
        'forma_pagamento' => $formaPagamento
    ]);

    $pedidoId = (int) $pdo->lastInsertId();

    // Prepara os comandos dos itens e do estoque
    $inserirItem = $pdo->prepare(
        "INSERT INTO itens_pedido
         (pedido_id, produto_id, nome_produto, quantidade, preco_unitario)
         VALUES
         (:pedido_id, :produto_id, :nome_produto, :quantidade, :preco_unitario)"
    );


$atualizarEstoque = $pdo->prepare(
    "UPDATE produtos
     SET estoque = estoque - :quantidade_retirar
     WHERE id = :id
     AND estoque >= :quantidade_disponivel"
);


    foreach ($itens as $item) {

        $inserirItem->execute([
            'pedido_id' => $pedidoId,
            'produto_id' => $item['id'],
            'nome_produto' => $item['nome'],
            'quantidade' => $item['quantidade'],
            'preco_unitario' => $item['preco']
        ]);


$atualizarEstoque->execute([
    'quantidade_retirar' => $item['quantidade'],
    'quantidade_disponivel' => $item['quantidade'],
    'id' => $item['id']
]);


        if ($atualizarEstoque->rowCount() !== 1) {
            throw new Exception('Não foi possível atualizar o estoque.');
        }
    }

    // Confirma todas as alterações no banco
    $pdo->commit();

    // Esvazia o carrinho somente após salvar tudo
    $_SESSION['carrinho'] = [];

    // Renova o token para impedir reenvio do formulário
    $_SESSION['csrf_checkout'] = bin2hex(random_bytes(32));

    header('Location: pedido_sucesso.php?id=' . $pedidoId);
    exit;

} catch (Throwable $erro) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log($erro->getMessage());

    http_response_code(400);

    exit(
        'Não foi possível finalizar o pedido. ' .
        'Verifique os produtos e o estoque no carrinho.'
    );
}
