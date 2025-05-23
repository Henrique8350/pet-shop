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
<head class="style_cad_prod">
    <meta charset="UTF-8">
    <title>Cadastro de Produto</title>
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