<?php
session_start();
include_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/conexao.php';

// Verifica se o cliente está logado
if (!isset($_SESSION["cliente_id"])) {
    header("Location: login.php");
    exit();
}

$cliente_id = $_SESSION["cliente_id"];

// Consulta os dados do cliente
$stmt = $conn->prepare("SELECT nome, email, telefone, endereco, data_nascimento, cpf, genero FROM clientes WHERE id_cliente = ?");
$stmt->bind_param("i", $cliente_id);
$stmt->execute();
$result = $stmt->get_result();
$cliente = $result->fetch_assoc();
$stmt->close();

// Consulta os pets do cliente
$pets = [];
$stmtPets = $conn->prepare("SELECT nome, especie, raca, idade FROM pets WHERE id_cliente = ?");
$stmtPets->bind_param("i", $cliente_id);
$stmtPets->execute();
$resultPets = $stmtPets->get_result();
while ($row = $resultPets->fetch_assoc()) {
    $pets[] = $row;
}
$stmtPets->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Perfil do Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f0f2f5;
            min-height: 100vh;
        }
        .profile-container {
            max-width: 900px;
            margin: 40px auto 60px;
            background: #fff;
            border-radius: 12px;
            padding: 30px 40px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.1);
        }
        h2 {
            color: #2c3e50;
            font-weight: 700;
        }
        .logout-btn {
            height: fit-content;
        }
        .section-title {
            margin-top: 40px;
            margin-bottom: 20px;
            color: #34495e;
            border-bottom: 2px solid #007bff;
            padding-bottom: 6px;
            font-weight: 600;
        }
        .list-group-item strong {
            color: #2c3e50;
        }
        .pet-list-item {
            font-size: 1rem;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        @media (max-width: 575.98px) {
            .profile-container {
                padding: 20px;
            }
            h2 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
<div class="profile-container">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <h2>Bem-vindo, <?= htmlspecialchars($cliente['nome']) ?>!</h2>
        <a href="/pet-shop/pages/logout.php" class="btn btn-danger logout-btn">Sair</a>
    </div>
    
    <h4 class="section-title">Seus dados</h4>
    <ul class="list-group shadow-sm">
        <li class="list-group-item"><strong>Nome completo:</strong> <?= htmlspecialchars($cliente['nome']) ?></li>
        <li class="list-group-item"><strong>Data de Nascimento:</strong> <?= htmlspecialchars(date('d/m/Y', strtotime($cliente['data_nascimento']))) ?></li>
        <li class="list-group-item"><strong>CPF:</strong> <?= htmlspecialchars($cliente['cpf']) ?></li>
        <li class="list-group-item"><strong>Gênero:</strong> <?= htmlspecialchars($cliente['genero']) ?></li>
        <li class="list-group-item"><strong>Email:</strong> <?= htmlspecialchars($cliente['email']) ?></li>
        <li class="list-group-item"><strong>Telefone:</strong> <?= htmlspecialchars($cliente['telefone']) ?></li>
        <li class="list-group-item"><strong>Endereço:</strong> <?= htmlspecialchars($cliente['endereco']) ?></li>
    </ul>

    <h4 class="section-title">Seus Pets</h4>
    <?php if (!empty($pets)): ?>
        <ul class="list-group shadow-sm">
            <?php foreach ($pets as $pet): ?>
                <li class="list-group-item pet-list-item d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
                    <div><strong>Nome:</strong> <?= htmlspecialchars($pet['nome']) ?></div>
                    <div><strong>Espécie:</strong> <?= htmlspecialchars($pet['especie']) ?></div>
                    <div><strong>Raça:</strong> <?= htmlspecialchars($pet['raca']) ?></div>
                    <div><strong>Idade:</strong> <?= htmlspecialchars($pet['idade']) ?> anos</div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="text-muted fst-italic">Você ainda não cadastrou nenhum pet.</p>
    <?php endif; ?>

    <a href="adicionar_pet.php" class="btn btn-primary mt-4">Cadastrar Novo Pet</a>
</div>

<?php include_once '../includes/footer.php'; ?>
</body>
</html>
