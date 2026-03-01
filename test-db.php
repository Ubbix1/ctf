<?php
$db_host = 'ctf.plexaur.com';
$db_user = 'owais_ctf';
$db_pass = 'Owais!C&f';
$db_name = 'ctf_plexaur';

try {
    $pdo = new PDO(
        "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
        $db_user,
        $db_pass
    );
    echo "✅ Connection Successful!";
    
    // Test a simple query
    $result = $pdo->query("SELECT 1");
    echo "<br>✅ Query Test: OK";
    
} catch (PDOException $e) {
    echo "❌ Connection Failed: " . $e->getMessage();
}
?>
