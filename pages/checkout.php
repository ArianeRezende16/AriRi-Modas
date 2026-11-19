
<?php
session_start();

require_once '../config/conexao.php';

// Exige login de cliente
if (empty($_SESSION['cliente_id'])) {
    header('Location: login.php');
    exit;
}

if (empty($_SESSION['carrinho'])) {
    header('Location: carrinho.php');
    exit;
}

if (empty($_SESSION['csrf_checkout'])) {
    $_SESSION['csrf_checkout'] = bin2hex(random_bytes(32));
}

$itens = [];
$total = 0;
$erro = '';

foreach ($_SESSION['carrinho'] as $id => $quantidade) {

    $id = filter_var($id, FILTER_VALIDATE_INT);
    $quantidade = filter_var($quantidade, FILTER_VALIDATE_INT);

    if (!$id || !$quantidade || $quantidade < 1) {
        $erro = 'O carrinho contém um item inválido.';
        break;
    }

    $consulta = $pdo->prepare(
        "SELECT id, nome, preco, estoque
         FROM produtos
         WHERE id = :id"
    );

    $consulta->execute(['id' => $id]);
    $produto = $consulta->fetch();

    if (!$produto || $quantidade > (int) $produto['estoque']) {
        $erro = 'Um produto está indisponível ou sem estoque suficiente.';
        break;
    }

    $subtotal = (float) $produto['preco'] * $quantidade;

    $itens[] = [
        'nome' => $produto['nome'],
        'quantidade' => $quantidade,
        'subtotal' => $subtotal
    ];

    $total += $subtotal;
}

include '../includes/header.php';
?>

<div class="container py-5" style="max-width: 800px;">

    <h2 class="mb-2">Finalizar compra</h2>

    <p class="text-muted mb-4">
        Confira seu pedido e escolha uma forma de pagamento.
    </p>

    <?php if ($erro !== ''): ?>

        <div class="alert alert-warning">
            <?php echo htmlspecialchars($erro); ?>
            <a href="carrinho.php">Voltar ao carrinho</a>
        </div>

    <?php else: ?>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">

                <h5 class="card-title mb-3">Resumo do pedido</h5>

                <?php foreach ($itens as $item): ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span>
                            <?php echo htmlspecialchars($item['nome']); ?>
                            (<?php echo $item['quantidade']; ?>x)
                        </span>

                        <span>
                            R$ <?php echo number_format($item['subtotal'], 2, ',', '.'); ?>
                        </span>
                    </div>
                <?php endforeach; ?>

                <hr>

                <div class="d-flex justify-content-between">
                    <strong>Total</strong>
                    <strong>
                        R$ <?php echo number_format($total, 2, ',', '.'); ?>
                    </strong>
                </div>

            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">

                <h5 class="card-title mb-3">Forma de pagamento</h5>

                <form action="finalizar_pedido.php" method="POST">

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?php echo htmlspecialchars($_SESSION['csrf_checkout']); ?>"
                    >

                    <div class="form-check mb-3">
                        <input class="form-check-input"
                               type="radio"
                               name="forma_pagamento"
                               id="pix"
                               value="pix"
                               checked>

                        <label class="form-check-label" for="pix">
                            PIX (simulado)
                        </label>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input"
                               type="radio"
                               name="forma_pagamento"
                               id="boleto"
                               value="boleto">

                        <label class="form-check-label" for="boleto">
                            Boleto bancário (simulado)
                        </label>
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input"
                               type="radio"
                               name="forma_pagamento"
                               id="cartao"
                               value="cartao">

                        <label class="form-check-label" for="cartao">
                            Cartão de crédito (simulado)
                        </label>
                    </div>

                    <div class="alert alert-info">
                        Projeto escolar: nenhum pagamento real será realizado.
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        Confirmar pedido
                    </button>

                </form>

            </div>
        </div>

    <?php endif; ?>

</div>

<?php include '../includes/footer.php'; ?>
