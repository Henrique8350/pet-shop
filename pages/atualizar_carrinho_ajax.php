<?php
session_start();
require_once __DIR__ . '/../includes/conexao.php'; // Certifique-se de que o caminho está correto

header('Content-Type: application/json'); // Informa ao navegador que a resposta será JSON

$response = [
    'success' => false,
    'message' => '',
    'new_total' => 0.00,
    'item_subtotal' => 0.00,
    'removed_item_id' => null
];

// Garante que a requisição é um POST e que os dados necessários estão presentes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_produto'])) {
    $id_produto = intval($_POST['id_produto']);
    $action = $_POST['action'] ?? ''; // 'update' ou 'remove'
    $nova_quantidade = isset($_POST['quantidade']) ? intval($_POST['quantidade']) : 0;

    // Garante que o carrinho existe na sessão
    if (!isset($_SESSION['carrinho'])) {
        $_SESSION['carrinho'] = [];
    }

    if ($action === 'update') {
        if ($nova_quantidade <= 0) {
            // Se a quantidade for 0 ou menos, remove o item
            if (isset($_SESSION['carrinho'][$id_produto])) {
                unset($_SESSION['carrinho'][$id_produto]);
                $response['removed_item_id'] = $id_produto;
                $response['message'] = 'Produto removido do carrinho (quantidade zero).';
            }
        } else {
            // Atualiza a quantidade
            $_SESSION['carrinho'][$id_produto] = $nova_quantidade;
            $response['message'] = 'Quantidade atualizada.';
        }
    } elseif ($action === 'remove') {
        if (isset($_SESSION['carrinho'][$id_produto])) {
            unset($_SESSION['carrinho'][$id_produto]);
            $response['removed_item_id'] = $id_produto;
            $response['message'] = 'Produto removido do carrinho.';
        } else {
            $response['message'] = 'Produto não encontrado no carrinho para remoção.';
        }
    } else {
        $response['message'] = 'Ação inválida.';
    }

    // Recalcula o total do carrinho e o subtotal do item (se aplicável)
    $total_geral = 0;
    $item_subtotal_calculado = 0;

    if (!empty($_SESSION['carrinho'])) {
        // Obter os IDs dos produtos no carrinho
        $carrinho_ids = array_keys($_SESSION['carrinho']);
        $placeholders = implode(',', array_fill(0, count($carrinho_ids), '?'));
        
        $sql = "SELECT id_produto, preco FROM produtos WHERE id_produto IN ($placeholders)";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $types = str_repeat('i', count($carrinho_ids));
            $stmt->bind_param($types, ...$carrinho_ids);
            $stmt->execute();
            $result = $stmt->get_result();
            $produtos_banco = [];
            while ($row = $result->fetch_assoc()) {
                $produtos_banco[$row['id_produto']] = $row['preco'];
            }
            $stmt->close();

            foreach ($_SESSION['carrinho'] as $prod_id => $qty) {
                if (isset($produtos_banco[$prod_id])) {
                    $preco_unitario = $produtos_banco[$prod_id];
                    $subtotal_item = $preco_unitario * $qty;
                    $total_geral += $subtotal_item;

                    if ($prod_id == $id_produto) { // Se for o item que estamos processando
                        $response['item_subtotal'] = $subtotal_item;
                    }
                }
            }
        } else {
            $response['message'] = 'Erro na preparação da consulta de produtos.';
        }
    }

    $response['success'] = true;
    $response['new_total'] = $total_geral;

} else {
    $response['message'] = 'Requisição inválida.';
}

$conn->close();
echo json_encode($response);
exit();
?>