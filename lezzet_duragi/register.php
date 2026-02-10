<?php 
require_once 'includes/header.php'; 

// Eğer zaten giriş yapmışsa anasayfaya yönlendir
if (isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='index.php';</script>";
    exit;
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];

    // Basit doğrulama
    if (empty($name) || empty($email) || empty($password)) {
        $error = "Lütfen zorunlu alanları doldurunuz.";
    } else {
        // E-posta daha önce alınmış mı?
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            $error = "Bu e-posta adresi zaten kayıtlı.";
        } else {
            // Kayıt İşlemi
            // Şifreyi MD5 ile şifreliyoruz (Admin panelindeki yapıya uygun olsun diye)
            $hashed_password = md5($password);
            
            $sql = "INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, 'uye')";
            $stmt = $pdo->prepare($sql);
            
            if ($stmt->execute([$name, $email, $phone, $hashed_password])) {
                $success = "Kayıt başarıyla oluşturuldu! Giriş sayfasına yönlendiriliyorsunuz...";
                // 2 saniye sonra giriş sayfasına at
                echo "<script>setTimeout(function(){ window.location.href='login.php'; }, 2000);</script>";
            } else {
                $error = "Bir hata oluştu, lütfen tekrar deneyin.";
            }
        }
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-header bg-primary-custom text-white text-center py-4">
                    <h3 class="mb-0 fw-bold"><i class="fas fa-user-plus me-2"></i>Aramıza Katıl</h3>
                    <p class="mb-0 text-white-50">Lezzet dünyasına adım atın</p>
                </div>
                <div class="card-body p-4 p-md-5">
                    
                    <?php if($error): ?>
                        <div class="alert alert-danger animate__animated animate__shakeX"><?= $error ?></div>
                    <?php endif; ?>
                    
                    <?php if($success): ?>
                        <div class="alert alert-success animate__animated animate__fadeIn"><?= $success ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="form-floating mb-3">
                            <input type="text" name="name" class="form-control" id="floatingName" placeholder="Ad Soyad" required>
                            <label for="floatingName">Ad Soyad</label>
                        </div>
                        
                        <div class="form-floating mb-3">
                            <input type="email" name="email" class="form-control" id="floatingEmail" placeholder="name@example.com" required>
                            <label for="floatingEmail">E-posta Adresi</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="tel" name="phone" class="form-control" id="floatingPhone" placeholder="0555...">
                            <label for="floatingPhone">Telefon (İsteğe Bağlı)</label>
                        </div>

                        <div class="form-floating mb-4">
                            <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Şifre" required>
                            <label for="floatingPassword">Şifre Belirle</label>
                        </div>

                        <button type="submit" class="btn btn-primary-custom w-100 btn-lg mb-3">Kayıt Ol</button>
                        
                        <div class="text-center">
                            <span class="text-muted">Zaten hesabın var mı?</span>
                            <a href="login.php" class="text-decoration-none fw-bold" style="color: var(--primary-color);">Giriş Yap</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>