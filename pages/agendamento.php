<?php
include_once __DIR__ . '/../includes/header.php';

// Conexão com o banco de dados
require_once __DIR__ . '/../includes/conexao.php';


session_start();

if (!isset($_SESSION["cliente_id"])) {
    // Redireciona para a página de login
    header("Location: login.php");
    exit();
}




if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_pet = $_POST["nome_pet"];
    $nome_dono = $_POST["nome_dono"];
    $telefone = $_POST["telefone"];
    $servico = $_POST["servico"];
    $data_hora = $_POST["data_hora"];

    $stmt = $conn->prepare("INSERT INTO agendamentos (nome_pet, nome_dono, telefone, servico, data_hora) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nome_pet, $nome_dono, $telefone, $servico, $data_hora);

    if ($stmt->execute()) {
        echo "<h3>Agendamento realizado com sucesso!</h3>";
    } else {
        echo "Erro ao agendar: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head class="style_agen">
    <meta charset="UTF-8">
    <title>Agendamento Pet Shop</title>
</head>
<body>
    <h2 style="text-align:center;">Agendamento para Pet Shop</h2>
    <form method="post" action="">
        <label for="nome_pet">Nome do Pet:</label>
        <input type="text" name="nome_pet" id="nome_pet" required>

        <label for="nome_dono">Nome do Dono:</label>
        <input type="text" name="nome_dono" id="nome_dono" required>

        <label for="telefone">Telefone:</label>
        <input type="tel" name="telefone" id="telefone">

        <label for="servico">Serviço:</label>
        <select name="servico" id="servico" required>
            <option value="">Selecione</option>
            <option value="Banho">Banho</option>
            <option value="Tosa">Tosa</option>
            <option value="Vacinação">Vacinação</option>
            <option value="Consulta Veterinária">Consulta Veterinária</option>
        </select>

        <label for="data_hora">Data e Hora:</label>
        <input type="datetime-local" name="data_hora" id="data_hora" required>

        <input type="submit" value="Agendar">
    </form>
    <?php include_once '../includes/footer.php';?>
</body>
</html>