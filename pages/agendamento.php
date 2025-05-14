<?php
include_once __DIR__ . '/../includes/header.php';

// Conexão com o banco de dados
require_once __DIR__ . '/../includes/conexao.php';


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
<head>
    <meta charset="UTF-8">
    <title>Agendamento Pet Shop</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 40px; background-color: #f0f8ff; }
        form { background: #fff; padding: 20px; max-width: 500px; margin: auto; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        label { display: block; margin-top: 10px; }
        input, select {
            width: 100%; padding: 8px; box-sizing: border-box;
        }
        input[type="submit"] {
            margin-top: 15px; padding: 10px 20px;
            background: #007BFF; color: white; border: none;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background: #0056b3;
        }
    </style>
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