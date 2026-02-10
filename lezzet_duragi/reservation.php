<?php
require_once 'includes/header.php';

// GÜVENLİK: Giriş yapmamışsa login sayfasına at
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php?redirect=reservation';</script>";
    exit;
}

$success = "";
$error = "";

// Form Gönderildiğinde
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $date = $_POST['date'];
    $time = $_POST['time'];
    $people = $_POST['people'];
    $note = trim($_POST['note']);
    $user_id = $_SESSION['user_id'];

    // Basit doğrulama
    if (empty($date) || empty($time) || empty($people)) {
        $error = "Lütfen tarih, saat ve kişi sayısını seçiniz.";
    } elseif (strtotime($date) < strtotime(date('Y-m-d'))) {
        $error = "Geçmiş bir tarihe rezervasyon yapamazsınız.";
    } else {
        // Veritabanına Kaydet (Varsayılan durum: 'bekliyor')
        $stmt = $pdo->prepare("INSERT INTO reservations (user_id, reservation_date, reservation_time, num_people, note, status) VALUES (?, ?, ?, ?, ?, 'bekliyor')");
        
        if ($stmt->execute([$user_id, $date, $time, $people, $note])) {
            $success = "Rezervasyon talebiniz alındı! Yönetici onayladığında profilinizde görebileceksiniz.";
        } else {
            $error = "Bir hata oluştu, lütfen tekrar deneyin.";
        }
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow-lg border-0 rounded-3 overflow-hidden">
                <div class="row g-0">
                    
                    <!-- Sol Taraf: Görsel ve Bilgi Alanı -->
                    <div class="col-md-5 bg-dark text-white d-flex flex-column justify-content-center align-items-center p-4 text-center position-relative" 
                         style="min-height: 400px;">
                         
                        <!-- Arka plan resmi (Admin'den gelen hero resmi) -->
                        <div style="position: absolute; top:0; left:0; width:100%; height:100%; 
                                    background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('<?= $settings['hero_image'] ?>') center/cover; z-index: 0;">
                        </div>

                        <div style="position: relative; z-index: 1;">
                            <i class="fas fa-calendar-alt fa-4x mb-3 text-primary-custom"></i>
                            
                            <!-- DİNAMİK BAŞLIK VE AÇIKLAMA -->
                            <!-- GÜNCELLEME: Başlık artık tema renginde (text-primary-custom sınıfı eklendi) -->
                            <h3 class="fw-bold text-primary-custom"><?= htmlspecialchars($settings['reservation_title'] ?? 'Masayı Ayırt') ?></h3>
                            <p class="small text-white-50 mb-4"><?= htmlspecialchars($settings['reservation_desc'] ?? 'Özel günleriniz için yerinizi ayırtın.') ?></p>
                            
                            <div class="text-start bg-white bg-opacity-10 p-3 rounded">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-clock text-primary-custom me-2"></i>
                                    <small><?= $settings['working_hours'] ?></small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-phone-alt text-primary-custom me-2"></i>
                                    <small><?= $settings['phone'] ?></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sağ Taraf: Form -->
                    <div class="col-md-7 bg-white p-4 p-md-5">
                        <h4 class="fw-bold mb-4" style="color: var(--primary-color);">Rezervasyon Bilgileri</h4>

                        <?php if($success): ?>
                            <div class="alert alert-success animate__animated animate__fadeIn text-center py-4">
                                <i class="fas fa-check-circle fa-3x mb-3"></i>
                                <h5>Talebiniz Alındı!</h5>
                                <p><?= $success ?></p>
                                <div class="mt-3">
                                    <a href="profile.php" class="btn btn-success">Profilime Git</a>
                                    <a href="index.php" class="btn btn-outline-success">Anasayfa</a>
                                </div>
                            </div>
                        <?php else: ?>
                        
                            <?php if($error): ?>
                                <div class="alert alert-danger animate__animated animate__shakeX">
                                    <i class="fas fa-exclamation-circle me-1"></i> <?= $error ?>
                                </div>
                            <?php endif; ?>

                            <form method="POST">
                                
                                <!-- Kişi Sayısı -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-muted">Kaç Kişi?</label>
                                    <div class="row g-2">
                                        <?php $options = ['1', '2', '3', '4', '5', '6', '8', '10+']; ?>
                                        <?php foreach($options as $opt): ?>
                                        <div class="col-3">
                                            <input type="radio" class="btn-check" name="people" id="p<?= $opt ?>" value="<?= $opt ?>" required>
                                            <label class="btn btn-outline-secondary w-100 btn-sm" for="p<?= $opt ?>"><?= $opt ?></label>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <!-- Tarih Seçimi -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted">Tarih</label>
                                        <input type="date" name="date" class="form-control" min="<?= date('Y-m-d') ?>" required>
                                    </div>
                                    <!-- Saat Seçimi -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted">Saat</label>
                                        <select name="time" class="form-select" required>
                                            <option value="">Seçiniz</option>
                                            <?php 
                                            // 12:00'den 23:00'a kadar yarım saatlik dilimler
                                            for($i=12; $i<23; $i++) {
                                                echo "<option value='{$i}:00'>{$i}:00</option>";
                                                echo "<option value='{$i}:30'>{$i}:30</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Masa Tercihi / Not -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold small text-muted">Özel İstek / Masa Tercihi</label>
                                    <textarea name="note" class="form-control" rows="2" placeholder="Örn: Pencere kenarı, Sessiz masa, Doğum günü sürprizi..."></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary-custom w-100 py-2 fw-bold shadow-sm">
                                    Rezervasyonu Tamamla <i class="fas fa-arrow-right ms-2"></i>
                                </button>

                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Radyo butonları seçilince tema rengini alsın */
    .btn-check:checked + .btn-outline-secondary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        color: #fff;
    }
</style>

<?php require_once 'includes/footer.php'; ?>