<?php
session_start();
include_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/conexao.php';

// Verifica se o cliente está logado
if (!isset($_SESSION["cliente_id"])) {
    header("Location: login.php");
    exit();
}

$mensagem = '';
$mensagemTipo = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $especie = $_POST["especie"];
    $raca = $_POST["raca"];
    $idade = $_POST["idade"];
    $cliente_id = $_SESSION["cliente_id"];

    // Prepara e executa inserção
    $stmt = $conn->prepare("INSERT INTO pets (cliente_id, nome, especie, raca, idade) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isssi", $cliente_id, $nome, $especie, $raca, $idade);

    if ($stmt->execute()) {
        $mensagem = "Pet cadastrado com sucesso!";
        $mensagemTipo = "success";
    } else {
        $mensagem = "Erro ao cadastrar o pet: " . $stmt->error;
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
    <title>Adicionar Pet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5 col-md-6">
    <h2 class="text-center mb-4">Cadastrar Novo Pet</h2>

    <?php if (!empty($mensagem)): ?>
        <div class="alert alert-<?= $mensagemTipo ?>"><?= $mensagem ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="mb-3">
            <label for="nome" class="form-label">Nome do Pet:</label>
            <input type="text" name="nome" id="nome" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="especie" class="form-label">Espécie:</label>
            <input type="text" name="especie" id="especie" class="form-control">
        </div>
        <div class="mb-3">
            <label for="raca" class="form-label">Raça:</label>
            <input type="text" name="raca" id="raca" class="form-control">
        </div>
        <div class="mb-3">
            <label for="idade" class="form-label">Idade (anos):</label>
            <input type="number" name="idade" id="idade" class="form-control" min="0">
        </div>
        <button type="submit" class="btn btn-success w-100">Cadastrar Pet</button>
        <a href="perfil.php" class="btn btn-link w-100">Voltar ao Perfil</a>
    </form>
</div>
<?php include_once '../includes/footer.php'; ?>
</body>
</html>
