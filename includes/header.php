
<?php
// Identifica a página atual
$paginaAtual = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>AriRi Modas | Loja Virtual</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS da loja -->
    <link rel="stylesheet" href="/AriRi%20Modas/assets/css/style.css">
    <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
>
</head>

<body>

    <!-- Cabeçalho -->
    <header>
        <h1>AriRi Modas</h1>
        <p>Seu estilo, sua essência.</p>
    </header>

    <!-- Menu de navegação -->
    
<!-- Menu de navegação -->
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid px-4">

        <!-- Botão do menu no celular -->
        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#menuAriri"
            aria-controls="menuAriri"
            aria-expanded="false"
            aria-label="Abrir menu de navegação"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Links da loja -->
        <div class="collapse navbar-collapse" id="menuAriri">
            <ul class="navbar-nav mx-auto">

                <li class="nav-item">
                    <a class="nav-link" href="/AriRi%20Modas/index.php">
                        Início
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/AriRi%20Modas/pages/novidades.php">
                        Novidades
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/AriRi%20Modas/pages/feminino.php">
                        Feminino
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/AriRi%20Modas/pages/promocoes.php">
                        Promoções
                    </a>
                </li>

            </ul>
        </div>

        <!-- Ícones de acesso -->
        <div class="icones-header d-flex align-items-center gap-3">

            <a href="/AriRi%20Modas/pages/login.php"
               title="Minha conta"
               aria-label="Minha conta">
                <i class="bi bi-person"></i>
            </a>

            <a href="/AriRi%20Modas/pages/carrinho.php"
               title="Meu carrinho"
               aria-label="Meu carrinho">
                <i class="bi bi-bag"></i>
            </a>

            <a href="/AriRi%20Modas/admin/login.php"
               title="Administração"
               aria-label="Administração">
                <i class="bi bi-gear"></i>
            </a>

        </div>

    </div>
</nav>


    <main>
