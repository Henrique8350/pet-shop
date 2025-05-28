<?php
session_start();
require_once __DIR__ . '/../includes/conexao.php';

// Verificar se o funcionário está logado
if (!isset($_SESSION['funcionario_id'])) {
    header("Location: login_funcionario.php");
    exit();
}

// Escapa o nome do funcionário para segurança (contra XSS)
$funcionario_nome = htmlspecialchars($_SESSION['funcionario_nome'] ?? 'Funcionário');
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel do Funcionário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>👋 Bem-vindo, <?= $funcionario_nome ?>!</h2>
    <hr>
    <div class="d-flex gap-3 flex-wrap">
        <a href="agendamento_funcionario.php" class="btn btn-primary">📅 Gerenciar Agendamentos</a>
        <a href="servicos.php" class="btn btn-info">🔧 Progresso dos Serviços</a>
        <a href="logout_funcionario.php" class="btn btn-danger">🚪 Sair</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
