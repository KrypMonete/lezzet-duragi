<?php
ob_start(); // Çıktı tamponlamayı başlat
require_once 'includes/header.php';

// GÜVENLİK: Giriş yapmamışsa login sayfasına at
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];
$success = "";
$error = "";

// --- 1. PROFİL GÜNCELLEME İŞLEMİ (YENİ) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $newName = trim($_POST['name']);
    $newEmail = trim($_POST['email']);
    $newPhone = trim($_POST['phone']);

    if (empty($newName) || empty($newEmail)) {
        $error = "Ad ve E-posta alanları zorunludur.";
    } else {
        // E-posta başkası tarafından kullanılıyor mu? (Kendi e-postası hariç)
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$newEmail, $user_id]);
        
        if ($stmt->fetchColumn() > 0) {
            $error = "Bu e-posta adresi başka bir kullanıcı tarafından kullanılıyor.";
        } else {
            // Güncelleme Sorgusu
            $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?");
            if ($stmt->execute([$newName, $newEmail, $newPhone, $user_id])) {
                // Session'daki ismi de güncelle (Navbarda anında değişmesi için)
                $_SESSION['name'] = $newName;
                
                header("Location: profile.php?msg=profile_updated");
                exit;
            } else {
                $error = "Güncelleme sırasında bir hata oluştu.";
            }
        }
    }
}

// --- 2. YORUM YAPMA İŞLEMİ ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_comment'])) {
    $comment = trim($_POST['comment']);
    $rating = $_POST['rating'];

    if (!empty($comment) && !empty($rating)) {
        $stmt = $pdo->prepare("INSERT INTO comments (user_id, comment, rating, is_approved) VALUES (?, ?, ?, 0)");
        if ($stmt->execute([$user_id, $comment, $rating])) {
            header("Location: profile.php?msg=comment_sent");
            exit;
        } else {
            $error = "Yorum gönderilirken bir hata oluştu.";
        }
    } else {
        $error = "Lütfen puan verip yorumunuzu yazınız.";
    }
}

// --- 3. REZERVASYON İPTAL İŞLEMİ ---
if (isset($_GET['cancel_reservation'])) {
    $res_id = $_GET['cancel_reservation'];
    $stmt = $pdo->prepare("UPDATE reservations SET status = 'iptal' WHERE id = ? AND user_id = ? AND reservation_date >= CURDATE()");
    if ($stmt->execute([$res_id, $user_id])) {
        header("Location: profile.php");
        exit;
    }
}

// --- 4. YORUM SİLME İŞLEMİ ---
if (isset($_GET['delete_comment'])) {
    $comment_id = $_GET['delete_comment'];
    $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ? AND user_id = ?");
    if ($stmt->execute([$comment_id, $user_id])) {
        header("Location: profile.php");
        exit;
    }
}

// URL MESAJ KONTROLLERİ
if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'comment_sent') $success = "Yorumunuz alındı! Yönetici onayından sonra yayınlanacaktır.";
    if ($_GET['msg'] == 'profile_updated') $success = "Profil bilgileriniz başarıyla güncellendi.";
}

