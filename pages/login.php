
<?php
session_start();

require_once '../config/conexao.php';

if (
    isset($_SESSION['cliente_id']) &&
    $_SESSION['cliente_id'] > 0
) {
    header('Location: minha_conta.php');
    exit;
}

$erro = '';

if (empty($_SESSION['csrf_login'])) {
    $_SESSION['csrf_login'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $token = $_POST['csrf_token'] ?? '';

    if (
        !is_string($token) ||
        !hash_equals($_SESSION['csrf_login'], $token)
    ) {
        http_response_code(403);
        exit('Solicitação inválida.');
    }

    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $senha === '') {
        $erro = 'Preencha o e-mail e a senha corretamente.';
    } else {

        $consulta = $pdo->prepare(
            "SELECT id, nome, senha, tipo
             FROM usuarios
             WHERE email = :email
             LIMIT 1"
        );

        $consulta->execute(['email' => $email]);
        $usuario = $consulta->fetch();

        if (
            $usuario &&
            $usuario['tipo'] === 'cliente' &&
            password_verify($senha, $usuario['senha'])
        ) {
            session_regenerate_id(true);

            $_SESSION['cliente_id'] = (int) $usuario['id'];
            $_SESSION['cliente_nome'] = $usuario['nome'];

            header('Location: minha_conta.php');
            exit;
        }

        $erro = 'E-mail ou senha inválidos.';
    }
}

include '../includes/header.php';
?>

<div class="container py-5" style="max-width: 480px;">

    <h2 class="text-center mb-2">Minha conta</h2>

    <p class="text-center text-muted mb-4">
        Entre na sua conta da AriRi Modas.
    </p>

    <?php if ($erro !== ''): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($erro); ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <input
            type="hidden"
            name="csrf_token"
            value="<?php echo htmlspecialchars($_SESSION['csrf_login']); ?>"
        >

        <div class="mb-3">
            <label class="form-label">E-mail</label>

            <input
                type="email"
                name="email"
                class="form-control"
                required
            >
        </div>

        <div class="mb-3">
            <label class="form-label">Senha</label>

            <input
                type="password"
                name="senha"
                class="form-control"
                required
            >
        </div>

        <button type="submit" class="btn btn-primary w-100">
            Entrar
        </button>

    </form>

    <p class="text-center mt-4">
        Ainda não tem uma conta?
        <a href="cadastro.php">Cadastre-se</a>
    </p>

</div>

<?php include '../includes/footer.php'; ?>
