<?php require_once 'includes/header.php'; ?>

<!-- İstatistikleri Çekelim -->
<?php
// 1. Onaylanmış (Aktif) Yorum Sayısı
$stmt = $pdo->query("SELECT COUNT(*) FROM comments WHERE is_approved = 1");
$active_comments = $stmt->fetchColumn();

// 2. Bekleyen (Onaylanmamış) Yorum Sayısı
$stmt = $pdo->query("SELECT COUNT(*) FROM comments WHERE is_approved = 0");
$pending_comments = $stmt->fetchColumn();

// 3. Toplam Personel Sayısı
$stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'personel'");
$total_staff = $stmt->fetchColumn();

// 4. Bekleyen Rezervasyonlar
$stmt = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status = 'bekliyor'");
$pending_reservations = $stmt->fetchColumn();
?>

<div class="container-fluid">
    <h2 class="mb-4">Yönetim Paneli Özeti</h2>

    <div class="row">
        <!-- Kart 1: Bekleyen Yorumlar (DİKKAT ÇEKİCİ) -->
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3 shadow-sm h-100">
                <div class="card-header fw-bold"><i class="fas fa-comment-dots me-2"></i>Bekleyen Yorumlar</div>
                <div class="card-body">
                    <h1 class="card-title display-4 fw-bold"><?= $pending_comments ?></h1>
                    <p class="card-text">Onayınızı bekleyen yeni yorum var.</p>
                </div>
                <a href="comments.php" class="card-footer text-white text-decoration-none small">
                    İncele <i class="fas fa-arrow-circle-right ms-1"></i>
                </a>
            </div>
        </div>

        <!-- Kart 2: Aktif Yorumlar -->
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3 shadow-sm h-100">
                <div class="card-header"><i class="fas fa-comments me-2"></i>Yayındaki Yorumlar</div>
                <div class="card-body">
                    <h1 class="card-title display-4 fw-bold"><?= $active_comments ?></h1>
                    <p class="card-text">Sitede görünen toplam yorum.</p>
                </div>
            </div>
        </div>

        <!-- Kart 3: Personel Sayısı -->
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3 shadow-sm h-100">
                <div class="card-header"><i class="fas fa-user-tie me-2"></i>Personel Kadrosu</div>
                <div class="card-body">
                    <h1 class="card-title display-4 fw-bold"><?= $total_staff ?></h1>
                    <p class="card-text">Sistemde kayıtlı çalışan sayısı.</p>
                </div>
                <?php if($_SESSION['role'] == 'admin'): ?>
                <a href="users.php" class="card-footer text-white text-decoration-none small">
                    Yönet <i class="fas fa-arrow-circle-right ms-1"></i>
                </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Kart 4: Bekleyen Rezervasyon -->
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3 shadow-sm h-100">
                <div class="card-header"><i class="fas fa-calendar-alt me-2"></i>Yeni Rezervasyon</div>
                <div class="card-body">
                    <h1 class="card-title display-4 fw-bold"><?= $pending_reservations ?></h1>
                    <p class="card-text">Onay bekleyen masa talebi.</p>
                </div>
                <a href="reservations.php" class="card-footer text-white text-decoration-none small">
                    Yönet <i class="fas fa-arrow-circle-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>