<?php
include_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/conexao.php';

if (!isset($_SESSION["cliente_id"])) {
    header("Location: login.php");
    exit();
}

$id_cliente = $_SESSION["cliente_id"];

// Buscar serviços
$servicos = [];
$result_servico = $conn->query("SELECT id_servico, nome FROM servicos");
if ($result_servico && $result_servico->num_rows > 0) {
    while ($row = $result_servico->fetch_assoc()) {
        $servicos[] = $row;
    }
}

$message = '';
$messageClass = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_pet = trim($_POST["nome_pet"]) ?? null;
    $id_servico = $_POST["id_servico"] ?? null;
    $nome_dono = trim($_POST["nome_dono"]);
    $telefone = trim($_POST["telefone"]);
    $data_hora = $_POST["data_hora"];

    if (!$nome_pet || !$id_servico) {
        $message = "Por favor, preencha o nome do pet e selecione o serviço!";
        $messageClass = "warning";
    } else {
        // Verificar se pet já existe para esse cliente
        $stmt = $conn->prepare("SELECT id_pet FROM pets WHERE nome = ? AND id_cliente = ?");
        $stmt->bind_param("si", $nome_pet, $id_cliente);
        $stmt->execute();
        $stmt->bind_result($id_pet);
        $stmt->fetch();
        $stmt->close();

        // Se não existe, insere novo pet
        if (!$id_pet) {
            $stmt = $conn->prepare("INSERT INTO pets (nome, id_cliente) VALUES (?, ?)");
            $stmt->bind_param("si", $nome_pet, $id_cliente);
            if (!$stmt->execute()) {
                $message = "Erro ao cadastrar pet: " . $stmt->error;
                $messageClass = "danger";
                $stmt->close();
            } else {
                $id_pet = $stmt->insert_id;
                $stmt->close();
            }
        }

        if ($id_pet) {
            // Insere o agendamento
            $stmt = $conn->prepare(
                "INSERT INTO agendamentos 
                (id_cliente, id_pet, id_servico, nome_dono, telefone, data_hora) 
                VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("iiisss", $id_cliente, $id_pet, $id_servico, $nome_dono, $telefone, $data_hora);

            if ($stmt->execute()) {
                $message = "Agendamento realizado com sucesso!";
                $messageClass = "success";
            } else {
                $message = "Erro ao agendar: " . $stmt->error;
                $messageClass = "danger";
            }

            $stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <title>Agendamento Pet Shop</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <style>
        /* Reset básico */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
        }

        h2 {
            text-align: center;
            margin-top: 40px;
            margin-bottom: 30px;
            color: #2c3e50;
        }

        form {
            background: #fff;
            max-width: 480px;
            margin: 0 auto 40px;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #34495e;
        }

        input[type="text"],
        input[type="tel"],
        input[type="datetime-local"],
        select {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 20px;
            border: 1.8px solid #ddd;
            border-radius: 6px;
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
            padding: 14px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        /* Mensagens */
        .message {
            max-width: 480px;
            margin: 20px auto;
            padding: 15px 20px;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
            font-size: 1.1rem;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1.5px solid #c3e6cb;
        }
        .danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1.5px solid #f5c6cb;
        }
        .warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1.5px solid #ffeeba;
        }

        @media (max-width: 540px) {
            form {
                padding: 25px 20px;
            }
        }
    </style>
</head>
<body>

    <h2>Agendamento para Pet Shop</h2>

    <?php if ($message): ?>
        <div class="message <?= $messageClass ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <label for="nome_pet">Nome do Pet:</label>
        <input type="text" name="nome_pet" id="nome_pet" required>

        <label for="id_servico">Selecione o Serviço:</label>
        <select name="id_servico" id="id_servico" required>
            <option value="">Selecione</option>
            <?php foreach ($servicos as $servico) : ?>
                <option value="<?= htmlspecialchars($servico['id_servico']) ?>">
                    <?= htmlspecialchars($servico['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="nome_dono">Nome do Dono:</label>
        <input type="text" name="nome_dono" id="nome_dono" required>

        <label for="telefone">Telefone:</label>
        <input type="tel" name="telefone" id="telefone" required>

        <label for="data_hora">Data e Hora:</label>
        <input type="datetime-local" name="data_hora" id="data_hora" required>

         <img src= "../assets/img/pet/2.png"  class="calendario"  alt=" calendário pet shop"> 

        <input type="submit" value="Agendar">
        
    </form>

<?php include_once '../includes/footer.php'; ?>

</body>
</html>
