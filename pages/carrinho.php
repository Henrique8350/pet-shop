<?php
session_start();
include_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/conexao.php';

$mensagem = '';
$mensagemTipo = '';

// No caso de usarmos GET para mensagens de redirecionamento (útil para debug ou fallback)
if (isset($_GET['msg']) && isset($_GET['type'])) {
    $mensagem = htmlspecialchars($_GET['msg']);
    $mensagemTipo = htmlspecialchars($_GET['type']);
}

$carrinho = $_SESSION['carrinho'] ?? [];
$produtos_no_carrinho = [];
$total = 0;

if (!empty($carrinho)) {
    $placeholders = implode(',', array_fill(0, count($carrinho), '?'));
    $sql = "SELECT id_produto, nome, preco FROM produtos WHERE id_produto IN ($placeholders)";
    
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $types = str_repeat('i', count($carrinho)); // 'i' para int
        $stmt->bind_param($types, ...array_keys($carrinho));
        $stmt->execute();
        $result = $stmt->get_result();

        while ($produto = $result->fetch_assoc()) {
            $quantidade = $carrinho[$produto['id_produto']];
            $produto['quantidade'] = $quantidade;
            $produto['subtotal'] = $produto['preco'] * $quantidade;
            $total += $produto['subtotal'];
            $produtos_no_carrinho[] = $produto;
        }
        $stmt->close();
    } else {
        $mensagem = "Erro na preparação da consulta: " . $conn->error;
        $mensagemTipo = 'danger';
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <title>Carrinho - Pet Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body.bg-light {
            background: #f8fafc;
        }

        h1.text-success {
            font-weight: 700;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            text-shadow: 1px 1px 2px #d4edda;
        }

        .card.shadow {
            border-radius: 15px;
            border: none;
        }

        .list-group-item {
            border: none;
            border-bottom: 1px solid #e3e6f0;
            padding: 15px 20px;
            transition: background-color 0.3s ease;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .list-group-item:last-child {
            border-bottom: none;
        }

        .list-group-item:hover {
            background-color: #f1f5fb;
        }

        .list-group-item strong {
            font-size: 1.1rem;
            color: #212529;
        }

        h4.text-end {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-weight: 700;
            letter-spacing: 0.03em;
        }

        .btn-outline-primary {
            border-radius: 30px;
            padding: 10px 30px;
            font-weight: 600;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .btn-outline-primary:hover {
            background-color: #0d6efd;
            color: white;
        }

        .btn-success {
            border-radius: 30px;
            padding: 10px 30px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgb(25 135 84 / 0.4);
            transition: box-shadow 0.3s ease;
        }

        .btn-success:hover {
            box-shadow: 0 6px 18px rgb(25 135 84 / 0.7);
        }

        .alert-info {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 1.1rem;
        }
        /* Estilos para os botões de ação do carrinho */
        .cart-actions {
            display: flex;
            align-items: center;
            gap: 10px; /* Espaçamento entre os elementos */
            flex-wrap: wrap; /* Permite quebrar linha em telas pequenas */
        }
        .cart-actions .form-control {
            max-width: 80px; /* Largura menor para o input de quantidade */
            text-align: center;
        }
        .btn-sm {
            padding: .25rem .5rem;
            font-size: .875rem;
            border-radius: .2rem;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h1 class="text-center text-success mb-4">Seu Carrinho</h1>

        <?php if (!empty($mensagem)): ?>
            <div class="alert alert-<?= $mensagemTipo ?> text-center" role="alert"><?= htmlspecialchars($mensagem) ?></div>
        <?php endif; ?>

        <?php if (empty($produtos_no_carrinho)) { ?>
            <div class="alert alert-info text-center">O carrinho está vazio.</div>
            <div class="text-center">
                <a href="produtos.php" class="btn btn-primary">Voltar à loja</a>
            </div>
        <?php } else { ?>
            <div class="card shadow p-4 mb-4">
                <ul class="list-group" id="cart-items-list">
                    <?php foreach ($produtos_no_carrinho as $produto) { ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-column flex-md-row" data-product-id="<?= intval($produto['id_produto']); ?>">
                            <div>
                                <strong><?php echo htmlspecialchars($produto['nome']); ?></strong><br />
                                Preço unitário: R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?><br />
                                Subtotal: <strong id="subtotal_<?= intval($produto['id_produto']); ?>">R$ <?php echo number_format($produto['subtotal'], 2, ',', '.'); ?></strong>
                            </div>
                            <div class="cart-actions mt-3 mt-md-0">
                                <label for="quantidade_<?= intval($produto['id_produto']); ?>" class="form-label mb-0">Qtde:</label>
                                <input type="number" 
                                       data-product-id="<?= intval($produto['id_produto']); ?>" 
                                       id="quantidade_<?= intval($produto['id_produto']); ?>" 
                                       class="form-control form-control-sm item-quantity" 
                                       value="<?= intval($produto['quantidade']); ?>" 
                                       min="0" 
                                       required>
                                <button type="button" 
                                        data-product-id="<?= intval($produto['id_produto']); ?>" 
                                        class="btn btn-danger btn-sm remove-item">Remover</button>
                            </div>
                        </li>
                    <?php } ?>
                </ul>

                <h4 class="text-end mt-4">Total: <span class="text-success" id="total_carrinho">R$ <?php echo number_format($total, 2, ',', '.'); ?></span></h4>

                <div class="d-flex justify-content-between mt-4 flex-column flex-md-row gap-2">
                    <a href="produtos.php" class="btn btn-outline-primary">Continuar comprando</a>
                    <a href="finalizar_compra.php" class="btn btn-success">Finalizar Compra</a>
                </div>
            </div>
        <?php } ?>
    </div>

    <?php include_once '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const quantityInputs = document.querySelectorAll('.item-quantity');
            const removeButtons = document.querySelectorAll('.remove-item');
            const totalCarrinhoSpan = document.getElementById('total_carrinho');
            const cartItemsList = document.getElementById('cart-items-list');

            // Função para formatar moeda
            function formatCurrency(value) {
                return 'R$ ' + parseFloat(value).toFixed(2).replace('.', ',');
            }

            // Função para enviar requisição AJAX
            async function updateCart(productId, quantity = null, action) {
                const formData = new FormData();
                formData.append('id_produto', productId);
                formData.append('action', action);
                if (quantity !== null) {
                    formData.append('quantidade', quantity);
                }

                try {
                    const response = await fetch('atualizar_carrinho_ajax.php', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();

                    if (data.success) {
                        // Atualiza o total geral
                        totalCarrinhoSpan.textContent = formatCurrency(data.new_total);

                        // Atualiza o subtotal do item se não foi removido
                        if (data.removed_item_id === null) {
                            const subtotalSpan = document.getElementById('subtotal_' + productId);
                            if (subtotalSpan) {
                                subtotalSpan.textContent = formatCurrency(data.item_subtotal);
                            }
                        } else {
                            // Se o item foi removido, remove a linha da lista
                            const itemToRemove = document.querySelector(`[data-product-id="${data.removed_item_id}"]`);
                            if (itemToRemove) {
                                itemToRemove.remove();
                                // Se não houver mais itens, mostrar mensagem de carrinho vazio
                                if (cartItemsList.children.length === 0) {
                                    const container = document.querySelector('.card.shadow.p-4.mb-4'); // Seleciona o card principal
                                    if (container) {
                                        container.innerHTML = `
                                            <div class="alert alert-info text-center">O carrinho está vazio.</div>
                                            <div class="text-center">
                                                <a href="produtos.php" class="btn btn-primary">Voltar à loja</a>
                                            </div>
                                        `;
                                    }
                                }
                            }
                        }

                        // Exibir mensagem de sucesso/erro (opcional, pode ser um toast, etc.)
                        displayMessage(data.message, data.success ? 'success' : 'danger');

                    } else {
                        displayMessage(data.message, 'danger');
                    }
                } catch (error) {
                    console.error('Erro na requisição AJAX:', error);
                    displayMessage('Erro de comunicação com o servidor.', 'danger');
                }
            }

            // Event Listener para mudanças na quantidade
            quantityInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const productId = this.dataset.productId;
                    const newQuantity = parseInt(this.value);
                    updateCart(productId, newQuantity, 'update');
                });
            });

            // Event Listener para botões de remover
            removeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.dataset.productId;
                    // Confirmação antes de remover
                    if (confirm('Tem certeza que deseja remover este item do carrinho?')) {
                        updateCart(productId, null, 'remove');
                    }
                });
            });

            // Função para exibir mensagens dinamicamente
            function displayMessage(message, type) {
                let alertDiv = document.querySelector('.alert');
                // Se a mensagem que estamos prestes a exibir é a de "carrinho vazio", não sobrescreva.
                // Isso é para evitar que a mensagem de remoção de último item sobrescreva a de "carrinho vazio".
                if (alertDiv && alertDiv.textContent.includes('O carrinho está vazio')) {
                     // Não faça nada, a mensagem de carrinho vazio já está lá.
                     return;
                }

                if (!alertDiv) {
                    alertDiv = document.createElement('div');
                    alertDiv.className = 'alert text-center';
                    alertDiv.setAttribute('role', 'alert');
                    document.querySelector('.container.mt-5').prepend(alertDiv);
                }
                alertDiv.textContent = message;
                alertDiv.className = `alert alert-${type} text-center`;

                // Remove a mensagem após alguns segundos, a menos que seja a de "carrinho vazio"
                if (!message.includes('O carrinho está vazio')) {
                    setTimeout(() => {
                        alertDiv.remove();
                    }, 3000); // Remove após 3 segundos
                }
            }
        });
    </script>
</body>
</html>