<?php
session_start();
include_once __DIR__ . '/../includes/header.php';

// Conexão com o banco de dados
require_once __DIR__ . '/../includes/conexao.php';

// Buscar todos os produtos
$sql = "SELECT * FROM produtos";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos - Pet Shop</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        header { background: #28a745; padding: 20px; text-align: center; color: white; position: relative; }
        .carrinho-link {
            position: absolute;
            right: 20px;
            top: 20px;
            color: white;
            text-decoration: underline;
            font-weight: bold;
        }
        .container { width: 90%; max-width: 1200px; margin: 20px auto; display: flex; flex-wrap: wrap; justify-content: space-between; }
        .produto { background: white; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); width: 30%; margin-bottom: 20px; padding: 20px; border-radius: 8px; text-align: center; }
        .produto img { width: 100%; height: auto; border-radius: 8px; }
        .produto h3 { font-size: 1.2em; margin-top: 10px; }
        .produto p { color: #555; margin: 10px 0; }
        .produto .preco { font-size: 1.5em; color: #28a745; font-weight: bold; }
        .produto button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .produto button:hover { background-color: #0056b3; }
    </style>
</head>
<body>

    <header>
        <h1>Produtos - Pet Shop</h1>
        <a class="carrinho-link" href="carrinho.php">🛒 Ver Carrinho</a>
    </header>

    <div class="container">
        <?php if ($result->num_rows > 0) { ?>
            <?php while ($produto = $result->fetch_assoc()) { ?>
                <div class="produto">
                    <img src="<?php echo $produto['imagem_url']; ?>" alt="<?php echo $produto['nome']; ?>">
                    <h3><?php echo $produto['nome']; ?></h3>
                    <p><strong>Categoria:</strong> <?php echo $produto['categoria']; ?></p>
                    <p><?php echo $produto['descricao']; ?></p>
                    <p class="preco">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                    <form action="adicionar_carrinho.php" method="post">
                        <input type="hidden" name="id" value="<?php echo $produto['id_produto']; ?>">
                        <button type="submit">Adicionar ao Carrinho</button>
                    </form>
                </div>
            <?php } ?>
        <?php } else { ?>
            <p>Nenhum produto disponível no momento.</p>
        <?php } ?>
    </div>
    <?php include_once '../includes/footer.php';?>
</body>
</html>