// --- VERİLERİ ÇEKME ---
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM reservations WHERE user_id = ? ORDER BY reservation_date DESC, reservation_time DESC");
$stmt->execute([$user_id]);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM comments WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$myComments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container py-5">
    <div class="row">
        
        <!-- SOL KOLON: Profil Kartı -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 text-center p-4">
                <div class="mb-3">
                    <div class="rounded-circle bg-primary-custom text-white d-inline-flex align-items-center justify-content-center fw-bold shadow-sm" 
                         style="width: 100px; height: 100px; font-size: 2.5rem;">
                        <?= strtoupper(substr($user['name'], 0, 1)) ?>
                    </div>
                </div>
                <h4 class="fw-bold mb-1"><?= htmlspecialchars($user['name']) ?></h4>
                <p class="text-muted small mb-3"><?= htmlspecialchars($user['email']) ?></p>
                
                <div class="d-grid gap-2">
                    <!-- DÜZENLEME BUTONU (YENİ) -->
                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                        <i class="fas fa-edit me-1"></i> Bilgileri Düzenle
                    </button>
                    
                    <a href="logout.php" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-sign-out-alt me-2"></i>Çıkış Yap
                    </a>
                </div>
                
                <hr>
                <div class="text-start small text-muted">
                    <div class="mb-2"><i class="fas fa-phone me-2"></i> <?= htmlspecialchars($user['phone'] ?? 'Belirtilmedi') ?></div>
                    <div class="mb-2"><i class="fas fa-calendar me-2"></i> Kayıt: <?= date('d.m.Y', strtotime($user['created_at'])) ?></div>
                </div>
            </div>
        </div>

        <!-- SAĞ KOLON: Sekmeli İçerik -->
        <div class="col-lg-8">
            
            <?php if($success): ?>
                <div class="alert alert-success animate__animated animate__fadeIn mb-4"><?= $success ?></div>
            <?php endif; ?>
            <?php if($error): ?>
                <div class="alert alert-danger animate__animated animate__shakeX mb-4"><?= $error ?></div>
            <?php endif; ?>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0">
                    <ul class="nav nav-tabs card-header-tabs" id="profileTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" id="reservations-tab" data-bs-toggle="tab" data-bs-target="#reservations">
                                <i class="fas fa-calendar-check me-2"></i>Rezervasyonlarım
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="comments-tab" data-bs-toggle="tab" data-bs-target="#comments">
                                <i class="fas fa-star me-2"></i>Değerlendirmelerim
                            </button>
                        </li>
                    </ul>
                </div>
                
                <div class="card-body p-4">
                    <div class="tab-content">
                        <!-- Rezervasyonlar Tablo -->
                        <div class="tab-pane fade show active" id="reservations">
                            <?php if(empty($reservations)): ?>
                                <div class="text-center py-5 text-muted">
                                    <i class="far fa-calendar-times fa-3x mb-3"></i>
                                    <p>Henüz bir rezervasyonunuz yok.</p>
                                    <a href="reservation.php" class="btn btn-primary-custom btn-sm">Hemen Rezervasyon Yap</a>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Tarih</th>
                                                <th>Saat</th>
                                                <th>Kişi</th>
                                                <th>Durum</th>
                                                <th class="text-end">İşlem</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($reservations as $res): ?>
                                            <tr>
                                                <td><i class="far fa-calendar-alt text-muted me-1"></i> <?= date('d.m.Y', strtotime($res['reservation_date'])) ?></td>
                                                <td><i class="far fa-clock text-muted me-1"></i> <?= date('H:i', strtotime($res['reservation_time'])) ?></td>
                                                <td><?= $res['num_people'] ?> Kişi</td>
                                                <td>
                                                    <?php 
                                                    $badges = ['bekliyor' => 'bg-warning text-dark', 'onaylandi' => 'bg-success', 'iptal' => 'bg-danger', 'tamamlandi' => 'bg-secondary'];
                                                    $labels = ['bekliyor' => 'Onay Bekliyor', 'onaylandi' => 'Onaylandı', 'iptal' => 'İptal Edildi', 'tamamlandi' => 'Tamamlandı'];
                                                    ?>
                                                    <span class="badge <?= $badges[$res['status']] ?? 'bg-secondary' ?>"><?= $labels[$res['status']] ?? $res['status'] ?></span>
                                                </td>
                                                <td class="text-end">
                                                    <?php if (strtotime($res['reservation_date']) >= strtotime(date('Y-m-d')) && $res['status'] != 'iptal' && $res['status'] != 'tamamlandi'): ?>
                                                        <a href="profile.php?cancel_reservation=<?= $res['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('İptal etmek istediğinize emin misiniz?')"><i class="fas fa-times"></i> İptal</a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Değerlendirmeler Tablo -->
                        <div class="tab-pane fade" id="comments">
                            <div class="bg-light p-3 rounded mb-4 border">
                                <h6 class="fw-bold mb-3">Deneyimini Paylaş</h6>
                                <form method="POST">
                                    <div class="mb-2 rating-css">
                                        <div class="star-icon">
                                            <input type="radio" name="rating" value="1" id="rating1"><label for="rating1" class="fa fa-star"></label>
                                            <input type="radio" name="rating" value="2" id="rating2"><label for="rating2" class="fa fa-star"></label>
                                            <input type="radio" name="rating" value="3" id="rating3"><label for="rating3" class="fa fa-star"></label>
                                            <input type="radio" name="rating" value="4" id="rating4"><label for="rating4" class="fa fa-star"></label>
                                            <input type="radio" name="rating" value="5" id="rating5" checked><label for="rating5" class="fa fa-star"></label>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <textarea name="comment" class="form-control" rows="2" placeholder="Yorumunuz..." required></textarea>
                                    </div>
                                    <button type="submit" name="submit_comment" class="btn btn-primary-custom btn-sm">Gönder</button>
                                </form>
                            </div>
                            
                            <h6 class="fw-bold border-bottom pb-2 mb-3">Geçmiş Yorumların</h6>
                            <?php if(empty($myComments)): ?>
                                <p class="text-muted small">Yorumunuz bulunmuyor.</p>
                            <?php else: ?>
                                <?php foreach($myComments as $comm): ?>
                                    <div class="mb-3 pb-3 border-bottom last-no-border position-relative">
                                        <a href="profile.php?delete_comment=<?= $comm['id'] ?>" class="btn btn-sm text-danger position-absolute top-0 end-0 mt-2" onclick="return confirm('Silmek istediğinize emin misiniz?')"><i class="fas fa-trash-alt"></i></a>
                                        <div class="d-flex justify-content-between mb-1 pe-4">
                                            <div class="text-warning small"><?php for($i=0; $i<$comm['rating']; $i++) echo '<i class="fas fa-star"></i>'; ?></div>
                                            <small class="text-muted"><?= date('d.m.Y', strtotime($comm['created_at'])) ?></small>
                                        </div>
                                        <p class="mb-1 text-muted small">"<?= htmlspecialchars($comm['comment']) ?>"</p>
                                        <?php if($comm['is_approved']): ?>
                                            <span class="badge bg-success" style="font-size: 0.6rem;">Yayında</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark" style="font-size: 0.6rem;">Onay Bekliyor</span>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DÜZENLEME MODALI (POP-UP) -->
<div class="modal fade" id="editProfileModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Profil Bilgilerini Düzenle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Ad Soyad</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">E-posta Adresi</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Telefon Numarası</label>
                        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" name="update_profile" class="btn btn-primary-custom">Değişiklikleri Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .rating-css div { color: #ffe400; font-size: 20px; font-weight: 800; text-align: left; text-transform: uppercase; }
    .rating-css input { display: none; }
    .rating-css input + label { font-size: 20px; text-shadow: 1px 1px 0 #8f8420; cursor: pointer; }
    .rating-css input:checked + label ~ label { color: #b4b4b4; }
    .rating-css label:active { transform: scale(0.8); transition: 0.3s all; }
    .last-no-border:last-child { border-bottom: none !important; }
    .nav-tabs .nav-link.active { color: var(--primary-color); font-weight: bold; border-top: 3px solid var(--primary-color); }
    .nav-tabs .nav-link { color: var(--text-color); }
</style>

<?php 
require_once 'includes/footer.php'; 
ob_end_flush(); 
?>