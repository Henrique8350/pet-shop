<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <title>Pet Shop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <!-- Estilo global -->
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        main {
            flex: 1;
        }
        /* Navbar */
        .navbar {
            height: 80px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.6rem;
            letter-spacing: 1px;
        }
        .navbar-nav .nav-link {
            font-weight: 500;
            font-size: 1rem;
            transition: color 0.3s ease;
        }
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link:focus {
            color: #ffc107 !important;
        }
        .nav-link.user-name {
            font-weight: 600;
            color: #ffc107 !important;
            cursor: pointer;
        }
        .nav-link.user-name:hover {
            color: #fff !important;
            text-decoration: underline;
        }
        @media (max-width: 576px) {
            .navbar-brand {
                font-size: 1.3rem;
            }
            .navbar-nav .nav-link {
                font-size: 0.9rem;
            }
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
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/pet-shop/index.php">PetShop</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="/pet-shop/pages/produtos.php">Produtos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/pet-shop/pages/servicos.php">Serviços</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/pet-shop/pages/carrinho.php">Carrinho</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/pet-shop/pages/agendamento.php">Agendar</a>
                    </li>

                    <?php if (isset($_SESSION['cliente_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link user-name" href="/pet-shop/pages/perfil.php">
                                Olá, <?= htmlspecialchars($_SESSION['cliente_nome']) ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/pet-shop/pages/logout.php">Sair</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/pet-shop/pages/login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/pet-shop/pages/cadastro_clientes.php">Cadastro</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
