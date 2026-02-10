<?php
ob_start(); // KRİTİK NOKTA: Çıktı tamponlamayı başlat (Excel bozulmasın diye)
require_once '../includes/db.php'; 

// --- EXCEL (CSV) DIŞA AKTARMA ---
if (isset($_GET['export'])) {
    // Tamponu temizle (db.php'den veya boşluklardan gelen gereksiz verileri sil)
    if (ob_get_length()) ob_end_clean();
    
    // Dosya adını ve tipini ayarla
    $filename = "kullanici_listesi_" . date('Y-m-d') . ".csv";
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);
    
    // PHP'nin çıktı akışını aç
    $output = fopen('php://output', 'w');
    
    // Excel'in Türkçe karakterleri tanıması için BOM (Byte Order Mark) ekle
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Başlık satırını yaz
    fputcsv($output, ['ID', 'Ad Soyad', 'E-posta', 'Telefon', 'Rol', 'Kayıt Tarihi'], ";");
    
    // Verileri çek ve yaz
    $stmt = $pdo->query("SELECT id, name, email, phone, role, created_at FROM users ORDER BY id DESC");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Rolleri Türkçeleştir
        $roles = ['admin' => 'Yönetici', 'personel' => 'Personel', 'uye' => 'Müşteri'];
        $row['role'] = $roles[$row['role']] ?? $row['role'];
        
        fputcsv($output, $row, ";");
    }
    
    fclose($output);
    exit(); // İşlem bitince kodun geri kalanını çalıştırma
}

// HTML Çıktısı başladığı için tamponu serbest bırakabiliriz ama şart değil, header.php zaten html basacak.
require_once 'includes/header.php';

// GÜVENLİK: Sadece Admin Girebilir
if ($_SESSION['role'] != 'admin') {
    echo "<script>alert('Bu sayfaya erişim yetkiniz yok!'); window.location.href='index.php';</script>";
    exit;
}

// --- SİLME İŞLEMİ ---
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Admin kendisini silemesin
    if ($id == $_SESSION['user_id']) {
        $error = "Kendinizi silemezsiniz!";
    } else {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: users.php?success=silindi");
        exit;
    }
}

// --- DÜZENLEME MODU ---
$editMode = false;
$editUser = [];

if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $editUser = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($editUser) $editMode = true;
}

// --- GÜNCELLEME İŞLEMİ ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['user_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];

    // E-posta kontrolü
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$email, $id]);
    
    if ($stmt->fetchColumn() > 0) {
        $error = "Bu e-posta adresi başka bir kullanıcı tarafından kullanılıyor.";
    } else {
        $sql = "UPDATE users SET name=?, email=?, phone=?, role=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $email, $phone, $role, $id]);
        
        $success = "Kullanıcı bilgileri güncellendi.";
        echo "<script>setTimeout(function(){ window.location.href='users.php'; }, 1500);</script>";
    }
}

// Kullanıcıları Listele
$users = $pdo->query("SELECT * FROM users ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Kullanıcı Yönetimi</h2>
        <a href="users.php?export=true" class="btn btn-success">
            <i class="fas fa-file-excel"></i> Listeyi Excel Olarak İndir
        </a>
    </div>

    <div class="row">
        <!-- SOL: Düzenleme Formu -->
        <div class="col-md-4">
            <div class="card border-<?= $editMode ? 'warning' : 'secondary' ?>">
                <div class="card-header <?= $editMode ? 'bg-warning text-dark' : 'bg-secondary text-white' ?>">
                    <?= $editMode ? '<i class="fas fa-user-edit"></i> Kullanıcıyı Düzenle' : '<i class="fas fa-info-circle"></i> Bilgi' ?>
                </div>
                <div class="card-body">
                    <?php if(!$editMode): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-arrow-right fa-3x mb-3"></i><br>
                            Düzenlemek istediğiniz kullanıcının yanındaki <span class="badge bg-warning text-dark">Düzenle</span> butonuna basınız.
                        </div>
                    <?php else: ?>
                        
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        <?php if(isset($success)): ?>
                            <div class="alert alert-success"><?= $success ?></div>
                        <?php endif; ?>

                        <form method="POST" action="users.php">
                            <input type="hidden" name="user_id" value="<?= $editUser['id'] ?>">

                            <div class="mb-3">
                                <label>Ad Soyad</label>
                                <input type="text" name="name" class="form-control" required value="<?= $editUser['name'] ?>">
                            </div>

                            <div class="mb-3">
                                <label>E-posta</label>
                                <input type="email" name="email" class="form-control" required value="<?= $editUser['email'] ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label>Telefon</label>
                                <input type="text" name="phone" class="form-control" value="<?= $editUser['phone'] ?>">
                            </div>

                            <div class="mb-3">
                                <label>Rol / Yetki</label>
                                <select name="role" class="form-select bg-light border-primary">
                                    <option value="uye" <?= $editUser['role'] == 'uye' ? 'selected' : '' ?>>Müşteri (Üye)</option>
                                    <option value="personel" <?= $editUser['role'] == 'personel' ? 'selected' : '' ?>>Personel (Garson/Mutfak)</option>
                                    <option value="admin" <?= $editUser['role'] == 'admin' ? 'selected' : '' ?>>Yönetici (Admin)</option>
                                </select>
                                <small class="text-danger">* Personel veya Admin yaparken dikkatli olun.</small>
                            </div>

                            <button type="submit" class="btn btn-warning w-100">
                                Değişiklikleri Kaydet
                            </button>
                            <a href="users.php" class="btn btn-secondary w-100 mt-2">İptal</a>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- SAĞ: Kullanıcı Listesi -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Kayıtlı Kullanıcılar</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Ad Soyad</th>
                                    <th>E-posta</th>
                                    <th>Rol</th>
                                    <th class="text-end">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td>#<?= $user['id'] ?></td>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($user['name']) ?></div>
                                        <small class="text-muted"><?= htmlspecialchars($user['phone'] ?? '-') ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td>
                                        <?php if($user['role'] == 'admin'): ?>
                                            <span class="badge bg-danger">Yönetici</span>
                                        <?php elseif($user['role'] == 'personel'): ?>
                                            <span class="badge bg-primary">Personel</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Müşteri</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <a href="users.php?edit=<?= $user['id'] ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-user-edit"></i>
                                        </a>
                                        
                                        <!-- Kendini silemesin -->
                                        <?php if($user['id'] != $_SESSION['user_id']): ?>
                                            <a href="users.php?delete=<?= $user['id'] ?>" 
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('Bu kullanıcıyı ve tüm verilerini silmek istediğinize emin misiniz?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>