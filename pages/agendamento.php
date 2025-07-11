<?php
session_start();

// --- INÍCIO DA LÓGICA DE PROCESSAMENTO (AJAX ou FORM SUBMIT) ---
// Verifica se esta é uma requisição AJAX para 'check_availability'
$is_ajax_check = (isset($_POST['action']) && $_POST['action'] === 'check_availability');

// Se for uma requisição AJAX, configuramos o cabeçalho e SAÍMOS IMEDIATAMENTE após gerar o JSON.
// Esta parte do código deve ser a PRIMEIRA a ser executada no arquivo PHP
// para evitar que qualquer HTML seja enviado antes do JSON.
if ($is_ajax_check) {
    header('Content-Type: application/json');
    // Incluir APENAS a conexão com o banco de dados aqui.
    // NUNCA inclua o header.php ou qualquer arquivo que possa gerar HTML.
    require_once __DIR__ . '/../includes/conexao.php';

    $response_ajax = [
        'available' => false,
        'message' => 'Data e hora inválidas.'
    ];

    $data_hora_completa_str = $_POST['data_hora_completa'] ?? '';

    // Validação de formato (YYYY-MM-DDTHH:MM)
    if (!preg_match("/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/", $data_hora_completa_str)) {
        $response_ajax['message'] = 'Formato de data e hora inválido. Use YYYY-MM-DDTHH:MM.';
        echo json_encode($response_ajax);
        exit();
    }

    $data_hora_db_format = str_replace('T', ' ', $data_hora_completa_str) . ':00'; 
    
    $data_hora_obj = new DateTime($data_hora_db_format);
    $agora = new DateTime();

    // 1. Não permitir datas e horas passadas
    if ($data_hora_obj < $agora) {
        $response_ajax['message'] = 'Não é possível agendar para uma data ou hora passada. Por favor, escolha uma data e hora futura.';
        echo json_encode($response_ajax);
        exit();
    }

    // 2. Exemplo: Não permitir agendamentos aos domingos (opcional, remova se não usar)
    if ($data_hora_obj->format('N') == 7) { 
        $response_ajax['message'] = 'Não há agendamentos disponíveis aos domingos. Por favor, escolha outro dia.';
        echo json_encode($response_ajax);
        exit();
    }

    // Lógica de vagas
    $sql_vagas = "SELECT COUNT(*) FROM agendamentos WHERE data_hora = ?";
    $stmt_vagas = $conn->prepare($sql_vagas);
    $limite_vagas_por_horario = 1; // **Defina seu limite de agendamentos por horário específico**

    if ($stmt_vagas) {
        $stmt_vagas->bind_param("s", $data_hora_db_format);
        $stmt_vagas->execute();
        $stmt_vagas->bind_result($agendamentos_no_horario);
        $stmt_vagas->fetch();
        $stmt_vagas->close();

        if ($agendamentos_no_horario >= $limite_vagas_por_horario) {
            $response_ajax['available'] = false;
            $response_ajax['message'] = 'Este horário (' . $data_hora_obj->format('d/m/Y H:i') . ') está lotado. Por favor, escolha outro horário.';
        } else {
            $response_ajax['available'] = true;
            $response_ajax['message'] = 'Horário disponível (' . $data_hora_obj->format('d/m/Y H:i') . ')! Faltam ' . ($limite_vagas_por_horario - $agendamentos_no_horario) . ' vaga(s).';
        }
    } else {
         $response_ajax['message'] = 'Erro ao preparar consulta de vagas: ' . $conn->error;
    }
    
    $conn->close(); // Fecha a conexão para a requisição AJAX
    echo json_encode($response_ajax);
    exit(); // Sai imediatamente após enviar o JSON. NADA DE HTML DEVE SER IMPRESSO DEPOIS DISSO.
} 

// Se não for uma requisição AJAX para check_availability, continue com o processamento normal da página
// Inclua o header.php aqui, como de costume, pois a página será renderizada.
include_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/conexao.php'; // Conexão com o BD novamente, pois foi fechada no bloco AJAX

if (!isset($_SESSION["cliente_id"])) {
    header("Location: login.php");
    exit();
}

$id_cliente = $_SESSION["cliente_id"];

