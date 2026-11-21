
<?php
require_once '../config/conexao.php';
include '../includes/header.php';


// Recebe o texto digitado na barra de pesquisa
$busca = trim($_GET['busca'] ?? '');

if ($busca !== '') {

    // Pesquisa produtos pelo nome, descrição ou categoria

$sql = "SELECT * FROM produtos
        WHERE nome LIKE :busca_nome
        OR descricao LIKE :busca_descricao
        OR categoria LIKE :busca_categoria
        ORDER BY id ASC";

$comando = $pdo->prepare($sql);

$comando->execute([
    'busca_nome' => '%' . $busca . '%',
    'busca_descricao' => '%' . $busca . '%',
    'busca_categoria' => '%' . $busca . '%'
]);


} else {

    // Se não pesquisar nada, mostra todos os produtos
    $sql = "SELECT * FROM produtos ORDER BY id ASC";
    $comando = $pdo->query($sql);

}

$produtos = $comando->fetchAll();

?>

<section class="pagina">
    <h2>Moda Feminina</h2>

<form action="feminino.php" method="GET" class="row g-2 mb-4">

    <div class="col-md-9">
        <input
            type="search"
            name="busca"
            class="form-control"
            placeholder="O que você está procurando?"
            value="<?php echo htmlspecialchars($_GET['busca'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
        >
    </div>

    <div class="col-md-3">
        <button type="submit" class="btn btn-primary w-100">
            Pesquisar
        </button>
    </div>

</form>

    <p>Descubra peças escolhidas para valorizar seu estilo!</p>

    <div class="container mt-5">
        <div class="row g-4">

            <?php foreach ($produtos as $produto): ?>

                <div class="col-6 col-md-4 col-lg-3">
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