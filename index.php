
<?php include 'includes/header.php'; ?>

<!-- Banner principal -->
<section class="banner">

    <h2>Bem-vinda à AriRi Modas!</h2>

    <p>
        Encontre peças que combinam com você.
        Sua próxima escolha favorita está aqui!
    </p>

    <a href="pages/colecao.php" class="btn btn-primary">
        Conheça nossa coleção
    </a>

</section>


<?php
require_once __DIR__ . '/config/conexao.php';

// Busca quatro produtos disponíveis para a página inicial
$consultaDestaques = $pdo->query(
    "SELECT id, nome, preco, imagem
     FROM produtos
     WHERE estoque > 0
     ORDER BY id ASC
     LIMIT 4"
);

$destaques = $consultaDestaques->fetchAll();
?>

<section class="container py-5">

    <h2 class="text-center mb-2">
        Peças em destaque
    </h2>

    <p class="text-center text-muted mb-4">
        Escolhas especiais para o seu estilo.
    </p>

    <div class="row g-3 g-md-4">

        <?php foreach ($destaques as $produto): ?>

            <div class="col-6 col-md-3">

                <div class="card produto-card h-100">

                    <img
                        src="assets/img/<?php echo htmlspecialchars($produto['imagem'], ENT_QUOTES, 'UTF-8'); ?>"
                        class="card-img-top foto-produto"
                        alt="<?php echo htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8'); ?>"
                    >

                    <div class="card-body d-flex flex-column">

                        <h5 class="card-title">
                            <?php echo htmlspecialchars($produto['nome']); ?>
                        </h5>

                        <p class="card-text">
                            R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?>
                        </p>

                        <a
                            href="pages/produto.php?id=<?php echo (int) $produto['id']; ?>"
                            class="btn btn-primary mt-auto"
                        >
                            Ver peça
                        </a>

                    </div>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

    <div class="text-center mt-4">

        <a href="pages/feminino.php" class="btn btn-outline-secondary">
            Ver todas as peças
        </a>

    </div>

</section>

<?php include 'includes/footer.php'; ?>
