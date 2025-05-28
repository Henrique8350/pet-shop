<?php
session_start();
require_once __DIR__ . '/../includes/conexao.php';

// Verificar se o funcionário está logado
if (!isset($_SESSION['funcionario_id'])) {
    header('Location: login_funcionario.php');
    exit();
}

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST["nome"]);
    $email = trim($_POST["email"]);
    $senha = password_hash($_POST["senha"], PASSWORD_DEFAULT);

    // Verificar se o email já está cadastrado
    $verificar = $conn->prepare("SELECT id_funcionario FROM funcionarios WHERE email = ?");
    $verificar->bind_param("s", $email);
    $verificar->execute();
    $resultado = $verificar->get_result();

    if ($resultado->num_rows > 0) {
        $msg = "<div class='alert alert-danger'>Email já cadastrado!</div>";
    } else {
        $stmt = $conn->prepare("INSERT INTO funcionarios (nome, email, senha) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nome, $email, $senha);

        if ($stmt->execute()) {
            $msg = "<div class='alert alert-success'>Funcionário cadastrado com sucesso!</div>";
        } else {
            $msg = "<div class='alert alert-danger'>Erro ao cadastrar funcionário.</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Funcionário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .cadastro-container {
            max-width: 500px;
            margin: 80px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<div class="cadastro-container">
    <h2 class="text-center mb-4">Cadastro de Funcionário</h2>

    <?= $msg ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Nome:</label>
            <input type="text" name="nome" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email:</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Senha:</label>
            <input type="password" name="senha" class="form-control" required>
        </div>

        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-primary">Cadastrar</button>
            <a href="dashboard.php" class="btn btn-secondary">Voltar</a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
