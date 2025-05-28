<?php
session_start();
require_once '../includes/conexao.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $senha = trim($_POST["senha"]);

    $stmt = $conn->prepare("SELECT * FROM funcionarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $funcionario = $result->fetch_assoc();

    if ($funcionario && password_verify($senha, $funcionario['senha'])) {
        $_SESSION["funcionario_id"] = $funcionario['id_funcionario'];
        $_SESSION["funcionario_nome"] = $funcionario['nome'];

        header("Location: painel_funcionario.php");
        exit();
    } else {
        $erro = "Email ou senha inválidos!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login Funcionário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #4CAF50;
            --dark-color: #333;
            --light-bg: #f7f9fc;
            --card-bg: #fff;
            --hover-color: #3e8e41;
        }

        body {
            background-color: var(--light-bg);
            font-family: 'Segoe UI', sans-serif;
        }

        .login-container {
            max-width: 420px;
            margin: 100px auto;
            background-color: var(--card-bg);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        h2 {
            color: var(--dark-color);
            font-weight: 700;
            margin-bottom: 20px;
        }

        label {
            font-weight: 500;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--hover-color);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
        }

        a {
            text-decoration: none;
            color: var(--primary-color);
            transition: color 0.3s ease;
        }

        a:hover {
            color: var(--hover-color);
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2 class="text-center">🔐 Login Funcionário</h2>

        <?php if (isset($erro)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">📧 Email</label>
                <input type="email" id="email" name="email" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
                <label for="senha" class="form-label">🔑 Senha</label>
                <input type="password" id="senha" name="senha" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Entrar</button>
        </form>

        <div class="mt-3 text-center">
            <a href="../index.php">🔙 Voltar para a Home</a>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
