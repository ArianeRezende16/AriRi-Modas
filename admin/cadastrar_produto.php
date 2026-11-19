
<?php
session_start();

// Somente administradores podem acessar
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

$mensagem = '';
$erro = '';

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

    $categorias = ['Blusas', 'Calças', 'Vestidos', 'Saias'];

    if (
        $nome === '' ||
        !in_array($categoria, $categorias, true) ||
        $preco === false || $preco <= 0 ||
        $estoque === false || $estoque < 0
    ) {
        $erro = 'Confira os dados informados.';
    } elseif (
        !isset($_FILES['imagem']) ||
        $_FILES['imagem']['error'] !== UPLOAD_ERR_OK
    ) {
        $erro = 'Selecione uma imagem válida.';
    } elseif ($_FILES['imagem']['size'] > 5 * 1024 * 1024) {
        $erro = 'A imagem deve ter no máximo 5 MB.';
    } else {

        $arquivoTemporario = $_FILES['imagem']['tmp_name'];
        $tipo = (new finfo(FILEINFO_MIME_TYPE))
            ->file($arquivoTemporario);

        $extensoes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp'
        ];

        if (!isset($extensoes[$tipo])) {
            $erro = 'Use uma imagem JPG, PNG ou WEBP.';
        } else {

            $nomeImagem = bin2hex(random_bytes(16))
                . '.' . $extensoes[$tipo];

            $destino = __DIR__ . '/../assets/img/' . $nomeImagem;

            if (!move_uploaded_file($arquivoTemporario, $destino)) {
                $erro = 'Não foi possível salvar a imagem.';
            } else {

                try {
                    $sql = "INSERT INTO produtos
                            (nome, descricao, preco, imagem, categoria, estoque)
                            VALUES
                            (:nome, :descricao, :preco, :imagem, :categoria, :estoque)";

                    $comando = $pdo->prepare($sql);

                    $comando->execute([
                        'nome' => $nome,
                        'descricao' => $descricao,
                        'preco' => $preco,
                        'imagem' => $nomeImagem,
                        'categoria' => $categoria,
                        'estoque' => $estoque
                    ]);

                    $mensagem = 'Produto cadastrado com sucesso!';

                } catch (PDOException $erroBanco) {
                    @unlink($destino);
                    error_log($erroBanco->getMessage());
                    $erro = 'Não foi possível cadastrar o produto.';
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cadastrar produto | AriRi Modas</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<div class="container py-5" style="max-width: 650px;">

    <h2>Cadastrar produto</h2>
    <p>Adicione uma nova peça à AriRi Modas.</p>

    <?php if ($mensagem !== ''): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($mensagem); ?>
        </div>
    <?php endif; ?>

    <?php if ($erro !== ''): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($erro); ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <input
    type="hidden"
    name="csrf_token"
    value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>"
>


        <div class="mb-3">
            <label class="form-label">Nome do produto</label>
            <input type="text" name="nome"
                   class="form-control" maxlength="150" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Descrição</label>
            <textarea name="descricao"
                      class="form-control" rows="3"></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Categoria</label>
            <select name="categoria" class="form-select" required>
                <option value="">Selecione</option>
                <option value="Blusas">Blusas</option>
                <option value="Calças">Calças</option>
                <option value="Vestidos">Vestidos</option>
                <option value="Saias">Saias</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Preço (R$)</label>
            <input type="number" name="preco"
                   class="form-control" min="0.01"
                   step="0.01" placeholder="89.90" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Quantidade em estoque</label>
            <input type="number" name="estoque"
                   class="form-control" min="0" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Foto do produto</label>
            <input type="file" name="imagem"
                   class="form-control"
                   accept=".jpg,.jpeg,.png,.webp" required>
        </div>

        <button type="submit" class="btn btn-primary">
            Cadastrar produto
        </button>

        <a href="painel.php" class="btn btn-outline-secondary">
            Voltar ao painel
        </a>

    </form>

</div>

</body>
</html>
