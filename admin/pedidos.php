
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

$consulta = $pdo->query(
    "SELECT
        pedidos.id,
        pedidos.total,
        pedidos.forma_pagamento,
        pedidos.status,
        pedidos.criado_em,
        usuarios.nome AS cliente,
        usuarios.email
     FROM pedidos
     INNER JOIN usuarios
        ON pedidos.usuario_id = usuarios.id
     ORDER BY pedidos.criado_em DESC, pedidos.id DESC"
);

$pedidos = $consulta->fetchAll();

$pagamentos = [
    'pix' => 'PIX',
    'boleto' => 'Boleto',
    'cartao' => 'Cartão de crédito'
];

include '../includes/header.php';
?>

<div class="container py-5">

    <h2 class="mb-2">Pedidos recebidos</h2>

    <p class="text-muted mb-4">
        Acompanhe as vendas da AriRi Modas.
    </p>

    <a href="painel.php" class="btn btn-outline-secondary mb-4">
        Voltar ao painel
    </a>

    <?php if (empty($pedidos)): ?>

        <div class="alert alert-info">
            Nenhum pedido foi realizado ainda.
        </div>

    <?php else: ?>

        <div class="table-responsive">

            <table class="table table-striped align-middle tabela-admin-pedidos">
                <thead>
                    <tr>
                        <th>Pedido</th>
                        <th>Cliente</th>
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
                                <strong>
                                    <?php echo htmlspecialchars($pedido['cliente']); ?>
                                </strong>

                                <br>

                                <small class="text-muted">
                                    <?php echo htmlspecialchars($pedido['email']); ?>
                                </small>
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
                                <span class="badge bg-warning text-dark">
                                    <?php echo htmlspecialchars(
                                        ucfirst($pedido['status'])
                                    ); ?>
                                </span>
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

</div>

<?php include '../includes/footer.php'; ?>
