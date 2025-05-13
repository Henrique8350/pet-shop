<?php
// Conexão com o banco de dados
require_once __DIR__ . '/../includes/conexao.php';

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $senha_form = $_POST["senha"];
    $telefone = $_POST["telefone"];
    $endereco = $_POST["endereco"];

    // Criptografa a senha
    $senha_hash = password_hash($senha_form, PASSWORD_DEFAULT);

    // Prepara e executa a inserção no banco de dados
    $stmt = $conn->prepare("INSERT INTO clientes (nome, email, senha, telefone, endereco) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nome, $email, $senha_hash, $telefone, $endereco);

    if ($stmt->execute()) {
        echo "<h3>Cliente cadastrado com sucesso!</h3>";
    } else {
        echo "Erro ao cadastrar: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Cliente</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        form { max-width: 500px; }
        label { display: block; margin-top: 10px; }
        input, textarea {
            width: 100%; padding: 8px; box-sizing: border-box;
        }
        input[type="submit"] {
            margin-top: 15px; padding: 10px 20px;
        }
    </style>
</head>
<body>
    <h2>Cadastro de Cliente</h2>
    <form method="post" action="">
        <label for="nome">Nome:</label>
        <input type="text" name="nome" id="nome" required>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>

        <label for="senha">Senha:</label>
        <input type="password" name="senha" id="senha" required>

        <label for="telefone">Telefone:</label>
        <input type="tel" name="telefone" id="telefone">

        <label for="endereco">Endereço:</label>
        <textarea name="endereco" id="endereco"></textarea>

        <input type="submit" value="Cadastrar">
    </form>
</body>
</html>