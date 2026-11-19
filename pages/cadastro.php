
<?php
session_start();

require_once '../config/conexao.php';

$erro = '';
$sucesso = '';

if (empty($_SESSION['csrf_cadastro'])) {
    $_SESSION['csrf_cadastro'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $token = $_POST['csrf_token'] ?? '';

    if (
        !is_string($token) ||
        !hash_equals($_SESSION['csrf_cadastro'], $token)
    ) {
        http_response_code(403);
        exit('Solicitação inválida.');
    }

    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmarSenha = $_POST['confirmar_senha'] ?? '';

    if (
        $nome === '' ||
        mb_strlen($nome) > 150 ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($email) > 150
    ) {
        $erro = 'Preencha seu nome e e-mail corretamente.';

    } elseif (strlen($senha) < 8) {

        $erro = 'A senha deve ter pelo menos 8 caracteres.';

    } elseif ($senha !== $confirmarSenha) {

        $erro = 'As senhas não coincidem.';

    } else {

        $consulta = $pdo->prepare(
            "SELECT id FROM usuarios WHERE email = :email"
        );

        $consulta->execute(['email' => $email]);

        if ($consulta->fetch()) {

            $erro = 'Este e-mail já está cadastrado.';

        } else {

            $senhaProtegida = password_hash(
                $senha,
                PASSWORD_DEFAULT
            );

            try {
                $comando = $pdo->prepare(
                    "INSERT INTO usuarios
                     (nome, email, senha, tipo)
                     VALUES
                     (:nome, :email, :senha, 'cliente')"
                );

                $comando->execute([
                    'nome' => $nome,
                    'email' => $email,
                    'senha' => $senhaProtegida
                ]);

                $sucesso = 'Conta criada com sucesso! Você já pode entrar.';

                $_SESSION['csrf_cadastro'] = bin2hex(random_bytes(32));

            } catch (PDOException $e) {
                error_log($e->getMessage());
                $erro = 'Não foi possível criar sua conta.';
            }
        }
    }
}

include '../includes/header.php';
?>

<div class="container py-5" style="max-width: 550px;">

    <h2 class="text-center mb-2">Criar minha conta</h2>

    <p class="text-center text-muted mb-4">
        Cadastre-se e faça parte da AriRi Modas.
    </p>

    <?php if ($erro !== ''): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($erro); ?>
        </div>
    <?php endif; ?>

    <?php if ($sucesso !== ''): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($sucesso); ?>
            <a href="login.php">Fazer login</a>
        </div>
    <?php endif; ?>

    <form method="POST">

        <input type="hidden" name="csrf_token"
               value="<?php echo htmlspecialchars($_SESSION['csrf_cadastro']); ?>">

        <div class="mb-3">
            <label class="form-label">Nome completo</label>

            <input type="text" name="nome"
                   class="form-control"
                   maxlength="150"
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label">E-mail</label>

            <input type="email" name="email"
                   class="form-control"
                   maxlength="150"
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label">Senha</label>

            <input type="password" name="senha"
                   class="form-control"
                   minlength="8"
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label">Confirmar senha</label>

            <input type="password" name="confirmar_senha"
                   class="form-control"
                   minlength="8"
                   required>
        </div>

        <button type="submit" class="btn btn-primary w-100">
            Criar conta
        </button>

    </form>

    <p class="text-center mt-4">
        Já tem uma conta?
        <a href="login.php">Entrar</a>
    </p>

</div>

<?php include '../includes/footer.php'; ?>

