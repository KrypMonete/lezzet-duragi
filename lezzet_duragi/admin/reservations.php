<?php
require_once 'includes/header.php';

// --- DURUM GÜNCELLEME İŞLEMİ ---
if (isset($_GET['status']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $status = $_GET['status'];
    
    // Güvenlik: Sadece izin verilen durumlar
    $allowed_statuses = ['bekliyor', 'onaylandi', 'iptal', 'tamamlandi'];
    
    if (in_array($status, $allowed_statuses)) {
        $stmt = $pdo->prepare("UPDATE reservations SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
        
        // İşlem sonrası temiz URL'ye dön
        header("Location: reservations.php?success=guncellendi");
        exit;
    }
}

// --- SİLME İŞLEMİ ---
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM reservations WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: reservations.php?success=silindi");
    exit;
}

// --- REZERVASYONLARI ÇEKME ---
// Müşteri adını ve telefonunu da almak için users tablosuyla birleştiriyoruz (LEFT JOIN)
// En yakın tarihli rezervasyon en üstte görünsün
$sql = "SELECT r.*, u.name as user_name, u.phone as user_phone, u.email as user_email
        FROM reservations r 
        LEFT JOIN users u ON r.user_id = u.id 
        ORDER BY r.reservation_date DESC, r.reservation_time ASC";

$reservations = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
    <h2 class="mb-4">Rezervasyon Yönetimi</h2>
    
    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            İşlem başarıyla gerçekleştirildi.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <span>Rezervasyon Listesi</span>
            <span class="badge bg-light text-dark"><?= count($reservations) ?> Toplam Kayıt</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-secondary">
                        <tr>
                            <th>Tarih & Saat</th>
                            <th>Müşteri Bilgisi</th>
                            <th>Kişi</th>
                            <th>Müşteri Notu</th>
                            <th>Durum</th>
                            <th class="text-end" style="min-width: 200px;">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservations as $res): ?>
                        
                        <!-- Tarihi geçmiş rezervasyonları biraz soluk gösterelim -->
                        <?php $isPast = strtotime($res['reservation_date']) < strtotime(date('Y-m-d')); ?>
                        <tr class="<?= $isPast ? 'opacity-75 bg-light' : '' ?>">
                            
                            <td>
                                <div class="fw-bold"><i class="far fa-calendar-alt"></i> <?= date('d.m.Y', strtotime($res['reservation_date'])) ?></div>
                                <div class="text-primary"><i class="far fa-clock"></i> <?= date('H:i', strtotime($res['reservation_time'])) ?></div>
                                <?php if($isPast): ?>
                                    <span class="badge bg-secondary" style="font-size: 0.7em;">Geçmiş</span>
                                <?php endif; ?>
                            </td>
                            
                            <td>
                                <div class="fw-bold"><?= htmlspecialchars($res['user_name'] ?? 'Misafir') ?></div>
                                <div class="small text-muted">
                                    <i class="fas fa-phone-alt"></i> <?= htmlspecialchars($res['user_phone'] ?? '-') ?>
                                </div>
                            </td>
                            
                            <td>
                                <span class="badge bg-info text-dark" style="font-size: 1em;">
                                    <?= htmlspecialchars($res['num_people']) ?>
                                </span>
                            </td>
                            
                            <td>
                                <?php if(!empty($res['note'])): ?>
                                    <span class="d-inline-block text-truncate" style="max-width: 150px;" 
                                          title="<?= htmlspecialchars($res['note']) ?>">
                                        <i class="fas fa-sticky-note text-warning"></i> <?= htmlspecialchars($res['note']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            
                            <td>
                                <?php 
                                    $statusColors = [
                                        'bekliyor' => 'warning text-dark',
                                        'onaylandi' => 'success',
                                        'iptal' => 'danger',
                                        'tamamlandi' => 'secondary'
                                    ];
                                    $statusText = [
                                        'bekliyor' => 'Bekliyor',
                                        'onaylandi' => 'Onaylandı',
                                        'iptal' => 'İptal',
                                        'tamamlandi' => 'Tamamlandı'
                                    ];
                                    $color = $statusColors[$res['status']] ?? 'secondary';
                                    $text = $statusText[$res['status']] ?? $res['status'];
                                ?>
                                <span class="badge bg-<?= $color ?>"><?= $text ?></span>
                            </td>
                            
                            <td class="text-end">
                                <!-- Durum Değiştirme Butonları -->
                                <div class="btn-group btn-group-sm">
                                    <?php if($res['status'] != 'onaylandi'): ?>
                                        <a href="reservations.php?id=<?= $res['id'] ?>&status=onaylandi" 
                                           class="btn btn-outline-success" title="Onayla">
                                            <i class="fas fa-check"></i>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if($res['status'] != 'iptal'): ?>
                                        <a href="reservations.php?id=<?= $res['id'] ?>&status=iptal" 
                                           class="btn btn-outline-danger" title="İptal Et"
                                           onclick="return confirm('Bu rezervasyonu iptal etmek istediğinize emin misiniz?');">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    <?php endif; ?>

                                    <?php if($res['status'] != 'tamamlandi' && $isPast): ?>
                                         <a href="reservations.php?id=<?= $res['id'] ?>&status=tamamlandi" 
                                           class="btn btn-outline-secondary" title="Tamamlandı Olarak İşaretle">
                                            <i class="fas fa-check-double"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Silme Butonu -->
                                <a href="reservations.php?delete=<?= $res['id'] ?>" 
                                   class="btn btn-sm btn-dark ms-1" 
                                   onclick="return confirm('Kayıt tamamen silinecek! Emin misiniz?');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>

                        <?php if(empty($reservations)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-calendar-times fa-3x mb-3"></i><br>
                                    Henüz hiç rezervasyon yok.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>