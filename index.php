<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Pet Shop - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background-color: #f9f9f9;
            padding-top: 60px;
        }
        .card {
            margin-bottom: 30px;
        }
        .navbar-brand {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">PetShop</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="pages/produtos.php">Produtos</a></li>
                    <li class="nav-item"><a class="nav-link" href="pages/servicos.php">Serviços</a></li>
                    <li class="nav-item"><a class="nav-link" href="pages/carrinho.php">Carrinho</a></li>
                    <li class="nav-item"><a class="nav-link" href="pages/agendamento.php">Agendar</a></li>
                    <?php if (isset($_SESSION['cliente_id'])): ?>
                        <li class="nav-item"><a class="nav-link text-warning" href="#">Olá, <?= $_SESSION['cliente_nome'] ?></a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="pages/login.php">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="pages/cadastro_clientes.php">Cadastro</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Conteúdo da home -->
    <div class="container text-center">
        <h1 class="my-4">Bem-vindo ao nosso Pet Shop!</h1>
        <p class="lead">Veja nossos produtos e agende serviços para o seu pet com facilidade!</p>

        <div class="row">
            <!-- Produtos -->
            <div class="col-md-6">
                <div class="card">
                    <img src="https://via.placeholder.com/500x250?text=Produtos" class="card-img-top" alt="Produtos">
                    <div class="card-body">
                        <h5 class="card-title">Produtos</h5>
                        <p class="card-text">Rações, brinquedos, acessórios e muito mais.</p>
                        <a href="pages/produtos.php" class="btn btn-primary">Ver produtos</a>
                    </div>
                </div>
            </div>

            <!-- Serviços -->
            <div class="col-md-6">
                <div class="card">
                    <img src="https://via.placeholder.com/500x250?text=Serviços" class="card-img-top" alt="Serviços">
                    <div class="card-body">
                        <h5 class="card-title">Serviços</h5>
                        <p class="card-text">Banho, tosa, vacinação e consultas veterinárias.</p>
                        <a href="pages/servicos.php" class="btn btn-primary">Ver serviços</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts do Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
