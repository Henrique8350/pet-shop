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
    <link rel="stylesheet" href="../css/style.css">
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
