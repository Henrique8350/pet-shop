<?php
include_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/conexao.php';

$mensagem = '';
$mensagemTipo = '';

function consultarCepViaCEP($cep) {
    $cep = preg_replace('/[^0-9]/', '', $cep);
    if (strlen($cep) != 8) {
        return ['erro' => true, 'mensagem' => 'CEP inválido. Deve conter 8 dígitos.'];
    }

    $url = "https://viacep.com.br/ws/{$cep}/json/";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        return ['erro' => true, 'mensagem' => 'Erro na requisição cURL: ' . curl_error($ch)];
    }
    curl_close($ch);

    $data = json_decode($response, true);

    if (isset($data['erro']) && $data['erro'] === true) {
        return ['erro' => true, 'mensagem' => 'CEP não encontrado ou erro na API do ViaCEP.'];
    }
    return $data;
}

// Inicialização das variáveis para preenchimento do formulário
$nome = '';
$email = '';
$cep = '';
$logradouro = '';
$bairro = '';
$localidade = '';
$uf = '';
$cpf = '';
$telefone = '';
$genero = '';
$tipo_sanguineo = '';
$alergia = '';
$descricao_alergia = '';
$endereco = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Pega dados do formulário, com fallback para '' para evitar warnings
    $nome = htmlspecialchars($_POST['nome'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $cep = htmlspecialchars($_POST['cep'] ?? '');
    $logradouro = htmlspecialchars($_POST['logradouro'] ?? '');
    $bairro = htmlspecialchars($_POST['bairro'] ?? '');
    $localidade = htmlspecialchars($_POST['localidade'] ?? '');
    $uf = htmlspecialchars($_POST['uf'] ?? '');
    $cpf = htmlspecialchars($_POST['cpf'] ?? '');
    $telefone = htmlspecialchars($_POST['telefone'] ?? '');
    $genero = htmlspecialchars($_POST['genero'] ?? '');
    $tipo_sanguineo = htmlspecialchars($_POST['tipo_sanguineo'] ?? '');
    $alergia = htmlspecialchars($_POST['alergia'] ?? '');
    $descricao_alergia = htmlspecialchars($_POST['descricao_alergia'] ?? '');
    $endereco = htmlspecialchars($_POST['endereco'] ?? '');

    // Se o usuário clicou em buscar CEP
    if (isset($_POST['buscar_cep'])) {
        $enderecoAPI = consultarCepViaCEP($cep);

        if (isset($enderecoAPI['erro'])) {
            $mensagem = $enderecoAPI['mensagem'];
            $mensagemTipo = 'danger';
        } else {
            $logradouro = $enderecoAPI['logradouro'] ?? '';
            $bairro = $enderecoAPI['bairro'] ?? '';
            $localidade = $enderecoAPI['localidade'] ?? '';
            $uf = $enderecoAPI['uf'] ?? '';
            $mensagem = 'CEP encontrado e campos preenchidos!';
            $mensagemTipo = 'success';
        }
    } 
    // Senão, tenta cadastrar o cliente
    else if (isset($_POST['cadastrar'])) {
        if (!empty($nome) && !empty($email) && !empty($_POST['senha']) && !empty($cpf) && !empty($genero) && !empty($tipo_sanguineo)) {

            $senha_form = $_POST["senha"];
            $senha_hash = password_hash($senha_form, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO clientes 
                (nome, email, senha, telefone, cpf, genero, tipo_sanguineo, alergia, descricao_alergia, endereco) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            if ($stmt === false) {
                $mensagem = "Erro na preparação da consulta: " . $conn->error;
                $mensagemTipo = "danger";
            } else {
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

        } else {
            $mensagem = "Por favor, preencha todos os campos obrigatórios para cadastrar.";
            $mensagemTipo = "danger";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Versão corrigida -->
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
                    <input type="text" name="nome" id="nome" class="form-control" value="<?= htmlspecialchars($nome) ?>" required>
                </div>
                <div class="mb-3 col-md-6">
                    <label for="cpf" class="form-label">CPF:</label>
                    <input type="text" name="cpf" id="cpf" class="form-control" value="<?= htmlspecialchars($cpf) ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($email) ?>" required>
            </div>

            <div class="mb-3">
                <label for="senha" class="form-label">Senha:</label>
                <input type="password" name="senha" id="senha" class="form-control" required>
            </div>

            <div class="row">
                <div class="mb-3 col-md-6">
                    <label for="telefone" class="form-label">Telefone:</label>
                    <input type="tel" name="telefone" id="telefone" class="form-control" value="<?= htmlspecialchars($telefone) ?>">
                </div>

                <div class="mb-3 col-md-6">
                    <label for="genero" class="form-label">Gênero:</label>
                    <select name="genero" id="genero" class="form-select" required>
                        <option value="">Selecione...</option>
                        <option value="Masculino" <?= $genero === "Masculino" ? 'selected' : '' ?>>Masculino</option>
                        <option value="Feminino" <?= $genero === "Feminino" ? 'selected' : '' ?>>Feminino</option>
                        <option value="Outro" <?= $genero === "Outro" ? 'selected' : '' ?>>Outro</option>
                        <option value="Prefiro não dizer" <?= $genero === "Prefiro não dizer" ? 'selected' : '' ?>>Prefiro não dizer</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label for="tipo_sanguineo" class="form-label">Tipo Sanguíneo:</label>
                <select name="tipo_sanguineo" id="tipo_sanguineo" class="form-select" required>
                    <option value="">Selecione...</option>
                    <?php
                    $tipos = ["A+", "A-", "B+", "B-", "AB+", "AB-", "O+", "O-"];
                    foreach ($tipos as $tipo) {
                        $sel = ($tipo_sanguineo === $tipo) ? "selected" : "";
                        echo "<option value='$tipo' $sel>$tipo</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Possui Alergia?</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="alergia" id="alergia_sim" value="Sim" <?= $alergia === "Sim" ? "checked" : "" ?> required>
                    <label class="form-check-label" for="alergia_sim">Sim</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="alergia" id="alergia_nao" value="Não" <?= $alergia === "Não" ? "checked" : "" ?> required>
                    <label class="form-check-label" for="alergia_nao">Não</label>
                </div>
            </div>

            <div class="mb-3">
                <label for="descricao_alergia" class="form-label">Se sim, descreva:</label>
                <textarea name="descricao_alergia" id="descricao_alergia" class="form-control" rows="2" placeholder="Ex.: Alergia a medicamentos, pó, etc..."><?= htmlspecialchars($descricao_alergia) ?></textarea>
            </div>

            <!-- Campos para endereço e CEP -->
            <div class="mb-3 row">
                <div class="col-md-6">
                    <label for="cep" class="form-label">CEP:</label>
                    <input type="text" name="cep" id="cep" class="form-control" value="<?= htmlspecialchars($cep) ?>" required>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <button type="submit" name="buscar_cep" class="btn btn-primary w-100">Buscar CEP</button>
                </div>
            </div>

            <div class="mb-3">
                <label for="logradouro" class="form-label">Logradouro:</label>
                <input type="text" name="logradouro" id="logradouro" class="form-control" value="<?= htmlspecialchars($logradouro) ?>">
            </div>

            <div class="mb-3">
                <label for="bairro" class="form-label">Bairro:</label>
                <input type="text" name="bairro" id="bairro" class="form-control" value="<?= htmlspecialchars($bairro) ?>">
            </div>

            <div class="mb-3 row">
                <div class="col-md-6">
                    <label for="localidade" class="form-label">Cidade:</label>
                    <input type="text" name="localidade" id="localidade" class="form-control" value="<?= htmlspecialchars($localidade) ?>">
                </div>
                <div class="col-md-6">
                    <label for="uf" class="form-label">UF:</label>
                    <input type="text" name="uf" id="uf" class="form-control" value="<?= htmlspecialchars($uf) ?>">
                </div>
            </div>

            <div class="mb-3">
                <label for="endereco" class="form-label">Complemento / Endereço completo:</label>
                <textarea name="endereco" id="endereco" class="form-control" rows="2"><?= htmlspecialchars($endereco) ?></textarea>
            </div>

            <button type="submit" name="cadastrar" class="btn btn-success w-100">Cadastrar</button>
        </form>
    </div>

    <?php include_once __DIR__ . '/../includes/footer.php';?>
</body>
</html>