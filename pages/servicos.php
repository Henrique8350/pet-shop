<?php
session_start();

// Conexão com o banco (antes de qualquer output para redirecionamento)
require_once __DIR__ . '/../includes/conexao.php';

// Verifica conexão (se conexao.php já faz isso, pode ser redundante)
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// --- Lógica de Validação de Login e Redirecionamento ---
if (!isset($_SESSION["cliente_id"])) {
    header("Location: login.php"); // Redireciona para a página de login
    exit(); // Crucial: parar a execução após o redirecionamento
}

$id_cliente = $_SESSION["cliente_id"]; // Obtém o ID do cliente logado

// --- Processamento do Formulário de Reserva ---
$mensagem = '';
$mensagemClass = ''; // Usar 'sucesso', 'erro', 'warning' para classes CSS

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_pet = trim($_POST["nome_pet"]) ?? null;
    $nome_dono = trim($_POST["nome_dono"]); // 'nome_dono' e 'telefone' vêm do formulário
    $telefone = trim($_POST["telefone"]);
    $id_servico = $_POST["servico"] ?? null; // 'servico' no formulário é 'id_servico'
    $data_hora = $_POST["data_hora"];

    // Validação básica dos campos
    if (!$nome_pet || !$id_servico || !$nome_dono || !$telefone || !$data_hora) {
        $mensagem = "Por favor, preencha todos os campos obrigatórios!";
        $mensagemClass = "warning";
    } else {
        // Converter o formato de datetime-local para o formato do banco de dados (YYYY-MM-DD HH:MM:SS)
        $data_hora_db_format = str_replace('T', ' ', $data_hora) . ':00';

        // Opcional: Re-verificação de disponibilidade (se você quer que servicos.php também verifique)
        // Isso requer que $limite_vagas_por_horario esteja definido ou seja buscado.
        // Por simplicidade aqui, estou comentando a re-verificação para focar na inserção.
        // Se você quiser a verificação aqui, copie o bloco do agendamento.php.

        // Lógica para verificar/cadastrar PET (copiada do agendamento.php)
        $id_pet = null;
        $stmt = $conn->prepare("SELECT id_pet FROM pets WHERE nome = ? AND id_cliente = ?");
        if ($stmt) {
            $stmt->bind_param("si", $nome_pet, $id_cliente);
            $stmt->execute();
            $stmt->bind_result($id_pet);
            $stmt->fetch();
            $stmt->close();
        } else {
            $mensagem = "Erro ao preparar consulta de pet existente: " . $conn->error;
            $mensagemClass = "erro";
        }

        if (!$id_pet && empty($mensagem)) { // Só tenta inserir se não houve erro na consulta anterior
            $stmt = $conn->prepare("INSERT INTO pets (nome, id_cliente) VALUES (?, ?)");
            if ($stmt) {
                $stmt->bind_param("si", $nome_pet, $id_cliente);
                if (!$stmt->execute()) {
                    $mensagem = "Erro ao cadastrar pet: " . $stmt->error;
                    $mensagemClass = "erro";
                } else {
                    $id_pet = $stmt->insert_id;
                }
                $stmt->close();
            } else {
                $mensagem = "Erro ao preparar cadastro de pet: " . $conn->error;
                $mensagemClass = "erro";
            }
        }

        // Insere o agendamento (SE o pet foi encontrado/cadastrado e não houve erros)
        if ($id_pet && empty($mensagem)) {
            $stmt = $conn->prepare(
                "INSERT INTO agendamentos 
                (id_cliente, id_pet, id_servico, nome_dono, telefone, data_hora, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            if ($stmt) {
                $status = 'pendente'; // Define um status inicial para o agendamento
                $stmt->bind_param("iiissss", $id_cliente, $id_pet, $id_servico, $nome_dono, $telefone, $data_hora_db_format, $status);

                if ($stmt->execute()) {
                    $mensagem = "Agendamento realizado com sucesso!";
                    $mensagemClass = "sucesso";
                    // Opcional: Redirecionar após o sucesso
                    // header("Location: agendamento_confirmado.php?id=" . $conn->insert_id);
                    // exit();
                } else {
                    $mensagem = "Erro ao agendar: " . $stmt->error;
                    $mensagemClass = "erro";
                }
                $stmt->close();
            } else {
                $mensagem = "Erro ao preparar agendamento: " . $conn->error;
                $mensagemClass = "erro";
            }
        }
    }
}

// --- FIM DO PROCESSAMENTO DO FORMULÁRIO ---


// Buscar serviços (para preencher o <select>)
$servicos = [];
$result_servico = $conn->query("SELECT id_servico, nome, preco FROM servicos"); // Incluí 'preco' para o option text
if ($result_servico && $result_servico->num_rows > 0) {
    while ($row = $result_servico->fetch_assoc()) {
        $servicos[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <title>Reservar Serviço - Pet Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        /* Variáveis CSS para cores e sombras (copiadas do index.php) */
        :root {
            --primary-color: #007bff;
            --secondary-color: #f9f9f9;
            --text-color: #333;
            --highlight-color: #ffc107;
            --card-shadow: rgba(0, 0, 0, 0.15);
            --btn-hover-color: #0056b3;
        }

        /* Estilos da Navbar (copiados e adaptados do index.php) */
        /* Estes estilos devem estar no seu 'includes/header.php' ou em um CSS global para evitar duplicação */
        .navbar {
            box-shadow: 0 4px 12px var(--card-shadow);
            min-height: 60px;
            padding-top: .5rem;
            padding-bottom: .5rem;
            background-color: var(--primary-color) !important; 
            position: fixed; /* Garante que a navbar flutue */
            top: 0;
            width: 100%;
            z-index: 1030; /* Garante que fique acima de outros elementos */
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.6rem;
            letter-spacing: 1px;
            color: #fff !important;
            white-space: nowrap;
            user-select: none;
        }
        .nav-link {
            font-weight: 500;
            transition: color 0.3s ease;
            padding: .5rem 1rem !important;
        }
        .nav-link:hover {
            color: var(--highlight-color) !important;
        }

        /* Responsividade Navbar */
        @media (max-width: 991.98px) {
            .navbar-collapse {
                background-color: var(--primary-color);
                padding: 1rem;
                margin-top: .5rem;
                border-radius: .5rem;
            }
            .nav-item {
                text-align: center;
            }
            .nav-link {
                padding: .75rem 1rem !important;
            }
        }
        @media (max-width: 480px) {
            body {
                padding-top: 80px; /* Ajusta padding do body para navbar em mobile */
            }
            .navbar {
                min-height: 70px;
            }
            .navbar-brand {
                font-size: 1.4rem;
            }
        }
        /* Fim dos estilos da Navbar */


        /* Estilos específicos desta página (mantidos e ajustados para variáveis CSS) */
        body {
            background-color: var(--secondary-color); /* Usando variável */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 70px; /* Adicionado para compensar navbar fixa */
            padding-bottom: 30px; /* Adicionado para espaço abaixo do formulário */
            color: var(--text-color);
            display: flex; /* Para centralizar o conteúdo verticalmente */
            flex-direction: column;
            align-items: center;
            justify-content: flex-start; /* Alinha ao topo e deixa o formulário centralizar */
            min-height: 100vh;
        }
        h2 {
            text-align: center;
            color: var(--primary-color); /* Usando variável */
            margin-bottom: 25px;
            margin-top: 30px; /* Ajustado para dar espaço abaixo da navbar */
            user-select: none;
        }
        form {
            background: white;
            max-width: 450px;
            width: 100%; /* Garante que o formulário ocupe a largura máxima definida */
            margin: 0 auto;
            padding: 25px 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px var(--card-shadow); /* Usando variável */
        }
        label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: var(--text-color); /* Usando variável */
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
            border-color: var(--primary-color); /* Usando variável */
            outline: none;
        }
        input[type="submit"] {
            background-color: var(--primary-color); /* Usando variável */
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
            background-color: var(--btn-hover-color); /* Usando variável */
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
        .mensagem.warning { /* Adicionado para a classe warning do PHP */
            background-color: #fff3cd;
            color: #856404;
            border: 1.5px solid #ffeeba;
        }
        /* Responsividade */
        @media (max-width: 480px) {
            body {
                padding: 15px 10px;
                padding-top: 80px; /* Manter padding-top para navbar em mobile */
            }
            form {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand" href="../index.php">🐾 PetShop</a> 
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="produtos.php">Produtos</a></li>
                    <li class="nav-item"><a class="nav-link" href="servicos.php">Serviços</a></li>
                    <li class="nav-item"><a class="nav-link" href="carrinho.php">Carrinho</a></li>
                    <li class="nav-item"><a class="nav-link" href="agendamento.php">Agendar</a></li>

                    <?php if (isset($_SESSION['cliente_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link text-warning" href="#">Olá, <?= htmlspecialchars($_SESSION['cliente_nome']) ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="perfil.php">Perfil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-danger" href="../logout.php">Sair</a>
                        </li>
                    <?php elseif (isset($_SESSION['funcionario_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link text-warning" href="#">Olá, <?= htmlspecialchars($_SESSION['funcionario_nome']) ?> (Funcionário)</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../funcionarios/painel_funcionario.php">Painel</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-danger" href="../funcionarios/logout_funcionario.php">Sair</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="login.php">Login Cliente</a></li>
                        <li class="nav-item"><a class="nav-link" href="cadastro_clientes.php">Cadastro Cliente</a></li>
                        <li class="nav-item"><a class="nav-link" href="../funcionarios/login_funcionario.php">Login Funcionário</a></li>
                        <li class="nav-item"><a class="nav-link" href="../funcionarios/cadastro_funcionario.php">Cadastro Funcionário</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>


    <h2>Reservar Serviço para o Pet</h2>

    <?php if($mensagem): ?>
        <div class="mensagem <?php echo strpos($mensagem, 'sucesso') !== false ? 'sucesso' : (strpos($mensagem, 'warning') !== false ? 'warning' : 'erro'); ?>">
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
            <?php foreach ($servicos as $servico_item) { ?> <option value="<?php echo $servico_item['id_servico']; ?>">
                    <?php echo htmlspecialchars($servico_item['nome']); ?> - R$ <?php echo number_format($servico_item['preco'], 2, ',', '.'); ?>
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