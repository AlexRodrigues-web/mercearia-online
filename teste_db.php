<?php
$host = 'localhost';
$db   = 'mercearia';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Conexão bem-sucedida!";
} catch (PDOException $e) {
    echo "❌ Erro ao conectar ao banco de dados: " . $e->getMessage();
}
?>
