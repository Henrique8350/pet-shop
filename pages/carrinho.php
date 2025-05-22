<?php
session_start();
include_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/conexao.php';

$carrinho = $_SESSION['carrinho'] ?? [];
$produtos_no_carrinho = [];
$total = 0;

if (!empty($carrinho)) {
    $ids = implode(',', array_map('intval', array_keys($carrinho)));
    $sql = "SELECT * FROM produtos WHERE id_produto IN ($ids)";
    $result = $conn->query($sql);

    while ($produto = $result->fetch_assoc()) {
        $quantidade = $carrinho[$produto['id_produto']];
        $produto['quantidade'] = $quantidade;
        $produto['subtotal'] = $produto['preco'] * $quantidade;
        $total += $produto['subtotal'];
        $produtos_no_carrinho[] = $produto;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head class="style_carrinho">
    <meta charset="UTF-8">
    <title>Carrinho</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h1 class="text-center text-success mb-4">Seu Carrinho</h1>

        <?php if (empty($produtos_no_carrinho)) { ?>
            <div class="alert alert-info text-center">O carrinho está vazio.</div>
            <div class="text-center">
                <a href="produtos.php" class="btn btn-primary">Voltar à loja</a>
            </div>
        <?php } else { ?>
            <div class="card shadow p-4 mb-4">
                <ul class="list-group">
                    <?php foreach ($produtos_no_carrinho as $produto) { ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong><?php echo $produto['nome']; ?></strong><br>
                                Quantidade: <?php echo $produto['quantidade']; ?><br>
                                Preço unitário: R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?><br>
                                Subtotal: <strong>R$ <?php echo number_format($produto['subtotal'], 2, ',', '.'); ?></strong>
                            </div>
                        </li>
                    <?php } ?>
                </ul>

                <h4 class="text-end mt-4">Total: <span class="text-success">R$ <?php echo number_format($total, 2, ',', '.'); ?></span></h4>

                <div class="d-flex justify-content-between mt-4">
                    <a href="produtos.php" class="btn btn-outline-primary">Continuar comprando</a>
                    <a href="finalizar_compra.php" class="btn btn-success">Finalizar Compra</a>
                </div>
            </div>
        <?php } ?>
    </div>
    <?php include_once '../includes/footer.php';?>
</body>
</html>
