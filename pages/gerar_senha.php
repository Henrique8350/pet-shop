<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerador de Senhas Criptografadas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Gerador de Senhas Criptografadas (password_hash)</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="senha" class="form-label">Digite a senha que deseja criptografar:</label>
            <input type="text" id="senha" name="senha" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Gerar Hash</button>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $senha = $_POST["senha"];
        $hash = password_hash($senha, PASSWORD_DEFAULT);

        echo "<div class='alert alert-success mt-3'>";
        echo "<strong>Senha original:</strong> " . htmlspecialchars($senha) . "<br>";
        echo "<strong>Hash gerado:</strong> <br><textarea class='form-control' rows='2'>" . htmlspecialchars($hash) . "</textarea>";
        echo "</div>";
    }
    ?>
</div>
</body>
</html>
