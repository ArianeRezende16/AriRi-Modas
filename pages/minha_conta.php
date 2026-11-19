
<?php
session_start();

if (
    !isset($_SESSION['cliente_id']) ||
    $_SESSION['cliente_id'] <= 0
) {
    header('Location: login.php');
    exit;
}

include '../includes/header.php';
?>

<div class="container py-5 text-center">

    <h2>Minha conta</h2>

    <p class="mt-4">
        Bem-vinda à AriRi Modas,
        <strong>
            <?php echo htmlspecialchars($_SESSION['cliente_nome']); ?>
        </strong>!
    </p>

    <p>Agora você está conectada à sua conta.</p>
<a href="meus_pedidos.php" class="btn btn-outline-primary mt-3">
    Meus pedidos
</a>
    <a href="colecao.php" class="btn btn-primary mt-3">
        Continuar comprando
    </a>

    <a href="logout.php" class="btn btn-outline-danger mt-3">
        Sair da conta
    </a>

</div>

<?php include '../includes/footer.php'; ?>
