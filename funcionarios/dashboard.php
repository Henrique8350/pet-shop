<?php
session_start();
require_once __DIR__ . '/../includes/conexao.php';

// Verificar se o funcionário está logado
if (!isset($_SESSION['funcionario_id'])) {
    header('Location: ../login.php');
    exit();
}

// Buscar informações do funcionário logado
$funcionario_id = $_SESSION['funcionario_id'];
$stmt = $conn->prepare("SELECT nome FROM funcionarios WHERE id_funcionario = ?");
$stmt->bind_param("i", $funcionario_id);
$stmt->execute();
$result = $stmt->get_result();
$funcionario = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Funcionário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #4CAF50;
            --dark-color: #343a40;
            --light-bg: #f4f6f9;
            --card-bg: #fff;
            --hover-color: #3e8e41;
        }

        body {
            background-color: var(--light-bg);
            font-family: 'Segoe UI', sans-serif;
        }

        .container {
            background-color: var(--card-bg);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            margin-top: 60px;
            max-width: 600px;
        }

        h2 {
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 10px;
        }

        p {
            color: #555;
            margin-bottom: 30px;
        }

        .list-group-item {
            border-radius: 12px;
            margin-bottom: 12px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .list-group-item:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .list-group-item.text-danger:hover {
            background-color: #dc3545;
        }

    </style>
</head>
<body>
<div class="container text-center">
    <h2>👋 Bem-vindo, <?= htmlspecialchars($funcionario['nome']) ?>!</h2>
    <p>Esse é o seu painel de controle.</p>

    <div class="list-group">
        <a href="cadastro_funcionario.php" class="list-group-item list-group-item-action">
            🧑‍💼 Cadastrar Funcionário
        </a>
        <a href="listar_funcionarios.php" class="list-group-item list-group-item-action">
            📄 Listar Funcionários
        </a>
        <a href="agendamento_funcionario.php" class="list-group-item list-group-item-action">
            📅 Gerenciar Agendamentos
        </a>
        <a href="logout_funcionario.php" class="list-group-item list-group-item-action text-danger">
            🚪 Sair
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
