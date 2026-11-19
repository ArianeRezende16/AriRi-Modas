
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
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$comando = $pdo->query(
    "SELECT id, nome, categoria, preco, estoque, imagem
     FROM produtos
     ORDER BY id DESC"
);

$produtos = $comando->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gerenciar produtos | AriRi Modas</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<div class="container py-5">

    <h2>Gerenciar produtos</h2>
    <p>Consulte os produtos cadastrados na AriRi Modas.</p>

    <a href="painel.php" class="btn btn-outline-secondary mb-4">
        Voltar ao painel
    </a>

    <div class="table-responsive">
        <table class="table table-striped align-middle">

            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Produto</th>
                    <th>Categoria</th>
                    <th>Preço</th>
                    <th>Estoque</th>
                    <th>Ações</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($produtos as $produto): ?>
                    <tr>

                        <td>
                            <img
                                src="../assets/img/<?php echo htmlspecialchars($produto['imagem']); ?>"
                                alt="<?php echo htmlspecialchars($produto['nome']); ?>"
                                style="width: 65px; height: 75px; object-fit: contain;"
                            >
                        </td>

                        <td>
                            <?php echo htmlspecialchars($produto['nome']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($produto['categoria']); ?>
                        </td>

                        <td>
                            R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?>
                        </td>

                        <td>
                            <?php echo (int) $produto['estoque']; ?>
                        </td>

                        <td>
                            <a
    href="editar_produto.php?id=<?php echo (int) $produto['id']; ?>"
    class="btn btn-sm btn-outline-primary">
    Editar
</a>

<form
    action="excluir_produto.php"
    method="POST"
    class="d-inline"
    onsubmit="return confirm('Tem certeza que deseja excluir este produto?');"
>
    <input
        type="hidden"
        name="id"
        value="<?php echo (int) $produto['id']; ?>"
    >

    <input
        type="hidden"
        name="csrf_token"
        value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>"
    >

    <button type="submit" class="btn btn-sm btn-outline-danger">
        Excluir
    </button>
</form>

                        </td>

                    </tr>
                <?php endforeach; ?>

            </tbody>

        </table>
    </div>

</div>

</body>
</html>
