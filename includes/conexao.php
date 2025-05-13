<?php
$host = 'localhost';        // ou '127.0.0.1'
$usuario = 'root';          // ou o nome do seu usuário MySQL
$senha = '';                // coloque a senha do seu MySQL, se tiver
$banco = 'petshop_db';      // nome do seu banco de dados

// Criar conexão
$conn = new mysqli($host, $usuario, $senha, $banco);

// Verificar conexão
if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}
?>
