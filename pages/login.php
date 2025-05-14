<?php
session_start();
$conn = new mysqli("localhost", "root", "", "petshop_db");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $senha = $_POST["senha"];

    $sql = "SELECT * FROM clientes WHERE email = '$email' AND senha = '$senha'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $cliente = $result->fetch_assoc();
        $_SESSION["cliente_id"] = $cliente["id"];
        $_SESSION["cliente_nome"] = $cliente["nome"];
        header("Location: index.php");
        exit();
    } else {
        $erro = "E-mail ou senha incorretos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - Pet Shop</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5 col-md-4">
        <h2 class="text-center">Login</h2>
        <?php if (!empty($erro)) { ?>
            <div class="alert alert-danger"><?= $erro ?></div>
        <?php } ?>
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
</body>
</html>
