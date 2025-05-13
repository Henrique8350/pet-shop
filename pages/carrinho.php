<?php
session_start();
require_once __DIR__ . '/../includes/conexao.php';

$carrinho = $_SESSION['carrinho'] ?? [];

$produtos_no_carrinho = [];

if (!empty($carrinho)) {
    $ids = implode(',', array_map('intval', array_keys($carrinho)));
    $sql = "SELECT * FROM produtos WHERE id_produto IN ($ids)";
    $result = $conn->query($sql);

    while ($produto = $result->fetch_assoc()) {
        $produto['quantidade'] = $carrinho[$produto['id_produto']];
        $produtos_no_carrinho[] = $produto;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Carrinho</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f6f6f6;
            margin: 0;
            padding: 20px;
        }
        h1 {
            color: #28a745;
            text-align: center;
        }
        ul {
            list-style: none;
            padding: 0;
            max-width: 600px;
            margin: 20px auto;
        }
        li {
            background-color: #fff;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }
        a {
            display: block;
            text-align: center;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            padding: 12px;
            width: 200px;
            margin: 20px auto;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        a:hover {
            background-color: #0056b3;
        }
        p {
            text-align: center;
            font-size: 1.1em;
        }
    </style>
</head>
<body>
    <h1>Seu Carrinho</h1>
    <?php if (empty($produtos_no_carrinho)) { ?>
        <p>O carrinho está vazio.</p>
    <?php } else { ?>
        <ul>
            <?php foreach ($produtos_no_carrinho as $produto) { ?>
                <li>
                    <strong><?php echo $produto['nome']; ?></strong><br>
                    Quantidade: <?php echo $produto['quantidade']; ?><br>
                    Preço Unitário: R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?>
                </li>
            <?php } ?>
        </ul>
        <a href="finalizar_compra.php">Finalizar Compra</a>
    <?php } ?>
</body>
</html>
