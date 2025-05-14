<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Pet Shop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Estilo personalizado -->
    <style>
        body {
            background-color: #f9f9f9;
            margin-top: 60px; /* Margem para garantir que o conteúdo não fique atrás da navbar */
        }
        .navbar {
            height: 70px; /* Definir a altura fixa da navbar */
            padding-top: 0.5rem; /* Ajuste do preenchimento da navbar */
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        .navbar-nav .nav-link {
            font-weight: 500;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top shadow">
        <div class="container">
            <a class="navbar-brand" href="/pet-shop/index.php">PetShop</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="/pet-shop/pages/produtos.php">Produtos</a></li>
                    <li class="nav-item"><a class="nav-link" href="/pet-shop/pages/servicos.php">Serviços</a></li>
                    <li class="nav-item"><a class="nav-link" href="/pet-shop/pages/carrinho.php">Carrinho</a></li>
                    <li class="nav-item"><a class="nav-link" href="/pet-shop/pages/agendamento.php">Agendar</a></li>
                    <?php if (isset($_SESSION['cliente_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link text-warning" href="#">Olá, <?= htmlspecialchars($_SESSION['cliente_nome']) ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/pet-shop/pages/logout.php">Sair</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="/pet-shop/pages/login.php">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="/pet-shop/pages/cadastro_clientes.php">Cadastro</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
