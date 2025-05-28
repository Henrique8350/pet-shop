<?php
session_start();
include_once __DIR__ . '/../includes/header.php';
$conn = new mysqli("localhost", "root", "", "petshop_db");

// Verifica conexão
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

$erro = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $senha = $_POST["senha"];

    $stmt = $conn->prepare("SELECT id_cliente, nome, senha FROM clientes WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $cliente = $resultado->fetch_assoc();

        if (password_verify($senha, $cliente["senha"])) {
            $_SESSION["cliente_id"] = $cliente["id_cliente"];
            $_SESSION["cliente_nome"] = $cliente["nome"];
            header("Location: perfil.php"); // Redireciona para perfil após login
            exit();
        } else {
            $erro = "Senha incorreta.";
        }
    } else {
        $erro = "E-mail não encontrado.";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <title>Login - Pet Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Font Awesome para ícones do footer -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        /* Estilo do corpo e container de login */
        body {
            background: #f5f7fa;
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
            background-color: #007bff;
            border-color: #007bff;
            font-weight: 600;
            padding: 10px;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .btn-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #007bff;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .btn-link:hover {
            color: #0056b3;
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
            background-color: #007bff; /* cor do bg-primary */
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
