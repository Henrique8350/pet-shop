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
$stmt = $conn->prepare("SELECT nome, email, telefone, endereco FROM clientes WHERE id_cliente = ?");
$stmt->bind_param("i", $cliente_id);
$stmt->execute();
$result = $stmt->get_result();
$cliente = $result->fetch_assoc();
$stmt->close();

// Consulta os pets do cliente (se existir tabela pets)
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
</head>
<body>
<div class="container mt-5">
    <h2>Bem-vindo, <?= htmlspecialchars($cliente['nome']) ?>!</h2>
    <hr>
    <h4>Seus dados</h4>
    <ul class="list-group mb-4">
        <li class="list-group-item"><strong>Email:</strong> <?= htmlspecialchars($cliente['email']) ?></li>
        <li class="list-group-item"><strong>Telefone:</strong> <?= htmlspecialchars($cliente['telefone']) ?></li>
        <li class="list-group-item"><strong>Endereço:</strong> <?= htmlspecialchars($cliente['endereco']) ?></li>
    </ul>

    <h4>Seus Pets</h4>
    <?php if (!empty($pets)): ?>
        <ul class="list-group">
            <?php foreach ($pets as $pet): ?>
                <li class="list-group-item">
                    <strong>Nome:</strong> <?= htmlspecialchars($pet['nome']) ?> |
                    <strong>Espécie:</strong> <?= htmlspecialchars($pet['especie']) ?> |
                    <strong>Raça:</strong> <?= htmlspecialchars($pet['raca']) ?> |
                    <strong>Idade:</strong> <?= htmlspecialchars($pet['idade']) ?> anos
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="text-muted">Você ainda não cadastrou nenhum pet.</p>
    <?php endif; ?>

    <a href="adicionar_pet.php" class="btn btn-primary mt-3">Cadastrar Novo Pet</a>
</div>
<?php include_once '../includes/footer.php'; ?>
</body>
</html>
