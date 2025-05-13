<?php
// Conexão com o banco de dados
require_once __DIR__ . '/includes/conexao.php';

// Buscar clientes cadastrados
$clientes_sql = "SELECT * FROM clientes LIMIT 5"; // Exibe os 5 primeiros clientes
$clientes_result = $conn->query($clientes_sql);

// Buscar agendamentos recentes
$agendamentos_sql = "SELECT * FROM agendamentos ORDER BY data_hora DESC LIMIT 5"; // Exibe os 5 últimos agendamentos
$agendamentos_result = $conn->query($agendamentos_sql);

// Buscar produtos disponíveis
$produtos_sql = "SELECT * FROM produtos LIMIT 5"; // Exibe os 5 primeiros produtos
$produtos_result = $conn->query($produtos_sql);

// Buscar serviços disponíveis
$servicos_sql = "SELECT * FROM servicos"; // Exibe todos os serviços
$servicos_result = $conn->query($servicos_sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo ao Pet Shop</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 40px; background-color: #f0f8ff; }
        h2 { text-align: center; }
        .container { display: flex; justify-content: space-around; flex-wrap: wrap; }
        .section { background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 45%; margin: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
        .link { text-align: center; margin-top: 20px; }
        .link a { padding: 10px 20px; background-color: #007BFF; color: white; text-decoration: none; border-radius: 5px; }
        .link a:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <h2>Bem-vindo ao Pet Shop</h2>
    
    <div class="container">
        <!-- Exibição de Clientes -->
        <div class="section">
            <h3>Clientes Cadastrados</h3>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Telefone</th>
                </tr>
                <?php while ($cliente = $clientes_result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $cliente['id_cliente']; ?></td>
                        <td><?php echo $cliente['nome']; ?></td>
                        <td><?php echo $cliente['email']; ?></td>
                        <td><?php echo $cliente['telefone']; ?></td>
                    </tr>
                <?php } ?>
            </table>
            <div class="link"><a href="pages/cadastro_clientes.php">Cadastrar Cliente</a>
            </div>
        </div>

        <!-- Exibição de Agendamentos -->
        <div class="section">
            <h3>Agendamentos Recentes</h3>
            <table>
                <tr>
                    <th>Nome do Pet</th>
                    <th>Serviço</th>
                    <th>Data e Hora</th>
                </tr>
                <?php while ($agendamento = $agendamentos_result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $agendamento['nome_pet']; ?></td>
                        <td><?php echo $agendamento['servico']; ?></td>
                        <td><?php echo $agendamento['data_hora']; ?></td>
                    </tr>
                <?php } ?>
            </table>
            <div class="link"><a href="pages/agendamento.php">Agendar Serviço</a></div>
        </div>
    </div>

    <div class="container">
        <!-- Exibição de Produtos -->
        <div class="section">
            <h3>Produtos Disponíveis</h3>
            <table>
                <tr>
                    <th>Nome</th>
                    <th>Preço</th>
                    <th>Categoria</th>
                </tr>
                <?php while ($produto = $produtos_result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $produto['nome']; ?></td>
                        <td>R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></td>
                        <td><?php echo $produto['categoria']; ?></td>
                    </tr>
                <?php } ?>
            </table>
            <div class="link"><a href="pages/produtos.php">Ver Todos os Produtos</a></div>
        </div>

        <!-- Exibição de Serviços -->
        <div class="section">
            <h3>Serviços Disponíveis</h3>
            <table>
                <tr>
                    <th>Nome</th>
                    <th>Duração</th>
                    <th>Preço</th>
                </tr>
                <?php while ($servico = $servicos_result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $servico['nome']; ?></td>
                        <td><?php echo $servico['duracao_minutos']; ?> minutos</td>
                        <td>R$ <?php echo number_format($servico['preco'], 2, ',', '.'); ?></td>
                    </tr>
                <?php } ?>
            </table>
            <div class="link"><a href="pages/servicos.php">Ver Todos os Serviços</a></div>
            </div>
    </div>
</body>
</html>
