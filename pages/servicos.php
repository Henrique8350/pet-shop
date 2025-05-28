<?php

session_start();

include_once __DIR__ . '/../includes/header.php';
// Conexão com o banco
require_once __DIR__ . '/../includes/conexao.php';


if (!isset($_SESSION["cliente_id"])) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT * FROM servicos";
$result = $conn->query($sql);

$mensagem = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_pet = $_POST["nome_pet"];
    $nome_dono = $_POST["nome_dono"];
    $telefone = $_POST["telefone"];
    $servico_id = $_POST["servico"];
    $data_hora = $_POST["data_hora"];

    $sql_servico = "SELECT * FROM servicos WHERE id_servico = ?";
    $stmt = $conn->prepare($sql_servico);
    $stmt->bind_param("i", $servico_id);
    $stmt->execute();
    $servico = $stmt->get_result()->fetch_assoc();
    $servico_nome = $servico['nome'];

    $stmt = $conn->prepare("INSERT INTO agendamentos (nome_pet, nome_dono, telefone, servico, data_hora) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nome_pet, $nome_dono, $telefone, $servico_nome, $data_hora);

    if ($stmt->execute()) {
        $mensagem = "Agendamento realizado com sucesso!";
    } else {
        $mensagem = "Erro ao agendar: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <title>Reservar Serviço - Pet Shop</title>
    <link rel="stylesheet" href="../css/style.css" />
    <style>
        body {
            background: #f4f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 30px 15px;
            color: #333;
        }
        h2 {
            text-align: center;
            color: #007bff;
            margin-bottom: 25px;
            user-select: none;
        }
        form {
            background: white;
            max-width: 450px;
            margin: 0 auto;
            padding: 25px 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: #555;
            user-select: none;
        }
        input[type="text"],
        input[type="tel"],
        input[type="datetime-local"],
        select {
            width: 100%;
            padding: 12px 14px;
            margin-bottom: 20px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        input[type="text"]:focus,
        input[type="tel"]:focus,
        input[type="datetime-local"]:focus,
        select:focus {
            border-color: #007bff;
            outline: none;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            width: 100%;
            padding: 14px 0;
            font-size: 1.1rem;
            font-weight: 700;
            border-radius: 50px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            user-select: none;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        /* Mensagem de sucesso/erro */
        .mensagem {
            max-width: 450px;
            margin: 20px auto 0;
            padding: 15px 20px;
            border-radius: 10px;
            font-weight: 600;
            text-align: center;
            user-select: none;
        }
        .mensagem.sucesso {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .mensagem.erro {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        /* Responsividade */
        @media (max-width: 480px) {
            body {
                padding: 15px 10px;
            }
            form {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

    <h2>Reservar Serviço para o Pet</h2>

    <?php if($mensagem): ?>
        <div class="mensagem <?php echo strpos($mensagem, 'sucesso') !== false ? 'sucesso' : 'erro'; ?>">
            <?php echo htmlspecialchars($mensagem); ?>
        </div>
    <?php endif; ?>

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
                    <?php echo htmlspecialchars($servico['nome']); ?> - R$ <?php echo number_format($servico['preco'], 2, ',', '.'); ?>
                </option>
            <?php } ?>
        </select>

        <label for="data_hora">Data e Hora:</label>
        <input type="datetime-local" name="data_hora" id="data_hora" required>

        <input type="submit" value="Reservar Serviço">
    </form>

    <?php include_once '../includes/footer.php'; ?>
</body>
</html>
