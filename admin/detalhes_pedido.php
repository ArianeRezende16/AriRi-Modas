
<?php
session_start();

if (
    !isset($_SESSION['usuario_id']) ||
    ($_SESSION['usuario_tipo'] ?? '') !== 'admin'
) {
    header('Location: login.php');
    exit;
}

require_once '../config/conexao.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header('Location: pedidos.php');
    exit;
}

$consulta = $pdo->prepare(
    "SELECT
        pedidos.id,
        pedidos.total,
        pedidos.forma_pagamento,
        pedidos.status,
        pedidos.criado_em,
        usuarios.nome AS cliente,
        usuarios.email
     FROM pedidos
     INNER JOIN usuarios
        ON pedidos.usuario_id = usuarios.id
     WHERE pedidos.id = :id"
);

$consulta->execute(['id' => $id]);
$pedido = $consulta->fetch();

if (!$pedido) {
    http_response_code(404);
    exit('Pedido não encontrado.');
}

$consultaItens = $pdo->prepare(
    "SELECT nome_produto, quantidade, preco_unitario
     FROM itens_pedido
     WHERE pedido_id = :pedido_id"
);

$consultaItens->execute(['pedido_id' => $id]);
$itens = $consultaItens->fetchAll();

$pagamentos = [
    'pix' => 'PIX',
    'boleto' => 'Boleto',
    'cartao' => 'Cartão de crédito'
];

include '../includes/header.php';
?>

<div class="container py-5" style="max-width: 850px;">

    <h2>Pedido #<?php echo (int) $pedido['id']; ?></h2>

    <p class="text-muted">
        Realizado em
        <?php echo date(
            'd/m/Y H:i',
            strtotime($pedido['criado_em'])
        ); ?>
    </p>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">

            <h5>Dados do cliente</h5>

            <p class="mb-1">
                <strong>Nome:</strong>
                <?php echo htmlspecialchars($pedido['cliente']); ?>
            </p>

            <p>
                <strong>E-mail:</strong>
                <?php echo htmlspecialchars($pedido['email']); ?>
            </p>

            <hr>

            <h5>Produtos comprados</h5>

            <div class="table-responsive">
                <table class="table align-middle">

                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Quantidade</th>
                            <th>Preço unitário</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($itens as $item): ?>
                            <tr>
                                <td>
                                    <?php echo htmlspecialchars($item['nome_produto']); ?>
                                </td>

                                <td>
                                    <?php echo (int) $item['quantidade']; ?>
                                </td>

                                <td>
                                    R$ <?php echo number_format(
                                        $item['preco_unitario'], 2, ',', '.'
                                    ); ?>
                                </td>

                                <td>
                                    R$ <?php echo number_format(
                                        $item['preco_unitario'] * $item['quantidade'],
                                        2, ',', '.'
                                    ); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>

            <hr>

            <p>
                <strong>Pagamento:</strong>
                <?php echo htmlspecialchars(
                    $pagamentos[$pedido['forma_pagamento']]
                    ?? 'Não informado'
                ); ?>
                (simulado)
            </p>

            <p>
                <strong>Status:</strong>
                <?php echo htmlspecialchars(ucfirst($pedido['status'])); ?>
            </p>

            <h4>
                Total: R$ <?php echo number_format(
                    $pedido['total'], 2, ',', '.'
                ); ?>
            </h4>

        </div>
    </div>

    <a href="pedidos.php" class="btn btn-outline-secondary">
        Voltar aos pedidos
    </a>

</div>

<?php include '../includes/footer.php'; ?>
