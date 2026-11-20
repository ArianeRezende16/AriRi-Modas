
<?php
session_start();

if (empty($_SESSION['cliente_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/conexao.php';

$consulta = $pdo->prepare(
    "SELECT id, total, forma_pagamento, status, criado_em
     FROM pedidos
     WHERE usuario_id = :usuario_id
     ORDER BY criado_em DESC, id DESC"
);

$consulta->execute([
    'usuario_id' => $_SESSION['cliente_id']
]);

$pedidos = $consulta->fetchAll();

$pagamentos = [
    'pix' => 'PIX',
    'boleto' => 'Boleto',
    'cartao' => 'Cartão de crédito'
];

include '../includes/header.php';
?>

<div class="container py-5">

    <h2 class="mb-2">Meus pedidos</h2>

    <p class="text-muted mb-4">
        Acompanhe suas compras na AriRi Modas.
    </p>

    <?php if (empty($pedidos)): ?>

        <div class="text-center py-5">
            <h4>Você ainda não fez nenhum pedido.</h4>

            <a href="colecao.php" class="btn btn-primary mt-3">
                Explorar coleção
            </a>
        </div>

    <?php else: ?>

        <div class="table-responsive">

            <table class="table table-striped align-middle tabela-meus-pedidos">

                <thead>
                    <tr>
                        <th>Pedido</th>
                        <th>Data</th>
                        <th>Pagamento</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Detalhes</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($pedidos as $pedido): ?>

                        <tr>
                            <td>
                                #<?php echo (int) $pedido['id']; ?>
                            </td>

                            <td>
                                <?php echo date(
                                    'd/m/Y H:i',
                                    strtotime($pedido['criado_em'])
                                ); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars(
                                    $pagamentos[$pedido['forma_pagamento']]
                                    ?? 'Não informado'
                                ); ?>
                            </td>

                            <td>
                                R$ <?php echo number_format(
                                    $pedido['total'], 2, ',', '.'
                                ); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars(
                                    ucfirst($pedido['status'])
                                ); ?>
                            </td>

                            <td>
                                <a
                                    href="detalhes_pedido.php?id=<?php echo (int) $pedido['id']; ?>"
                                    class="btn btn-sm btn-outline-primary"
                                >
                                    Ver detalhes
                                </a>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php endif; ?>

    <a href="minha_conta.php" class="btn btn-outline-secondary mt-3">
        Voltar para minha conta
    </a>

</div>

<?php include '../includes/footer.php'; ?>
