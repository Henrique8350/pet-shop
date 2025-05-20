<?php
include_once __DIR__ . '/../includes/header.php';
// Conexão com o banco
require_once __DIR__ . '/../includes/conexao.php';

// Buscar todos os serviços disponíveis
$sql = "SELECT * FROM servicos";
$result = $conn->query($sql);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Pegando os dados do formulário
    $nome_pet = $_POST["nome_pet"];
    $nome_dono = $_POST["nome_dono"];
    $telefone = $_POST["telefone"];
    $servico_id = $_POST["servico"];
    $data_hora = $_POST["data_hora"];

    // Verificando o preço e duração do serviço
    $sql_servico = "SELECT * FROM servicos WHERE id_servico = ?";
    $stmt = $conn->prepare($sql_servico);
    $stmt->bind_param("i", $servico_id);
    $stmt->execute();
    $servico = $stmt->get_result()->fetch_assoc();
    $servico_nome = $servico['nome'];
    $servico_preco = $servico['preco'];
    $servico_duracao = $servico['duracao'];

    // Inserir o agendamento
    $stmt = $conn->prepare("INSERT INTO agendamentos (nome_pet, nome_dono, telefone, servico, data_hora) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nome_pet, $nome_dono, $telefone, $servico_nome, $data_hora);

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
    <title>Reservar Serviço - Pet Shop</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <h2 style="text-align: center;">Reservar Serviço para o Pet</h2>

    <form method="post" action="">
        <label for="nome_pet">Nome do Pet:</label>
        <input type="text" name="nome_pet" id="nome_pet" required>

        <label for="nome_dono">Nome do Dono:</label>
        <input type="text" name="nome_dono" id="nome_dono" required>

        <label for="telefone">Telefone:</label>
        <input type="tel" name="telefone" id="telefone" required>

        <label for="servico">Serviço:</label>
        <select name="servico" id="servico" required>
            <option value="">Selecione um Serviço</option>
            <?php while ($servico = $result->fetch_assoc()) { ?>
                <option value="<?php echo $servico['id_servico']; ?>">
                    <?php echo $servico['nome']; ?> - R$ <?php echo number_format($servico['preco'], 2, ',', '.'); ?>
                </option>
            <?php } ?>
        </select>

        <label for="data_hora">Data e Hora:</label>
        <input type="datetime-local" name="data_hora" id="data_hora" required>

        <input type="submit" value="Reservar Serviço">
    </form>
    <?php include_once '../includes/footer.php';?>
</body>
</html>
