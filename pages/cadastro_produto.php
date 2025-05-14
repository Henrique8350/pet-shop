<?php
include_once __DIR__ . '/../includes/header.php';
// Conexão com o banco de dados
require_once __DIR__ . '/../includes/conexao.php';

// Processa o formulário
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $descricao = $_POST["descricao"];
    $preco = $_POST["preco"];
    $estoque = $_POST["estoque"];

    $stmt = $conn->prepare("INSERT INTO produtos (nome, descricao, preco, estoque) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssdi", $nome, $descricao, $preco, $estoque);

    if ($stmt->execute()) {
        echo "<h3>Produto cadastrado com sucesso!</h3>";
    } else {
        echo "Erro: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Produto</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 40px; background-color: #f4f4f4; }
        form { background: white; padding: 20px; max-width: 500px; margin: auto; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        label { display: block; margin-top: 10px; }
        input, textarea {
            width: 100%; padding: 8px; box-sizing: border-box;
        }
        input[type="submit"] {
            margin-top: 15px; padding: 10px 20px;
            background: #28a745; color: white; border: none;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Cadastro de Produto</h2>
    <form method="post" action="">
        <label for="nome">Nome do Produto:</label>
        <input type="text" name="nome" id="nome" required>

        <label for="descricao">Descrição:</label>
        <textarea name="descricao" id="descricao"></textarea>

        <label for="preco">Preço:</label>
        <input type="number" step="0.01" name="preco" id="preco" required>

        <label for="estoque">Estoque:</label>
        <input type="number" name="estoque" id="estoque" required>

        <input type="submit" value="Cadastrar Produto">
    </form>
    <?php include_once '../includes/footer.php';?>
</body>
</html>