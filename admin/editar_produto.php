
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

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header('Location: gerenciar_produtos.php');
    exit;
}

$consulta = $pdo->prepare(
    "SELECT * FROM produtos WHERE id = :id"
);
$consulta->execute(['id' => $id]);
$produto = $consulta->fetch();

if (!$produto) {
    http_response_code(404);
    exit('Produto não encontrado.');
}

$erro = '';
$categorias = ['Blusas', 'Calças', 'Vestidos', 'Saias'];


if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $token = $_POST['csrf_token'] ?? '';

    if (
        !is_string($token) ||
        !hash_equals($_SESSION['csrf_token'], $token)
    ) {
        http_response_code(403);
        exit('Solicitação inválida.');
    }


    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $categoria = $_POST['categoria'] ?? '';
    $preco = filter_var(
        $_POST['preco'] ?? '',
        FILTER_VALIDATE_FLOAT
    );
    $estoque = filter_var(
        $_POST['estoque'] ?? '',
        FILTER_VALIDATE_INT
    );

    if (
        $nome === '' ||
        mb_strlen($nome) > 150 ||
        !in_array($categoria, $categorias, true) ||
        $preco === false || !is_finite($preco) || $preco <= 0 ||
        $estoque === false || $estoque < 0
    ) {
        $erro = 'Confira os dados informados.';
    } else {

        $comando = $pdo->prepare(
            "UPDATE produtos
             SET nome = :nome,
                 descricao = :descricao,
                 categoria = :categoria,
                 preco = :preco,
                 estoque = :estoque
             WHERE id = :id"
        );

        $comando->execute([
            'nome' => $nome,
            'descricao' => $descricao,
            'categoria' => $categoria,
            'preco' => $preco,
            'estoque' => $estoque,
            'id' => $id
        ]);

        header('Location: gerenciar_produtos.php?editado=1');
        exit;
    }

    $produto['nome'] = $nome;
    $produto['descricao'] = $descricao;
    $produto['categoria'] = $categoria;
    $produto['preco'] = $_POST['preco'] ?? '';
    $produto['estoque'] = $_POST['estoque'] ?? '';
}

// Cria um token de segurança para o formulário
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar produto | AriRi Modas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="container py-5" style="max-width: 650px;">

    <h2>Editar produto</h2>
    <p>Atualize as informações da peça selecionada.</p>

    <?php if ($erro !== ''): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($erro); ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <input type="hidden" name="csrf_token"
               value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

        <div class="mb-3">
            <label class="form-label">Nome do produto</label>
            <input type="text" name="nome" class="form-control"
                   maxlength="150"
                   value="<?php echo htmlspecialchars($produto['nome']); ?>"
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label">Descrição</label>
            <textarea name="descricao" class="form-control"
                      rows="3"><?php echo htmlspecialchars($produto['descricao'] ?? ''); ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Categoria</label>
            <select name="categoria" class="form-select" required>
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?php echo htmlspecialchars($categoria); ?>"
                        <?php echo $produto['categoria'] === $categoria ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($categoria); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Preço (R$)</label>
            <input type="number" name="preco" class="form-control"
                   min="0.01" step="0.01"
                   value="<?php echo htmlspecialchars($produto['preco']); ?>"
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label">Estoque</label>
            <input type="number" name="estoque" class="form-control"
                   min="0"
                   value="<?php echo htmlspecialchars($produto['estoque']); ?>"
                   required>
        </div>

        <button type="submit" class="btn btn-primary">
            Salvar alterações
        </button>

        <a href="gerenciar_produtos.php" class="btn btn-outline-secondary">
            Cancelar
        </a>

    </form>
</div>

</body>
</html>
