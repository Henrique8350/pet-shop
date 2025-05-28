<?php
session_start();
require_once __DIR__ . '/../includes/conexao.php';

// Verificar se o funcionário está logado
if (!isset($_SESSION['funcionario_id'])) {
    header('Location: ../login_funcionario.php');
    exit();
}

// Sanitização e validação básica das entradas para evitar erros
function limparTexto($texto) {
    return trim(htmlspecialchars($texto));
}

// Adicionar serviço
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    if ($acao === 'adicionar') {
        $nome = limparTexto($_POST['nome'] ?? '');
        $preco = filter_var($_POST['preco'], FILTER_VALIDATE_FLOAT);

        if ($nome && $preco !== false) {
            $stmt = $conn->prepare("INSERT INTO servicos (nome, preco) VALUES (?, ?)");
            $stmt->bind_param("sd", $nome, $preco);
            $stmt->execute();
            $stmt->close();
            header("Location: servicos.php");
            exit();
        }
    } elseif ($acao === 'editar') {
        $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
        $nome = limparTexto($_POST['nome'] ?? '');
        $preco = filter_var($_POST['preco'], FILTER_VALIDATE_FLOAT);

        if ($id && $nome && $preco !== false) {
            $stmt = $conn->prepare("UPDATE servicos SET nome = ?, preco = ? WHERE id_servico = ?");
            $stmt->bind_param("sdi", $nome, $preco, $id);
            $stmt->execute();
            $stmt->close();
            header("Location: servicos.php");
            exit();
        }
    }
}

// Excluir serviço
if (isset($_GET['excluir'])) {
    $id = filter_var($_GET['excluir'], FILTER_VALIDATE_INT);
    if ($id) {
        $stmt = $conn->prepare("DELETE FROM servicos WHERE id_servico = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        header("Location: servicos.php");
        exit();
    }
}

// Buscar serviços
$result = $conn->query("SELECT * FROM servicos ORDER BY id_servico DESC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <title>Gerenciar Serviços</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-5">
    <h2>🛠️ Gerenciar Serviços</h2>
    <a href="dashboard.php" class="btn btn-secondary mb-3">🔙 Voltar</a>

    <div class="card mb-4">
        <div class="card-header">➕ Adicionar Novo Serviço</div>
        <div class="card-body">
            <form method="POST" novalidate>
                <input type="hidden" name="acao" value="adicionar" />
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome do Serviço:</label>
                    <input type="text" id="nome" name="nome" class="form-control" required />
                </div>
                <div class="mb-3">
                    <label for="preco" class="form-label">Preço (R$):</label>
                    <input type="number" id="preco" name="preco" step="0.01" min="0" class="form-control" required />
                </div>
                <button type="submit" class="btn btn-success">Adicionar Serviço</button>
            </form>
        </div>
    </div>

    <h4>📄 Lista de Serviços</h4>
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Serviço</th>
                <th>Preço</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id_servico'] ?></td>
                        <td><?= htmlspecialchars($row['nome']) ?></td>
                        <td>R$ <?= number_format($row['preco'], 2, ',', '.') ?></td>
                        <td>
                            <!-- Botão de Editar -->
                            <button class="btn btn-primary btn-sm" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalEditar<?= $row['id_servico'] ?>">
                                ✏️ Editar
                            </button>

                            <!-- Botão de Excluir -->
                            <a href="?excluir=<?= $row['id_servico'] ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Tem certeza que deseja excluir este serviço?');">
                                ❌ Excluir
                            </a>

                            <!-- Modal de Edição -->
                            <div class="modal fade" id="modalEditar<?= $row['id_servico'] ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST" novalidate>
                                            <div class="modal-header">
                                                <h5 class="modal-title">Editar Serviço</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="acao" value="editar" />
                                                <input type="hidden" name="id" value="<?= $row['id_servico'] ?>" />
                                                <div class="mb-3">
                                                    <label for="nome<?= $row['id_servico'] ?>" class="form-label">Nome:</label>
                                                    <input type="text" id="nome<?= $row['id_servico'] ?>" name="nome" class="form-control" value="<?= htmlspecialchars($row['nome']) ?>" required />
                                                </div>
                                                <div class="mb-3">
                                                    <label for="preco<?= $row['id_servico'] ?>" class="form-label">Preço:</label>
                                                    <input type="number" id="preco<?= $row['id_servico'] ?>" name="preco" step="0.01" min="0" class="form-control" value="<?= htmlspecialchars($row['preco']) ?>" required />
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-success">Salvar Alterações</button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- Fim do Modal -->
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4" class="text-center">Nenhum serviço cadastrado.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
