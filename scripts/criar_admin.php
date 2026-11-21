
<?php

// Este arquivo só pode ser executado pelo terminal
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Acesso não permitido.');
}

require_once __DIR__ . '/../config/conexao.php';

// Verifica se já existe um administrador
$consulta = $pdo->query(
    "SELECT COUNT(*) FROM usuarios WHERE tipo = 'admin'"
);

if ((int) $consulta->fetchColumn() > 0) {
    exit("Já existe um administrador cadastrado.\n");
}

echo "=== Criar administrador da AriRi Modas ===\n\n";

echo "Nome: ";
$nome = trim(fgets(STDIN));

echo "E-mail: ";
$email = trim(fgets(STDIN));

if (
    $nome === '' ||
    mb_strlen($nome) > 150 ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    strlen($email) > 150
) {
    exit("Nome ou e-mail inválido.\n");
}

// Gera uma senha aleatória para o administrador
$senha = bin2hex(random_bytes(12));
$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

try {

    $comando = $pdo->prepare(
        "INSERT INTO usuarios (nome, email, senha, tipo)
         VALUES (:nome, :email, :senha, 'admin')"
    );

    $comando->execute([
        'nome' => $nome,
        'email' => $email,
        'senha' => $senhaHash
    ]);

    echo "\nAdministrador criado com sucesso!\n";
    echo "E-mail: " . $email . "\n";
    echo "Senha gerada: " . $senha . "\n";
    echo "\nGuarde a senha em local seguro.\n";

} catch (PDOException $erro) {

    error_log($erro->getMessage());

    exit("Não foi possível criar o administrador.\n");
}
