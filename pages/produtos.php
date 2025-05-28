<?php
session_start();
include_once __DIR__ . '/../includes/header.php';

// Conexão com o banco de dados
require_once __DIR__ . '/../includes/conexao.php';

if (!isset($_SESSION["cliente_id"])) {
    header("Location: login.php");
    exit();
}

// Buscar todos os produtos
$sql = "SELECT * FROM produtos";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Produtos - Pet Shop</title>
    
    <style>
        /* Reset básico */
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        header h1 {
            font-weight: 700;
            color: #007bff;
            margin: 0;
            user-select: none;
        }
        .carrinho-link {
            background-color: #ffc107;
            color: #333;
            text-decoration: none;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 25px;
            box-shadow: 0 4px 10px rgba(255, 193, 7, 0.4);
            transition: background-color 0.3s ease;
            user-select: none;
        }
        .carrinho-link:hover {
            background-color: #e0a800;
            color: #222;
        }
        
        .container {
            display: grid;
            grid-template-columns: repeat(auto-fit,minmax(280px,1fr));
            gap: 25px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .produto {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .produto:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.15);
        }
        
        .produto img {
            width: 100%;
            max-height: 180px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 15px;
            user-select: none;
        }
        
        .produto h3 {
            margin: 0 0 10px;
            color: #007bff;
            font-weight: 700;
            text-align: center;
            user-select: none;
        }
        
        .produto p {
            margin: 5px 0;
            font-size: 0.95rem;
            text-align: center;
            color: #555;
        }
        
        .produto .preco {
            font-weight: 700;
            color: #28a745;
            font-size: 1.2rem;
            margin: 10px 0 15px;
            user-select: none;
        }
        
        form {
            width: 100%;
            text-align: center;
        }
        
        form button {
            background-color: #007bff;
            border: none;
            padding: 12px 25px;
            border-radius: 30px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s ease;
            user-select: none;
        }
        
        form button:hover {
            background-color: #0056b3;
        }
        
        /* Mensagem caso não tenha produtos */
        p {
            font-size: 1.1rem;
            color: #888;
            text-align: center;
            margin-top: 50px;
            user-select: none;
        }

        /* Responsividade */
        @media (max-width: 480px) {
            header {
                flex-direction: column;
                gap: 15px;
            }
            .carrinho-link {
                width: 100%;
                text-align: center;
            }
            form button {
                padding: 10px;
            }
        }
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
                    <img src="<?php echo htmlspecialchars($produto['imagem_url']); ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>">
                    <h3><?php echo htmlspecialchars($produto['nome']); ?></h3>
                    <p><strong>Categoria:</strong> <?php echo htmlspecialchars($produto['categoria']); ?></p>
                    <p><?php echo htmlspecialchars($produto['descricao']); ?></p>
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
