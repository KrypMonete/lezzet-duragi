<?php 
require_once 'includes/header.php'; 

$step = 1; // Varsayılan adım: Bilgi Doğrulama
$error = "";
$success = "";
$verified_user_id = 0;

// Form Gönderildiğinde
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // --- ADIM 1: KİMLİK DOĞRULAMA ---
    if (isset($_POST['check_user'])) {
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);

        if (empty($email) || empty($phone)) {
            $error = "Lütfen e-posta ve telefon numaranızı giriniz.";
        } else {
            // E-posta ve Telefon eşleşiyor mu?
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND phone = ?");
            $stmt->execute([$email, $phone]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $step = 2; // Doğrulama başarılı, şifre değiştirme ekranına geç
                $verified_user_id = $user['id'];
            } else {
                $error = "Girdiğiniz bilgilerle eşleşen bir kullanıcı bulunamadı.";
            }
        }
    }

    // --- ADIM 2: ŞİFRE GÜNCELLEME ---
    if (isset($_POST['update_password'])) {
        $user_id = $_POST['user_id'];
        $new_pass = $_POST['new_password'];
        $confirm_pass = $_POST['confirm_password'];

        if (empty($new_pass) || empty($confirm_pass)) {
            $error = "Lütfen yeni şifrenizi giriniz.";
            $step = 2; // Hatada aynı ekranda kal
            $verified_user_id = $user_id;
        } elseif ($new_pass !== $confirm_pass) {
            $error = "Şifreler uyuşmuyor!";
            $step = 2;
            $verified_user_id = $user_id;
        } else {
            // Şifreyi Güncelle (MD5)
            $hashed_password = md5($new_pass);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            
            if ($stmt->execute([$hashed_password, $user_id])) {
                $success = "Şifreniz başarıyla güncellendi! Giriş sayfasına yönlendiriliyorsunuz...";
                echo "<script>setTimeout(function(){ window.location.href='login.php'; }, 3000);</script>";
                $step = 3; // Başarılı ekranı
            } else {
                $error = "Bir hata oluştu.";
            }
        }
    }
}
?>

<div class="container py-5" style="min-height: 60vh; display: flex; align-items: center;">
    <div class="row justify-content-center w-100">
        <div class="col-md-5 col-lg-4">
            
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-header bg-white text-center py-4 border-0">
                    <h3 class="fw-bold mb-1"><i class="fas fa-lock text-primary-custom me-2"></i>Şifre Sıfırlama</h3>
                    <p class="text-muted small mb-0">Hesabınızı kurtarın</p>
                </div>
                
                <div class="card-body p-4 pt-0">
                    
                    <?php if($error): ?>
                        <div class="alert alert-danger animate__animated animate__shakeX small"><?= $error ?></div>
                    <?php endif; ?>
                    
                    <?php if($success): ?>
                        <div class="alert alert-success animate__animated animate__fadeIn small"><?= $success ?></div>
                    <?php endif; ?>

                    <!-- ADIM 1: BİLGİ DOĞRULAMA FORMU -->
                    <?php if($step == 1): ?>
                        <form method="POST">
                            <div class="mb-3 text-center text-muted small">
                                Güvenliğiniz için lütfen kayıtlı e-posta adresinizi ve telefon numaranızı giriniz.
                            </div>
                            <div class="form-floating mb-3">
                                <input type="email" name="email" class="form-control" id="resetEmail" placeholder="E-posta" required>
                                <label for="resetEmail">E-posta Adresi</label>
                            </div>
                            <div class="form-floating mb-4">
                                <input type="text" name="phone" class="form-control" id="resetPhone" placeholder="Telefon" required>
                                <label for="resetPhone">Telefon Numarası</label>
                            </div>
                            <button type="submit" name="check_user" class="btn btn-primary-custom w-100 mb-3">Doğrula ve Devam Et</button>
                            <div class="text-center">
                                <a href="login.php" class="text-decoration-none text-muted small">Giriş Yap'a Dön</a>
                            </div>
                        </form>
                    <?php endif; ?>

                    <!-- ADIM 2: YENİ ŞİFRE FORMU -->
                    <?php if($step == 2): ?>
                        <form method="POST">
                            <input type="hidden" name="user_id" value="<?= $verified_user_id ?>">
                            <div class="alert alert-info small"><i class="fas fa-check-circle me-1"></i> Bilgiler doğrulandı. Yeni şifrenizi belirleyin.</div>
                            
                            <div class="form-floating mb-3">
                                <input type="password" name="new_password" class="form-control" id="newPass" placeholder="Yeni Şifre" required>
                                <label for="newPass">Yeni Şifre</label>
                            </div>
                            <div class="form-floating mb-4">
                                <input type="password" name="confirm_password" class="form-control" id="confPass" placeholder="Şifre Tekrar" required>
                                <label for="confPass">Şifre Tekrar</label>
                            </div>
                            <button type="submit" name="update_password" class="btn btn-success w-100 mb-3">Şifreyi Güncelle</button>
                        </form>
                    <?php endif; ?>

                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>