
<?php
include '../includes/header.php';
include '../includes/produtos.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
?>

<?php if ($id === false || $id === null || !isset($produtos[$id])): ?>

    <section class="pagina">
        <h2>Produto não encontrado!</h2>
        <a href="colecao.php" class="btn btn-primary">
            Voltar para a coleção
        </a>
    </section>

<?php else: ?>

    <?php $produto = $produtos[$id]; ?>

    <section class="container my-5">
        <div class="row align-items-center g-5">

            <div class="col-md-6">
                <img
                    src="../assets/img/<?php echo htmlspecialchars($produto['imagem']); ?>"
                    class="img-fluid rounded imagem-detalhe"
                    alt="<?php echo htmlspecialchars($produto['nome']); ?>"
                >
            </div>

            <div class="col-md-6">

                <p class="categoria-produto">
                    <?php echo htmlspecialchars($produto['categoria']); ?>
                </p>

                <h2>
                    <?php echo htmlspecialchars($produto['nome']); ?>
                </h2>

                <h3 class="preco-detalhe mt-3">
                    R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?>
                </h3>

                <p class="mt-4">
                    <?php echo htmlspecialchars($produto['descricao']); ?>
                </p>

                <button class="btn btn-primary mt-3" disabled>
                    Adicionar ao carrinho
                </button>

                <p class="text-muted mt-2">
                    Carrinho disponível em breve.
                </p>

                <a href="colecao.php" class="d-block mt-4">
                    Voltar para a coleção
                </a>

            </div>

        </div>
    </section>

<?php endif; ?>

<?php include '../includes/footer.php'; ?>
