
<?php
require_once '../config/conexao.php';
include '../includes/header.php';

// Busca todos os produtos cadastrados no banco
$sql = "SELECT * FROM produtos ORDER BY id ASC";
$comando = $pdo->query($sql);
$produtos = $comando->fetchAll();
?>

<section class="pagina">
    <h2>Nossa coleção</h2>
    <p>Descubra peças escolhidas para valorizar seu estilo!</p>

    <div class="container mt-5">
        <div class="row g-4">

            <?php foreach ($produtos as $produto): ?>

                <div class="col-12 col-sm-6 col-md-4">
                    <div class="card produto-card h-100">

                        <img
                            src="../assets/img/<?php echo htmlspecialchars($produto['imagem']); ?>"
                            class="card-img-top foto-produto"
                            alt="<?php echo htmlspecialchars($produto['nome']); ?>"
                        >

                        <div class="card-body">

                            <p class="categoria-produto">
                                <?php echo htmlspecialchars($produto['categoria']); ?>
                            </p>

                            <h5 class="card-title">
                                <?php echo htmlspecialchars($produto['nome']); ?>
                            </h5>

                            <p class="card-text">
                                R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?>
                            </p>

                            <a href="produto.php?id=<?php echo $produto['id']; ?>"
                               class="btn btn-primary">
                                Ver produto
                            </a>

                        </div>
                    </div>
                </div>

            <?php endforeach; ?>

        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>

