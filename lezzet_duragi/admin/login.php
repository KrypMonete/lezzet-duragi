<?php
session_start();
require_once '../includes/db.php'; // Veritabanı bağlantısını çağırdık

// Eğer zaten giriş yapılmışsa direkt panele at
if (isset($_SESSION['user_id']) && ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'personel')) {
    header("Location: index.php");
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Veritabanında bu emaile sahip kullanıcı var mı?
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Kullanıcı varsa ve şifre doğruysa
    if ($user && $user['password'] === md5($password)) {
        
        // GÜNCELLEME: Sadece 'admin' değil, 'personel' de giriş yapabilsin
        if ($user['role'] === 'admin' || $user['role'] === 'personel') {
            // Giriş Başarılı! Oturumu başlat.
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role']; // Rolü kaydettik ki her sayfada kontrol edelim
            
            header("Location: index.php"); // Dashboard'a yönlendir
            exit;
        } else {
            $error = "Bu alana girmek için yetkiniz yok! Sadece yetkili personel girebilir.";
        }
    } else {
        $error = "Hatalı E-posta veya Şifre!";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yönetim Paneli Girişi | Lezzet Durağı</title>
    <!-- Basit ve temiz bir CSS (Bootstrap CDN kullanıyoruz hızlı tasarım için) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #ffffff; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .login-card { width: 100%; max-width: 400px; padding: 2.5rem; border-radius: 15px; background: white; box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
        .btn-primary { background-color: #ff6347; border: none; padding: 10px; font-weight: bold; }
        .btn-primary:hover { background-color: #e5533d; }
        .form-control:focus { border-color: #ff6347; box-shadow: 0 0 0 0.2rem rgba(255, 99, 71, 0.25); }
    </style>
</head>
<body>

<div class="login-card">
    <div class="text-center mb-4">
        <h3 class="fw-bold text-dark">Yönetim Paneli</h3>
        <p class="text-muted small">Personel veya Yönetici Girişi</p>
    </div>
    
    <?php if($error): ?>
        <div class="alert alert-danger text-center small"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label text-muted small fw-bold">E-posta Adresi</label>
            <input type="email" name="email" class="form-control" required placeholder="ornek@restoran.com">
        </div>
        <div class="mb-4">
            <label class="form-label text-muted small fw-bold">Şifre</label>
            <input type="password" name="password" class="form-control" required placeholder="******">
        </div>
        <button type="submit" class="btn btn-primary w-100 mb-3">Giriş Yap</button>
    </form>
    
    <div class="text-center mt-3 border-top pt-3">
        <a href="../index.php" class="text-decoration-none text-muted small">
            &larr; Siteye Geri Dön
        </a>
    </div>
</div>

</body>
</html>