<?php
session_start();
require_once __DIR__ . '/../includes/conexao.php';

if (!isset($_SESSION['funcionario_id'])) {
    header('Location: login_funcionario.php');
    exit();
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    die('ID inválido!');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $novo_status = $_POST['status'] ?? 'pendente';

    $stmt = $conn->prepare("UPDATE agendamentos SET status = ? WHERE id_agendamento = ?");
    $stmt->bind_param('si', $novo_status, $id);
    $stmt->execute();
    $stmt->close();

    header('Location: agendamento_funcionario.php');
    exit();
}

// Buscar agendamento atual
$stmt = $conn->prepare("SELECT * FROM agendamentos WHERE id_agendamento = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$agendamento = $result->fetch_assoc();
$stmt->close();

if (!$agendamento) {
    die('Agendamento não encontrado!');
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Atualizar Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --main-color: #4CAF50;
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
            margin-bottom: 30px;
        }

        label {
            font-weight: 600;
        }

        .form-select {
            border-radius: 10px;
        }

        .btn-save {
            background-color: var(--main-color);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 8px 18px;
            transition: background-color 0.3s ease;
        }

        .btn-save:hover {
            background-color: var(--hover-color);
        }

        .btn-cancel {
            background-color: var(--dark-color);
            color: white;
            border-radius: 10px;
            padding: 8px 18px;
            transition: background-color 0.3s ease;
        }

        .btn-cancel:hover {
            background-color: #23272b;
            color: white;
        }
    </style>
</head>

<body>
<div class="container">
    <h2>🔄 Atualizar Status do Agendamento</h2>
    <form method="POST">
        <div class="mb-4">
            <label class="form-label">Status do Agendamento:</label>
            <select name="status" class="form-select" required>
                <option value="pendente" <?= $agendamento['status'] === 'pendente' ? 'selected' : '' ?>>🕗 Pendente</option>
                <option value="em andamento" <?= $agendamento['status'] === 'em andamento' ? 'selected' : '' ?>>🔧 Em Andamento</option>
                <option value="concluido" <?= $agendamento['status'] === 'concluido' ? 'selected' : '' ?>>✅ Concluído</option>
            </select>
        </div>
        <div class="d-flex gap-3">
            <button type="submit" class="btn btn-save">💾 Salvar</button>
            <a href="agendamento_funcionario.php" class="btn btn-cancel">❌ Cancelar</a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
