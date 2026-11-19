
<?php
session_start();

require_once '../config/conexao.php';

// Se já estiver logada como administradora
if (
    isset($_SESSION['usuario_id']) &&
    ($_SESSION['usuario_tipo'] ?? '') === 'admin'
) {
    header('Location: painel.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $senha === '') {
        $erro = 'Preencha os dados corretamente.';
    } else {

        $sql = "SELECT id, nome, email, senha, tipo
                FROM usuarios
                WHERE email = :email
                LIMIT 1";

        $comando = $pdo->prepare($sql);
        $comando->execute(['email' => $email]);

        $usuario = $comando->fetch();

        if (
            $usuario &&
            $usuario['tipo'] === 'admin' &&
            password_verify($senha, $usuario['senha'])
        ) {
            session_regenerate_id(true);

            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_tipo'] = $usuario['tipo'];

            header('Location: painel.php');
            exit;
        }

        $erro = 'E-mail ou senha inválidos.';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login administrativo | AriRi Modas</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >
</head>

<body>

<div class="container py-5" style="max-width: 450px;">

    <div class="card shadow-sm border-0 p-4">

        <h2 class="text-center mb-2">AriRi Modas</h2>

        <p class="text-center text-muted mb-4">
            Acesso administrativo
        </p>

        <?php if ($erro !== ''): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($erro); ?>
            </div>
        <?php endif; ?>

        <form method="POST">

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

        <a href="../index.php" class="text-center mt-4">
            Voltar para a loja
        </a>

    </div>

</div>

</body>
</html>
