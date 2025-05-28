<?php
session_start();
require_once __DIR__ . '/../includes/conexao.php';

// Verificar se o funcionário está logado
if (!isset($_SESSION['funcionario_id'])) {
    header('Location: login_funcionario.php');
    exit();
}

// Buscar todos os agendamentos
$sql = "SELECT * FROM agendamentos ORDER BY data_hora DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Agendamentos - Funcionários</title>
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
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            margin-top: 40px;
        }

        h2 {
            font-weight: 700;
            color: var(--dark-color);
        }

        .btn-back {
            background-color: var(--dark-color);
            color: white;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background-color: #23272b;
            color: white;
        }

        .table {
            border-radius: 15px;
            overflow: hidden;
        }

        .table thead {
            background-color: var(--dark-color);
            color: white;
        }

        .table tbody tr:hover {
            background-color: #f1f1f1;
        }

        .badge-warning {
            background-color: #ffc107;
            color: #212529;
            font-size: 0.9rem;
        }

        .badge-success {
            background-color: #28a745;
            font-size: 0.9rem;
        }

        .badge-secondary {
            background-color: #6c757d;
            font-size: 0.9rem;
        }

        .btn-status {
            background-color: var(--main-color);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 0.9rem;
            transition: background-color 0.3s ease;
        }

        .btn-status:hover {
            background-color: var(--hover-color);
        }

        @media (max-width: 768px) {
            h2 {
                font-size: 1.5rem;
            }
            .btn-back {
                padding: 6px 10px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>

<body>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>📅 Gerenciamento de Agendamentos</h2>
        <a href="dashboard.php" class="btn btn-back">⬅️ Voltar para Dashboard</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Data/Hora</th>
                    <th>Cliente</th>
                    <th>Pet</th>
                    <th>Serviço</th>
                    <th>Telefone</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id_agendamento']) ?></td>
                            <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($row['data_hora']))) ?></td>
                            <td><?= htmlspecialchars($row['nome_dono']) ?></td>
                            <td><?= htmlspecialchars($row['nome_pet']) ?></td>
                            <td><?= htmlspecialchars($row['servico_nome']) ?></td>
                            <td><?= htmlspecialchars($row['telefone']) ?></td>
                            <td>
                                <span class="badge 
                                    <?= 
                                        ($row['status'] == 'pendente' ? 'badge-warning' : 
                                        ($row['status'] == 'concluido' ? 'badge-success' : 'badge-secondary')) 
                                    ?>">
                                    <?= htmlspecialchars(ucfirst($row['status'])) ?>
                                </span>
                            </td>
                            <td>
                                <a href="atualizar_status.php?id=<?= $row['id_agendamento'] ?>" class="btn btn-status">
                                    🔄 Atualizar
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">Nenhum agendamento encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
