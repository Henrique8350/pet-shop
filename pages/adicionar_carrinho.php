<?php
session_start(); // Inicia a sessão APENAS UMA VEZ no início do script

// --- Lógica de Validação de Login ---
// O usuário precisa estar logado para adicionar itens ao carrinho
if (!isset($_SESSION["cliente_id"])) {
    header("Location: login.php"); // Redireciona para a página de login
    exit(); // Para o script aqui para garantir o redirecionamento
}

// --- Lógica de Adicionar ao Carrinho ---
// O carrinho é um array na sessão para armazenar produto_id => quantidade
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

// Verifica se a requisição é POST e se o ID do produto foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $produto_id = intval($_POST['id']); // Garante que o ID é um inteiro

    // Adiciona o produto ao carrinho ou incrementa a quantidade
    if ($produto_id > 0) { // Validação básica para garantir um ID válido
        if (!isset($_SESSION['carrinho'][$produto_id])) {
            $_SESSION['carrinho'][$produto_id] = 1;
        } else {
            $_SESSION['carrinho'][$produto_id]++;
        }
        
        // Opcional: Adicionar uma mensagem de sucesso na sessão para exibir na página de produtos
        $_SESSION['mensagem_produtos'] = [
            'texto' => 'Produto adicionado ao carrinho!',
            'tipo' => 'success'
        ];
    } else {
        // Mensagem de erro para ID de produto inválido
        $_SESSION['mensagem_produtos'] = [
            'texto' => 'Erro: ID do produto inválido.',
            'tipo' => 'danger'
        ];
    }
} else {
    // Mensagem de erro se a requisição não for POST ou ID não for enviado
    $_SESSION['mensagem_produtos'] = [
        'texto' => 'Erro: Nenhuma ação de adicionar produto especificada.',
        'tipo' => 'danger'
    ];
}

// --- Redirecionamento Final ---
// Redireciona de volta para a página de produtos após adicionar ao carrinho
// Este header() DEVE ser a última coisa a ser executada antes de exit()
// e antes de qualquer output HTML.
header("Location: produtos.php"); 
exit(); // Crucial! Termina o script aqui para garantir o redirecionamento
?>