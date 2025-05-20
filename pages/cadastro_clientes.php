<?php
include_once __DIR__ . '/../includes/header.php';
// Conexão com o banco de dados
require_once __DIR__ . '/../includes/conexao.php';

$mensagem = '';
$mensagemTipo = '';

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
        $mensagem = "Cliente cadastrado com sucesso! Redirecionando para login...";
        $mensagemTipo = "success";

        // Redireciona após 2 segundos
        header("refresh:2;url=login.php");
    } else {
        $mensagem = "Erro ao cadastrar: " . $stmt->error;
        $mensagemTipo = "danger";
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container mt-5 col-md-6">
        <h2 class="text-center mb-4">Cadastro de Cliente</h2>

        <?php if (!empty($mensagem)): ?>
            <div class="alert alert-<?= $mensagemTipo ?>"><?= $mensagem ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome:</label>
                <input type="text" name="nome" id="nome" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="senha" class="form-label">Senha:</label>
                <input type="password" name="senha" id="senha" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="telefone" class="form-label">Telefone:</label>
                <input type="tel" name="telefone" id="telefone" class="form-control">
            </div>
            <div class="mb-3">
                <label for="endereco" class="form-label">Endereço:</label>
                <textarea name="endereco" id="endereco" class="form-control"></textarea>
            </div>
            <input type="submit" value="Cadastrar" class="btn btn-success w-100">
        </form>
    </div>
    <?php include_once '../includes/footer.php';?>
</body>
</html>
