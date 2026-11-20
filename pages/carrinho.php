
<?php
session_start();

require_once '../config/conexao.php';

if (empty($_SESSION['csrf_carrinho'])) {
    $_SESSION['csrf_carrinho'] = bin2hex(random_bytes(32));
}

// Cria o carrinho na sessão
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

$produtosCarrinho = [];
$total = 0;

foreach ($_SESSION['carrinho'] as $id => $quantidade) {

    $consulta = $pdo->prepare(
        "SELECT * FROM produtos WHERE id = :id"
    );

    $consulta->execute(['id' => $id]);
    $produto = $consulta->fetch();

    if ($produto) {
        $quantidade = max(1, (int) $quantidade);
        $subtotal = $produto['preco'] * $quantidade;

        $produto['quantidade'] = $quantidade;
        $produto['subtotal'] = $subtotal;

        $produtosCarrinho[] = $produto;
        $total += $subtotal;
    }
}

include '../includes/header.php';
?>

<div class="container py-5">

    <h2 class="mb-4">Meu carrinho</h2>

    <?php if (empty($produtosCarrinho)): ?>

        <div class="text-center py-5">
            <h4>Seu carrinho está vazio!</h4>

            <p>Que tal escolher algumas peças?</p>

            <a href="colecao.php" class="btn btn-primary">
                Ver coleção
            </a>
        </div>

    <?php else: ?>

        <div class="table-responsive">
            <table class="table align-middle tabela-carrinho">

                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Quantidade</th>
                        <th>Preço</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($produtosCarrinho as $produto): ?>
                        
<tr>
    <td data-label="Produto">
        <div class="d-flex align-items-center gap-3">

            <img
                src="../assets/img/<?php echo htmlspecialchars($produto['imagem'], ENT_QUOTES, 'UTF-8'); ?>"
                alt="<?php echo htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8'); ?>"
                class="foto-carrinho"
            >

            <span>
                <?php echo htmlspecialchars($produto['nome']); ?>
            </span>

        </div>
    </td>


                            
<td data-label="Quantidade">
    <div class="d-flex align-items-center gap-2 controles-carrinho">

        <form action="atualizar_carrinho.php" method="POST">
            <input type="hidden" name="id"
                   value="<?php echo (int) $produto['id']; ?>">

            <input type="hidden" name="acao" value="diminuir">

            <input type="hidden" name="csrf_token"
                   value="<?php echo htmlspecialchars($_SESSION['csrf_carrinho']); ?>">

            <button type="submit" class="btn btn-sm btn-outline-secondary">
                -
            </button>
        </form>

        <span><?php echo (int) $produto['quantidade']; ?></span>

        <form action="atualizar_carrinho.php" method="POST">
            <input type="hidden" name="id"
                   value="<?php echo (int) $produto['id']; ?>">

            <input type="hidden" name="acao" value="aumentar">

            <input type="hidden" name="csrf_token"
                   value="<?php echo htmlspecialchars($_SESSION['csrf_carrinho']); ?>">

            <button type="submit"
                    class="btn btn-sm btn-outline-secondary"
                    <?php echo $produto['quantidade'] >= $produto['estoque'] ? 'disabled' : ''; ?>>
                +
            </button>
        </form>

        <form action="atualizar_carrinho.php" method="POST">
            <input type="hidden" name="id"
                   value="<?php echo (int) $produto['id']; ?>">

            <input type="hidden" name="acao" value="remover">

            <input type="hidden" name="csrf_token"
                   value="<?php echo htmlspecialchars($_SESSION['csrf_carrinho']); ?>">

            <button type="submit" class="btn btn-sm btn-outline-danger">
                Remover
            </button>
        </form>

    </div>
</td>


                            <td data-label="Preço">
    R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?>
</td>

                            <td data-label="Subtotal">
    R$ <?php echo number_format($produto['subtotal'], 2, ',', '.'); ?>
</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>
        </div>

        <div class="text-end mt-4">
            <h4>
                Total: R$ <?php echo number_format($total, 2, ',', '.'); ?>
            </h4>

            <a href="colecao.php" class="btn btn-outline-secondary mt-3">
                Continuar comprando
            </a>
            <a href="checkout.php" class="btn btn-primary mt-3">
    Finalizar compra
</a>
        </div>

    <?php endif; ?>

</div>

<?php include '../includes/footer.php'; ?>
