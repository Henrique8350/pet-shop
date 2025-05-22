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
<head class="style_log">
    <meta charset="UTF-8">
    <title>Login - Pet Shop</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5 col-md-4">
        <h2 class="text-center">Login</h2>
        <?php if (!empty($erro)) : ?>
            <div class="alert alert-danger"><?= $erro ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label>Email:</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Senha:</label>
                <input type="password" name="senha" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Entrar</button>
            <a href="cadastro_clientes.php" class="btn btn-link w-100">Não tem conta? Cadastre-se</a>
        </form>
    </div>
    <?php include_once '../includes/footer.php';?>
</body>
</html>
