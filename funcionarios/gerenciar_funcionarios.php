<?php
session_start();
require_once __DIR__ . '/../includes/conexao.php';

// Verificar login
if (!isset($_SESSION['funcionario_id'])) {
    header("Location: login_funcionario.php");
    exit();
}

// Verificar se é admin
if ($_SESSION['funcionario_cargo'] != 'Administrador') {
    echo "🚫 Acesso negado. Esta página é restrita ao administrador.";
    exit();
}

// Buscar funcionários
$sql = "SELECT id, nome, cpf, cargo, data_nascimento, data_cadastro FROM funcionarios";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Funcionários</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">👥 Painel de Funcionários</h2>

    <div class="mb-3 d-flex gap-3">
        <a href="painel_funcionario.php" class="btn btn-secondary">🔙 Voltar ao Painel</a>
        <a href="cadastrar_funcionario.php" class="btn btn-success">➕ Cadastrar Novo Funcionário</a>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            📊 Relatório Geral
        </div>
        <div class="card-body">
            <canvas id="grafico"></canvas>
        </div>
    </div>

    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>Nome</th>
                <th>CPF</th>
                <th>Cargo</th>
                <th>Data de Nascimento</th>
                <th>Idade</th>
                <th>Data de Cadastro</th>
                <th>Concluídos</th>
                <th>Em Progresso</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total_concluidos = 0;
            $total_progresso = 0;

            while ($row = $result->fetch_assoc()): 
                // Calcular idade
                $nascimento = new DateTime($row['data_nascimento']);
                $hoje = new DateTime();
                $idade = $nascimento->diff($hoje)->y;

                $id_func = $row['id'];

                // Trabalhos concluídos
                $sql_concluidos = "SELECT COUNT(*) AS total FROM servicos WHERE funcionario_id = $id_func AND status = 'Concluído'";
                $res_concluidos = $conn->query($sql_concluidos);
                $concluidos = $res_concluidos->fetch_assoc()['total'] ?? 0;
                $total_concluidos += $concluidos;

                // Trabalhos em progresso
                $sql_progresso = "SELECT COUNT(*) AS total FROM servicos WHERE funcionario_id = $id_func AND status = 'Em Progresso'";
                $res_progresso = $conn->query($sql_progresso);
                $progresso = $res_progresso->fetch_assoc()['total'] ?? 0;
                $total_progresso += $progresso;
            ?>
            <tr>
                <td><?= htmlspecialchars($row['nome']) ?></td>
                <td><?= htmlspecialchars($row['cpf']) ?></td>
                <td><?= htmlspecialchars($row['cargo']) ?></td>
                <td><?= htmlspecialchars(date('d/m/Y', strtotime($row['data_nascimento']))) ?></td>
                <td><?= $idade ?> anos</td>
                <td><?= htmlspecialchars(date('d/m/Y', strtotime($row['data_cadastro']))) ?></td>
                <td><?= $concluidos ?></td>
                <td><?= $progresso ?></td>
                <td>
                    <a href="editar_funcionario.php?id=<?= $id_func ?>" class="btn btn-sm btn-warning">✏️ Editar</a>
                    <a href="excluir_funcionario.php?id=<?= $id_func ?>" 
                       class="btn btn-sm btn-danger"
                       onclick="return confirm('Tem certeza que deseja excluir <?= htmlspecialchars($row['nome']) ?>?')">
                       🗑️ Excluir
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
const ctx = document.getElementById('grafico');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Trabalhos Concluídos', 'Trabalhos em Progresso'],
        datasets: [{
            label: 'Quantidade',
            data: [<?= $total_concluidos ?>, <?= $total_progresso ?>],
            backgroundColor: ['#198754', '#0dcaf0']
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                precision: 0
            }
        }
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
