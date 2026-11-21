
<?php
session_start();

if (empty($_SESSION['cliente_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/conexao.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header('Location: minha_conta.php');
    exit;
}

$consulta = $pdo->prepare(
    "SELECT id, total, forma_pagamento, status
     FROM pedidos
     WHERE id = :id AND usuario_id = :usuario_id"
);

$consulta->execute([
    'id' => $id,
    'usuario_id' => $_SESSION['cliente_id']
]);

$pedido = $consulta->fetch();

if (!$pedido) {
    http_response_code(404);
    exit('Pedido não encontrado.');
}

$pagamentos = [
    'pix' => 'PIX',
    'boleto' => 'Boleto bancário',
    'cartao' => 'Cartão de crédito'
];

include '../includes/header.php';
?>

<div class="container py-5" style="max-width: 650px;">

    <div class="card border-0 shadow-sm text-center">
        <div class="card-body p-5">

            <h2 class="mb-3">Pedido realizado!</h2>

            <p>
                Seu pedido foi registrado com sucesso na AriRi Modas.
            </p>

            <h4 class="mt-4">
                Pedido #<?php echo (int) $pedido['id']; ?>
            </h4>

            <p class="mt-3">
                <strong>Total:</strong>
                R$ <?php echo number_format($pedido['total'], 2, ',', '.'); ?>
            </p>

            <p>
                <strong>Pagamento:</strong>
                <?php echo htmlspecialchars(
                    $pagamentos[$pedido['forma_pagamento']] ?? 'Não informado'
                ); ?>
                (simulado)
            </p>
<p>
    <strong>Status:</strong>
    <?php echo htmlspecialchars(ucfirst($pedido['status'])); ?>
</p>

            <div class="alert alert-info mt-4">
                Este é um projeto escolar.
                Nenhum pagamento real foi realizado.
            </div>

            <a href="colecao.php" class="btn btn-primary mt-3">
                Continuar comprando
            </a>

            <a href="minha_conta.php"
               class="btn btn-outline-secondary mt-3">
                Minha conta
            </a>

        </div>
    </div>

</div>

<?php include '../includes/footer.php'; ?>
