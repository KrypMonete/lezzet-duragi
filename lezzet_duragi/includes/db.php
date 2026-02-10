<?php
$host = "localhost";
$dbname = "lezzet_duragi"; // Senin oluşturduğun veritabanı adı
$username = "root";
$password = ""; // XAMPP'ta varsayılan şifre boştur

try {
    // PDO ile güvenli bağlantı oluşturuyoruz
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Hataları ekrana bassın ki sorunu görelim
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    // Bağlantı hatası olursa çalışmayı durdur ve mesaj ver
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}
?>