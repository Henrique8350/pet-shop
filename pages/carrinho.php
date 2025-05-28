<?php
session_start();
include_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/conexao.php';

$carrinho = $_SESSION['carrinho'] ?? [];
$produtos_no_carrinho = [];
$total = 0;

if (!empty($carrinho)) {
    // Sanitiza e monta a lista de IDs para consulta SQL
    $ids = implode(',', array_map('intval', array_keys($carrinho)));
    $sql = "SELECT * FROM produtos WHERE id_produto IN ($ids)";
    $result = $conn->query($sql);

    if ($result) {
        while ($produto = $result->fetch_assoc()) {
            $quantidade = $carrinho[$produto['id_produto']];
            $produto['quantidade'] = $quantidade;
            $produto['subtotal'] = $produto['preco'] * $quantidade;
            $total += $produto['subtotal'];
            $produtos_no_carrinho[] = $produto;
        }
    } else {
        echo "<div class='alert alert-danger'>Erro ao buscar os produtos do carrinho.</div>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <title>Carrinho - Pet Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body.bg-light {
            background: #f8fafc;
        }

        h1.text-success {
            font-weight: 700;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            text-shadow: 1px 1px 2px #d4edda;
        }

        .card.shadow {
            border-radius: 15px;
            border: none;
        }

        .list-group-item {
            border: none;
            border-bottom: 1px solid #e3e6f0;
            padding: 15px 20px;
            transition: background-color 0.3s ease;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .list-group-item:last-child {
            border-bottom: none;
        }

        .list-group-item:hover {
            background-color: #f1f5fb;
        }

        .list-group-item strong {
            font-size: 1.1rem;
            color: #212529;
        }

        h4.text-end {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-weight: 700;
            letter-spacing: 0.03em;
        }

        .btn-outline-primary {
            border-radius: 30px;
            padding: 10px 30px;
            font-weight: 600;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .btn-outline-primary:hover {
            background-color: #0d6efd;
            color: white;
        }

        .btn-success {
            border-radius: 30px;
            padding: 10px 30px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgb(25 135 84 / 0.4);
            transition: box-shadow 0.3s ease;
        }

        .btn-success:hover {
            box-shadow: 0 6px 18px rgb(25 135 84 / 0.7);
        }

        .alert-info {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 1.1rem;
        }
    </style>
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
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-column flex-md-row">
                            <div>
                                <strong><?php echo htmlspecialchars($produto['nome']); ?></strong><br />
                                Quantidade: <?php echo intval($produto['quantidade']); ?><br />
                                Preço unitário: R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?><br />
                                Subtotal: <strong>R$ <?php echo number_format($produto['subtotal'], 2, ',', '.'); ?></strong>
                            </div>
                        </li>
                    <?php } ?>
                </ul>

                <h4 class="text-end mt-4">Total: <span class="text-success">R$ <?php echo number_format($total, 2, ',', '.'); ?></span></h4>

                <div class="d-flex justify-content-between mt-4 flex-column flex-md-row gap-2">
                    <a href="produtos.php" class="btn btn-outline-primary">Continuar comprando</a>
                    <a href="finalizar_compra.php" class="btn btn-success">Finalizar Compra</a>
                </div>
            </div>
        <?php } ?>
    </div>

    <?php include_once '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
