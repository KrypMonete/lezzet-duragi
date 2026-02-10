<?php 
require_once 'includes/header.php'; 

// Zaten giriş yapmışsa yönlendir
if (isset($_SESSION['user_id'])) {
    // Admin veya Personel ise Yönetim Paneline
    if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'personel') {
        echo "<script>window.location.href='admin/index.php';</script>";
    } else {
        echo "<script>window.location.href='index.php';</script>";
    }
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Lütfen e-posta ve şifrenizi giriniz.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Kullanıcı var mı ve şifre (MD5) doğru mu?
        if ($user && $user['password'] === md5($password)) {
            // Oturum Başlat
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['email'] = $user['email'];

            // Role göre yönlendirme (GÜNCELLENDİ)
            if ($user['role'] == 'admin' || $user['role'] == 'personel') {
                echo "<script>window.location.href='admin/index.php';</script>";
            } else {
                if (isset($_GET['redirect']) && $_GET['redirect'] == 'reservation') {
                    echo "<script>window.location.href='reservation.php';</script>";
                } else {
                    echo "<script>window.location.href='index.php';</script>";
                }
            }
            exit;
        } else {
            $error = "Hatalı e-posta veya şifre!";
        }
    }
}
?>

<div class="container py-5" style="min-height: 70vh; display: flex; align-items: center;">
    <div class="row justify-content-center w-100">
        <div class="col-md-5 col-lg-4">
            
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-header bg-white text-center py-4 border-0">
                    <h2 class="fw-bold mb-1" style="color: var(--primary-color);">Hoşgeldiniz</h2>
                    <p class="text-muted small mb-0">Hesabınıza giriş yapın</p>
                </div>
                
                <div class="card-body p-4 pt-0">
                    <?php if($error): ?>
                        <div class="alert alert-danger animate__animated animate__shakeX py-2 text-center small"><?= $error ?></div>
                    <?php endif; ?>

                    <?php if(isset($_GET['redirect']) && $_GET['redirect'] == 'reservation'): ?>
                        <div class="alert alert-warning text-center small">
                            Rezervasyon yapabilmek için lütfen önce giriş yapın.
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="form-floating mb-3">
                            <input type="email" name="email" class="form-control" id="loginEmail" placeholder="name@example.com" required>
                            <label for="loginEmail">E-posta</label>
                        </div>
                        <div class="form-floating mb-2">
                            <input type="password" name="password" class="form-control" id="loginPassword" placeholder="Şifre" required>
                            <label for="loginPassword">Şifre</label>
                        </div>
                        
                        <!-- Şifremi Unuttum Linki -->
                        <div class="text-end mb-4">
                            <a href="forgot_password.php" class="text-decoration-none small text-muted">Şifremi Unuttum?</a>
                        </div>

                        <button type="submit" class="btn btn-primary-custom w-100 btn-lg mb-3">Giriş Yap</button>
                        
                        <div class="text-center">
                            <p class="mb-0 text-muted">Henüz üye değil misin?</p>
                            <a href="register.php" class="text-decoration-none fw-bold" style="color: var(--primary-color);">Hemen Kayıt Ol</a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>