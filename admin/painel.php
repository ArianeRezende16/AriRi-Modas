
<?php
session_start();

// Bloqueia usuários que não são administradores
if (
    !isset($_SESSION['usuario_id']) ||
    ($_SESSION['usuario_tipo'] ?? '') !== 'admin'
) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Painel administrativo | AriRi Modas</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<div class="container py-5">

    <h1>Painel administrativo</h1>

    <p>
        Bem-vinda,
        <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>!
    </p>

    <p>
        Você está conectada como administradora da AriRi Modas.
    </p>
    <a href="cadastrar_produto.php" class="btn btn-primary">
    Cadastrar novo produto
    </a>
    <a href="gerenciar_produtos.php" class="btn btn-outline-primary">
    Gerenciar produtos
</a>
<a href="logout.php" class="btn btn-outline-danger">
    Sair da conta
</a>
    <a href="../index.php" class="btn btn-outline-secondary">
        Ver loja
    </a>

</div>

</body>
</html>
