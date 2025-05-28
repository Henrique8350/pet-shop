<?php
include_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/conexao.php';

$mensagem = '';
$mensagemTipo = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $senha_form = $_POST["senha"];
    $telefone = $_POST["telefone"];
    $cpf = $_POST["cpf"];
    $genero = $_POST["genero"];
    $tipo_sanguineo = $_POST["tipo_sanguineo"];
    $alergia = $_POST["alergia"];
    $descricao_alergia = $_POST["descricao_alergia"];
    $endereco = $_POST["endereco"];

    $senha_hash = password_hash($senha_form, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO clientes 
        (nome, email, senha, telefone, cpf, genero, tipo_sanguineo, alergia, descricao_alergia, endereco) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "ssssssssss",
        $nome, $email, $senha_hash, $telefone, $cpf, $genero, $tipo_sanguineo, $alergia, $descricao_alergia, $endereco
    );

    if ($stmt->execute()) {
        $mensagem = "Cliente cadastrado com sucesso! Redirecionando para login...";
        $mensagemTipo = "success";
        header("refresh:2;url=login.php");
    } else {
        $mensagem = "Erro ao cadastrar: " . $stmt->error;
        $mensagemTipo = "danger";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f7fa;
            min-height: 100vh;
            padding-bottom: 120px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .form-container {
            background-color: white;
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 600px;
        }
        h2 {
            font-weight: 700;
            color: #343a40;
            text-align: center;
            margin-bottom: 30px;
        }
        .form-label {
            font-weight: 600;
        }
        .btn-success {
            font-weight: 600;
        }
        footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: #007bff;
            color: white;
            padding: 15px 20px;
            box-shadow: 0 -2px 8px rgba(0,0,0,0.1);
            z-index: 1030;
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
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Cadastro de Cliente</h2>

        <?php if (!empty($mensagem)): ?>
            <div class="alert alert-<?= $mensagemTipo ?>"><?= htmlspecialchars($mensagem) ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="row">
                <div class="mb-3 col-md-6">
                    <label for="nome" class="form-label">Nome:</label>
                    <input type="text" name="nome" id="nome" class="form-control" required>
                </div>
                <div class="mb-3 col-md-6">
                    <label for="cpf" class="form-label">CPF:</label>
                    <input type="text" name="cpf" id="cpf" class="form-control" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="senha" class="form-label">Senha:</label>
                <input type="password" name="senha" id="senha" class="form-control" required>
            </div>

            <div class="row">
                <div class="mb-3 col-md-6">
                    <label for="telefone" class="form-label">Telefone:</label>
                    <input type="tel" name="telefone" id="telefone" class="form-control">
                </div>

                <div class="mb-3 col-md-6">
                    <label for="genero" class="form-label">Gênero:</label>
                    <select name="genero" id="genero" class="form-select" required>
                        <option value="">Selecione...</option>
                        <option value="Masculino">Masculino</option>
                        <option value="Feminino">Feminino</option>
                        <option value="Outro">Outro</option>
                        <option value="Prefiro não dizer">Prefiro não dizer</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label for="tipo_sanguineo" class="form-label">Tipo Sanguíneo:</label>
                <select name="tipo_sanguineo" id="tipo_sanguineo" class="form-select" required>
                    <option value="">Selecione...</option>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Possui Alergia?</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="alergia" id="alergia_sim" value="Sim" required>
                    <label class="form-check-label" for="alergia_sim">Sim</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="alergia" id="alergia_nao" value="Não" required>
                    <label class="form-check-label" for="alergia_nao">Não</label>
                </div>
            </div>

            <div class="mb-3">
                <label for="descricao_alergia" class="form-label">Se sim, descreva:</label>
                <textarea name="descricao_alergia" id="descricao_alergia" class="form-control" rows="2" placeholder="Ex.: Alergia a medicamentos, pó, etc..."></textarea>
            </div>

            <div class="mb-3">
                <label for="endereco" class="form-label">Endereço:</label>
                <textarea name="endereco" id="endereco" class="form-control" rows="2"></textarea>
            </div>

            <button type="submit" class="btn btn-success w-100">Cadastrar</button>
        </form>
    </div>

    <?php include_once '../includes/footer.php';?>
</body>
</html>
