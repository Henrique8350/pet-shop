<?php
session_start();

// --- INÍCIO DO BLOCO DE PROCESSAMENTO E REDIRECIONAMENTO ---

// Inclua a conexão com o banco de dados aqui, antes de qualquer output,
// para que $conn esteja disponível para a lógica de login.
// REMOVA esta linha se seu 'includes/header.php' JÁ INCLUI 'conexao.php'.
require_once __DIR__ . '/../includes/conexao.php'; 

// Se o seu 'conexao.php' já define e verifica $conn, remova a linha abaixo.
// $conn = new mysqli("localhost", "root", "", "petshop_db"); 

// Verifica conexão (se 'conexao.php' já faz isso, pode ser redundante aqui)
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

$erro = ''; // Inicializa a variável de erro

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $senha = $_POST["senha"];

    $stmt = $conn->prepare("SELECT id_cliente, nome, senha FROM clientes WHERE email = ?");
    if ($stmt === false) { // Adiciona verificação de erro na preparação da query
        $erro = "Erro na preparação da consulta: " . $conn->error;
    } else {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $cliente = $resultado->fetch_assoc();

            if (password_verify($senha, $cliente["senha"])) {
                $_SESSION["cliente_id"] = $cliente["id_cliente"];
                $_SESSION["cliente_nome"] = $cliente["nome"];
                
                // --- REDIRECIONAMENTO AQUI (ANTES DE QUALQUER HTML!) ---
                header("Location: perfil.php"); // Caminho relativo, perfil.php está na mesma pasta 'pages/'
                exit(); // Crucial! Termina o script após o redirecionamento
            } else {
                $erro = "Senha incorreta.";
            }
        } else {
            $erro = "E-mail não encontrado.";
        }
        $stmt->close();
    }
}
$conn->close(); // Fecha a conexão após todo o processamento PHP
// --- FIM DO BLOCO DE PROCESSAMENTO E REDIRECIONAMENTO ---

// AGORA, e SOMENTE AGORA, incluímos o cabeçalho HTML e renderizamos a página.
// Isso garante que nenhum HTML seja enviado antes do redirecionamento.
include_once __DIR__ . '/../includes/header.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <title>Login - Pet Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        /* Variáveis CSS para cores e sombras */
        :root {
            --primary-color: #007bff;
            --secondary-color: #f9f9f9;
            --text-color: #333;
            --highlight-color: #ffc107;
            --card-shadow: rgba(0, 0, 0, 0.15);
            --btn-hover-color: #0056b3;
        }

        /* Estilos da Navbar (copiados e adaptados do index.php) */
        /* Estes estilos devem estar no seu 'includes/header.php' ou em um CSS global */
        /* Estou incluindo-os aqui para que o arquivo seja auto-suficiente na demonstração */
        .navbar {
            box-shadow: 0 4px 12px var(--card-shadow);
            min-height: 60px;
            padding-top: .5rem;
            padding-bottom: .5rem;
            background-color: var(--primary-color) !important;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1030;
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
        /* Fim dos estilos da Navbar */


        /* Estilo do corpo e container de login */
        body {
            background-color: var(--secondary-color);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
            margin: 0;
            padding-bottom: 120px; /* espaço para o footer fixo */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-container {
            background: white;
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        h2 {
            font-weight: 700;
            margin-bottom: 30px;
            color: #343a40;
            text-align: center;
            letter-spacing: 0.5px;
        }
        .form-label {
            font-weight: 600;
            color: #495057;
        }
        .btn-primary {
            background-color: var(--primary-color); 
            border-color: var(--primary-color); 
            font-weight: 600;
            padding: 10px;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: var(--btn-hover-color); 
            border-color: var(--btn-hover-color); 
        }
        .btn-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: var(--primary-color); 
            font-weight: 600;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .btn-link:hover {
            color: var(--btn-hover-color); 
            text-decoration: underline;
        }
        .alert-danger {
            font-weight: 600;
            font-size: 0.9rem;
            text-align: center;
        }

        /* Estilo do footer fixo na parte inferior */
        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: var(--primary-color); 
            color: white;
            padding: 15px 20px;
            box-shadow: 0 -2px 8px rgba(0,0,0,0.1);
            z-index: 1030;
            font-size: 0.9rem;
            font-weight: 500;
        }
        footer .container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 25px;
            flex-wrap: wrap;
        }
        footer a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        footer a:hover {
            text-decoration: underline;
            color: #cce5ff;
        }
        footer i {
            margin-right: 8px;
            font-size: 1.2rem;
        }
        /* Responsividade do footer */
        @media (max-width: 480px) {
            footer .container {
                flex-direction: column;
                gap: 10px;
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container shadow-sm">
        <h2>Login</h2>
        <?php if (!empty($erro)) : ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>
        <form method="POST" novalidate>
            <div class="mb-4">
                <label for="email" class="form-label">Email:</label>
                <input id="email" type="email" name="email" class="form-control" required autofocus />
            </div>
            <div class="mb-4">
                <label for="senha" class="form-label">Senha:</label>
                <input id="senha" type="password" name="senha" class="form-control" required />
            </div>
            <button type="submit" class="btn btn-primary w-100">Entrar</button>
        </form>
        <a href="cadastro_clientes.php" class="btn-link">Não tem conta? Cadastre-se</a>
    </div>

    <footer>
        <div class="container">
            <span>&copy; <?= date('Y') ?> Todos os direitos reservados a <strong>asafegnr</strong>.</span>
            <a href="https://wa.me/5581996827136" target="_blank" aria-label="WhatsApp">
                <i class="fab fa-whatsapp"></i> (81) 99682-7136
            </a>
            <a href="https://github.com/asafegnr" target="_blank" aria-label="GitHub">
                <i class="fab fa-github"></i> github.com/asafegnr
            </a>
        </div>
    </footer>
</body>
</html>