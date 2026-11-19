
<?php
session_start();

// Remove apenas os dados do cliente.
// Mantém o carrinho e outras sessões independentes.
unset(
    $_SESSION['cliente_id'],
    $_SESSION['cliente_nome']
);

session_regenerate_id(true);

header('Location: login.php');
exit;

