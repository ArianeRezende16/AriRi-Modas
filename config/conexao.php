
<?php

// Localiza o arquivo .env na pasta principal
$arquivoEnv = dirname(__DIR__) . '/.env';

if (!is_file($arquivoEnv)) {
    exit('Arquivo de configuração .env não encontrado.');
}

// Lê as variáveis do arquivo
$config = parse_ini_file($arquivoEnv, false, INI_SCANNER_RAW);

if ($config === false) {
    exit('Não foi possível ler o arquivo .env.');
}

$host = $config['DB_HOST'] ?? '';
$banco = $config['DB_NAME'] ?? '';
$usuario = $config['DB_USER'] ?? '';
$senha = $config['DB_PASS'] ?? '';

if ($host === '' || $banco === '' || $usuario === '') {
    exit('Configurações obrigatórias do banco incompletas.');
}

try {

    $pdo = new PDO(
        "mysql:host=$host;dbname=$banco;charset=utf8mb4",
        $usuario,
        $senha,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );

} catch (PDOException $erro) {

    error_log($erro->getMessage());

    http_response_code(500);

    exit('Não foi possível conectar ao banco de dados.');
}
