<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <title>Pet Shop - Home</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

    <style>
        :root {
            --primary-color: #007bff;
            --secondary-color: #f9f9f9;
            --text-color: #333;
            --highlight-color: #ffc107;
            --card-shadow: rgba(0, 0, 0, 0.15);
            --btn-hover-color: #0056b3;
        }

        body {
            background-color: var(--secondary-color);
            padding-top: 70px; /* Ajuste para acomodar a altura da navbar */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-color);
        }

        /* Navbar */
        .navbar {
            box-shadow: 0 4px 12px var(--card-shadow);
            /* Altura mínima da navbar para desktop */
            min-height: 60px; /* Altura padrão para telas maiores */
            padding-top: .5rem; /* Ajuste do preenchimento vertical */
            padding-bottom: .5rem; /* Ajuste do preenchimento vertical */
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.6rem;
            letter-spacing: 1px;
            color: #fff !important;
            user-select: none;
            /* Garante que o brand não quebre a linha facilmente */
            white-space: nowrap;
        }
        .nav-link {
            font-weight: 500;
            transition: color 0.3s ease;
            /* Aumenta o padding para tornar os links mais clicáveis e alargar a área do link */
            padding: .5rem 1rem !important;
        }
        .nav-link:hover {
            color: var(--highlight-color) !important;
        }

        /* Conteúdo da Navbar para responsividade em telas menores */
        @media (max-width: 991.98px) { /* Ponto de quebra padrão do Bootstrap para navbar-expand-lg */
            .navbar-collapse {
                background-color: var(--primary-color); /* Fundo para o menu colapsado */
                padding: 1rem;
                margin-top: .5rem; /* Espaço entre o brand e o menu colapsado */
                border-radius: .5rem;
            }
            .nav-item {
                text-align: center; /* Centraliza os itens no menu colapsado */
            }
            .nav-link {
                padding: .75rem 1rem !important; /* Mais padding para itens colapsados */
            }
        }
        /* Responsividade para altura da navbar em telas muito pequenas */
        @media (max-width: 480px) {
            body {
                padding-top: 80px; /* Ajusta padding do body para navbar em mobile */
            }
            .navbar {
                min-height: 70px; /* Aumenta um pouco a altura mínima para telas muito pequenas */
            }
            .navbar-brand {
                font-size: 1.4rem; /* Reduz um pouco o tamanho da fonte em telas pequenas */
            }
        }


        /* Cards */
        .card {
            border-radius: 15px;
            box-shadow: 0 6px 15px var(--card-shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }
        .card:hover {
            transform: translateY(-7px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
        }
        .card-img-top {
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            height: 250px;
            object-fit: cover;
        }
        .card-title {
            font-weight: 600;
            font-size: 1.4rem;
            color: var(--primary-color);
        }
        .card-text {
            font-size: 1rem;
            color: #555;
        }

        /* Buttons */
        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            font-weight: 600;
            transition: background-color 0.3s ease;
            padding: 0.5rem 1.3rem;
            border-radius: 30px;
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.3);
        }
        .btn-primary:hover {
            background-color: var(--btn-hover-color);
            box-shadow: 0 6px 15px rgba(0, 86, 179, 0.5);
        }

        /* Welcome Section */
        h1 {
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.3rem;
            user-select: none;
        }
        p.lead {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            color: #444;
        }

        /* Responsive adjustments (gerais, alguns já na navbar) */
        @media (max-width: 768px) {
            .card-img-top {
                height: 200px;
            }
        }
        /* O media query para max-width: 480px já está na navbar-responsive,
           então podemos removê-lo daqui se não houver outras regras gerais. */
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">🐾 PetShop</a> 
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="pages/produtos.php">Produtos</a></li>
                    <li class="nav-item"><a class="nav-link" href="pages/servicos.php">Serviços</a></li>
                    <li class="nav-item"><a class="nav-link" href="pages/carrinho.php">Carrinho</a></li>
                    <li class="nav-item"><a class="nav-link" href="pages/agendamento.php">Agendar</a></li>

                    <?php if (isset($_SESSION['cliente_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link text-warning" href="#">Olá, <?= htmlspecialchars($_SESSION['cliente_nome']) ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="pages/perfil.php">Perfil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-danger" href="logout.php">Sair</a>
                        </li>
                    <?php elseif (isset($_SESSION['funcionario_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link text-warning" href="#">Olá, <?= htmlspecialchars($_SESSION['funcionario_nome']) ?> (Funcionário)</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="funcionarios/painel_funcionario.php">Painel</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-danger" href="funcionarios/logout_funcionario.php">Sair</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="pages/login.php">Login Cliente</a></li>
                        <li class="nav-item"><a class="nav-link" href="pages/cadastro_clientes.php">Cadastro Cliente</a></li>
                        <li class="nav-item"><a class="nav-link" href="funcionarios/login_funcionario.php">Login Funcionário</a></li>
                        <li class="nav-item"><a class="nav-link" href="funcionarios/cadastro_funcionario.php">Cadastro Funcionário</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container text-center">
        <h1>Bem-vindo ao nosso Pet Shop! 🐶🐱</h1>
        <p class="lead">Veja nossos produtos e agende serviços para o seu pet com facilidade!</p>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <img src="/pet-shop/assets/img/produto.jpg" class="card-img-top" alt="Nossos Produtos" />
                    <div class="card-body">
                        <h5 class="card-title">Produtos</h5>
                        <p class="card-text">Rações, brinquedos, acessórios e muito mais.</p>
                        <a href="pages/produtos.php" class="btn btn-primary">Ver produtos</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card">
                    <img src="/pet-shop/assets/img/servicos.jpg" class="card-img-top" alt="Nossos Serviços" /> 
                    <div class="card-body">
                        <h5 class="card-title">Serviços</h5>
                        <p class="card-text">Banho, tosa, vacinação e consultas veterinárias.</p>
                        <a href="pages/servicos.php" class="btn btn-primary">Ver serviços</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>