<?php
session_start();
 include_once __DIR__ . '/../includes/header.php'; 


if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $produto_id = $_POST['id'];

    if (!isset($_SESSION['carrinho'][$produto_id])) {
        $_SESSION['carrinho'][$produto_id] = 1;
    } else {
        $_SESSION['carrinho'][$produto_id]++;
    }
}

header("Location: produtos.php");
    include_once '../includes/footer.php';
exit;