// Buscar serviços (isso só acontece se não for uma requisição AJAX de check_availability)
$servicos = [];
$result_servico = $conn->query("SELECT id_servico, nome FROM servicos");
if ($result_servico && $result_servico->num_rows > 0) {
    while ($row = $result_servico->fetch_assoc()) {
        $servicos[] = $row;
    }
}

$message = '';
$messageClass = '';

// Lógica para Submissão Completa do Formulário (quando o botão "Agendar" é clicado)
if ($_SERVER["REQUEST_METHOD"] == "POST") { // Este POST não é o do AJAX, é do formulário completo
    $nome_pet = trim($_POST["nome_pet"]) ?? null;
    $id_servico = $_POST["id_servico"] ?? null;
    $nome_dono = trim($_POST["nome_dono"]);
    $telefone = trim($_POST["telefone"]);
    $data_hora = $_POST["data_hora"]; // Este é o datetime-local, ex: 2025-07-10T14:30

    if (!$nome_pet || !$id_servico || !$nome_dono || !$telefone || !$data_hora) {
        $message = "Por favor, preencha todos os campos obrigatórios!";
        $messageClass = "warning";
    } else {
        $data_hora_db_format = str_replace('T', ' ', $data_hora) . ':00';

        // Re-verificação de disponibilidade (SEGURANÇA!)
        $data_hora_obj_check = new DateTime($data_hora_db_format);
        $agora_check = new DateTime();

        if ($data_hora_obj_check < $agora_check || $data_hora_obj_check->format('N') == 7) {
            $message = "A data ou hora selecionada é inválida ou indisponível. Por favor, verifique.";
            $messageClass = "danger";
        } else {
            $sql_check_slot = "SELECT COUNT(*) FROM agendamentos WHERE data_hora = ?";
            $stmt_check_slot = $conn->prepare($sql_check_slot);
            $stmt_check_slot->bind_param("s", $data_hora_db_format);
            $stmt_check_slot->execute();
            $stmt_check_slot->bind_result($count_slot);
            $stmt_check_slot->fetch();
            $stmt_check_slot->close();

            if ($count_slot >= $limite_vagas_por_horario) { 
                $message = "Este horário já está lotado. Por favor, escolha outro.";
                $messageClass = "danger";
            } else {
                // Lógica de cadastro de pet e agendamento
                $id_pet = null;
                $stmt = $conn->prepare("SELECT id_pet FROM pets WHERE nome = ? AND id_cliente = ?");
                if ($stmt) {
                    $stmt->bind_param("si", $nome_pet, $id_cliente);
                    $stmt->execute();
                    $stmt->bind_result($id_pet);
                    $stmt->fetch();
                    $stmt->close();
                } else {
                    $message = "Erro ao preparar consulta de pet existente: " . $conn->error;
                    $messageClass = "danger";
                }

                if (!$id_pet && empty($message)) {
                    $stmt = $conn->prepare("INSERT INTO pets (nome, id_cliente) VALUES (?, ?)");
                    if ($stmt) {
                        $stmt->bind_param("si", $nome_pet, $id_cliente);
                        if (!$stmt->execute()) {
                            $message = "Erro ao cadastrar pet: " . $stmt->error;
                            $messageClass = "danger";
                        } else {
                            $id_pet = $stmt->insert_id;
                        }
                        $stmt->close();
                    } else {
                        $message = "Erro ao preparar cadastro de pet: " . $conn->error;
                        $messageClass = "danger";
                    }
                }

                if ($id_pet && empty($message)) {
                    $stmt = $conn->prepare(
                        "INSERT INTO agendamentos 
                        (id_cliente, id_pet, id_servico, nome_dono, telefone, data_hora) 
                        VALUES (?, ?, ?, ?, ?, ?)"
                    );
                    if ($stmt) {
                        $stmt->bind_param("iiisss", $id_cliente, $id_pet, $id_servico, $nome_dono, $telefone, $data_hora_db_format);

                        if ($stmt->execute()) {
                            $message = "Agendamento realizado com sucesso!";
                            $messageClass = "success";
                        } else {
                            $message = "Erro ao agendar: " . $stmt->error;
                            $messageClass = "danger";
                        }
                        $stmt->close();
                    } else {
                        $message = "Erro ao preparar agendamento: " . $conn->error;
                        $messageClass = "danger";
                    }
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <title>Agendamento Pet Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        :root { /* Adicionado para compatibilidade com o CSS da navbar */
            --primary-color: #007bff;
            --secondary-color: #f9f9f9;
            --text-color: #333;
            --highlight-color: #ffc107;
            --card-shadow: rgba(0, 0, 0, 0.15);
            --btn-hover-color: #0056b3;
        }
        /* Mantenha seu CSS original e adicione/ajuste para os novos elementos */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--secondary-color); /* Usando variável */
            margin: 0;
            padding-top: 70px; /* Ajuste para navbar fixa se houver */
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        /* Navbar CSS - Copiado do index.php */
        .navbar {
            box-shadow: 0 4px 12px var(--card-shadow);
            min-height: 60px;
            padding-top: .5rem;
            padding-bottom: .5rem;
            background-color: var(--primary-color) !important; /* Garante que a navbar seja azul */
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
                padding-top: 80px;
            }
            .navbar {
                min-height: 70px;
            }
            .navbar-brand {
                font-size: 1.4rem;
            }
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

        /* Mensagens de feedback do formulário (aparecem no topo) */
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

        /* Novas classes para a mensagem de disponibilidade do calendário */
        .disponibilidade-msg {
            margin-top: 5px;
            padding: 10px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 0.95rem;
            display: none; /* Inicia oculto */
        }
        .msg-success-calendar {
            background-color: #e6ffe6; /* Verde claro para feedback de calendário */
            color: #0a6b0a;
            border: 1px solid #c8f5c8;
            display: block; /* Exibe quando tem mensagem */
        }
        .msg-danger-calendar {
            background-color: #ffe6e6; /* Vermelho claro para feedback de calendário */
            color: #b30000;
            border: 1px solid #f5c6cb;
            display: block; /* Exibe quando tem mensagem */
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


    <h2>Agendamento para Pet Shop</h2>

    <?php if ($message): ?>
        <div class="message <?= $messageClass ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="post" action="agendamento.php">
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
        <div id="disponibilidade-feedback" class="disponibilidade-msg"></div>

        <input type="submit" value="Agendar">
    </form>

<?php include_once '../includes/footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dataHoraInput = document.getElementById('data_hora');
        const disponibilidadeFeedback = document.getElementById('disponibilidade-feedback');

        // Função para verificar a disponibilidade via AJAX
        async function verificarDisponibilidade() {
            const dataHoraCompletaSelecionada = dataHoraInput.value; // Ex: 2025-07-10T14:30
            
            // Limpa mensagens anteriores
            disponibilidadeFeedback.textContent = '';
            disponibilidadeFeedback.className = 'disponibilidade-msg'; // Reseta as classes de cor e oculta
            
            if (!dataHoraCompletaSelecionada) {
                return; // Não faz nada se a data e hora estiverem vazias
            }

            const formData = new FormData();
            // Agora enviamos a data e hora completas para a verificação
            formData.append('data_hora_completa', dataHoraCompletaSelecionada); 
            formData.append('action', 'check_availability'); // Identificador para a requisição AJAX no PHP

            try {
                const response = await fetch('agendamento.php', { // Aponta para o próprio arquivo!
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.available) {
                    disponibilidadeFeedback.textContent = data.message;
                    disponibilidadeFeedback.classList.add('msg-success-calendar');
                } else {
                    disponibilidadeFeedback.textContent = data.message;
                    disponibilidadeFeedback.classList.add('msg-danger-calendar');
                }
            } catch (error) {
                console.error('Erro na requisição AJAX:', error);
                disponibilidadeFeedback.textContent = 'Erro ao verificar disponibilidade. Tente novamente.';
                disponibilidadeFeedback.classList.add('msg-danger-calendar');
            }
        }

        // Adiciona um listener para o evento 'change' (quando a data e hora são selecionadas ou alteradas)
        dataHoraInput.addEventListener('change', verificarDisponibilidade);

        // Opcional: Define a data e hora mínima para agendamento (a partir de agora)
        const now = new Date();
        const year = now.getFullYear();
        const month = (now.getMonth() + 1).toString().padStart(2, '0');
        const day = now.getDate().toString().padStart(2, '0');
        const hours = now.getHours().toString().padStart(2, '0');
        const minutes = now.getMinutes().toString().padStart(2, '0');
        dataHoraInput.min = `${year}-${month}-${day}T${hours}:${minutes}`;
    });
</script>

</body>
</html>