<?php
require_once 'includes/header.php';

// GÜVENLİK
if ($_SESSION['role'] != 'admin') {
    echo "<script>alert('Bu sayfaya erişim yetkiniz yok!'); window.location.href='index.php';</script>";
    exit;
}

// Ayarları Çek
$stmt = $pdo->query("SELECT * FROM settings WHERE id = 1");
$settings = $stmt->fetch(PDO::FETCH_ASSOC);

$currentBgColor = $settings['bg_color'] ?? '#ffffff';

// GÜNCELLEME İŞLEMİ
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Verileri Al
    $site_title = $_POST['site_title'];
    $hero_title = $_POST['hero_title'];
    $hero_subtitle = $_POST['hero_subtitle'];
    $about_title = $_POST['about_title'];
    $about_text = $_POST['about_text'];
    $about_whatsapp = $_POST['about_whatsapp'];
    
    $menu_title = $_POST['menu_title'];
    $menu_subtitle = $_POST['menu_subtitle'];
    $reservation_title = $_POST['reservation_title'];
    $reservation_desc = $_POST['reservation_desc'];
    
    // YENİ: Duyuru Şeridi Verileri
    $announcement_text = $_POST['announcement_text'];
    $announcement_active = isset($_POST['announcement_active']) ? 1 : 0;
    
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $theme_color = $_POST['theme_color'];
    $bg_color = $_POST['bg_color'];
    
    // Resim Yükleme (Aynı)
    $logo_path = $settings['logo_path'];
    if (!empty($_FILES['logo']['name'])) {
        $upload_dir = '../uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $logo_name = time() . "_" . basename($_FILES['logo']['name']);
        $target_file = $upload_dir . $logo_name;
        if (move_uploaded_file($_FILES['logo']['tmp_name'], $target_file)) $logo_path = 'uploads/' . $logo_name;
    }

    $hero_image = $settings['hero_image'];
    if (!empty($_FILES['hero_image']['name'])) {
        $upload_dir = '../uploads/';
        $hero_name = "hero_" . time() . "_" . basename($_FILES['hero_image']['name']);
        $target_file = $upload_dir . $hero_name;
        if (move_uploaded_file($_FILES['hero_image']['tmp_name'], $target_file)) $hero_image = 'uploads/' . $hero_name;
    }

    // Veritabanını Güncelle
    $sql = "UPDATE settings SET 
            site_title = ?, logo_path = ?, 
            hero_title = ?, hero_subtitle = ?, hero_image = ?,
            about_title = ?, about_text = ?, about_whatsapp = ?, 
            menu_title = ?, menu_subtitle = ?, 
            reservation_title = ?, reservation_desc = ?, 
            announcement_text = ?, announcement_active = ?,
            address = ?, phone = ?, email = ?, theme_color = ?, bg_color = ?
            WHERE id = 1";
            
    $updateStmt = $pdo->prepare($sql);
    $updateStmt->execute([
        $site_title, $logo_path, 
        $hero_title, $hero_subtitle, $hero_image,
        $about_title, $about_text, $about_whatsapp,
        $menu_title, $menu_subtitle,
        $reservation_title, $reservation_desc,
        $announcement_text, $announcement_active,
        $address, $phone, $email, $theme_color, $bg_color
    ]);

    echo "<script>alert('Ayarlar başarıyla güncellendi!'); window.location.href='settings.php';</script>";
}
?>

<div class="container-fluid">
    <h2 class="mb-4">Site Genel Ayarları (CMS)</h2>
    
    <form method="POST" enctype="multipart/form-data">
        <div class="row">
            
            <!-- Sol Kolon -->
            <div class="col-md-6">
                <!-- Genel Bilgiler -->
                <div class="card mb-4">
                    <div class="card-header bg-dark text-white">Genel Site Bilgileri</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label>Site Başlığı</label>
                            <input type="text" name="site_title" class="form-control" value="<?= $settings['site_title'] ?>">
                        </div>
                        
                        <!-- Tema Seçimi -->
                        <div class="mb-3">
                            <label class="mb-2 fw-bold">Site Teması & Arka Plan</label>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <?php 
                                $themes = [
                                    ['#ff6347', '#ffffff', 'Lezzet Kırmızısı'],
                                    ['#ff6347', '#212529', 'Gece Modu (Dark)'],
                                    ['#0d6efd', '#f0f8ff', 'Okyanus Mavisi'],
                                    ['#198754', '#f1fff5', 'Doğa Yeşili'],
                                    ['#6c757d', '#f8f9fa', 'Modern Gri'],
                                    ['#ffc107', '#fffcf5', 'Altın Sarısı'],
                                    ['#6610f2', '#f3f0ff', 'Asil Mor']
                                ];
                                foreach($themes as $theme): 
                                    $colorCode = $theme[0]; $bgCode = $theme[1]; $themeName = $theme[2];
                                    $btnStyle = "background-color: $colorCode; color: #fff;";
                                    if($bgCode == '#212529') $btnStyle .= " border: 2px solid #555;";
                                ?>
                                    <button type="button" class="btn btn-sm shadow-sm" style="<?= $btnStyle ?> width: auto;" onclick="setTheme('<?= $colorCode ?>', '<?= $bgCode ?>')">
                                        <?= $themeName ?><?php if($bgCode != '#ffffff'): ?> <i class="fas fa-fill-drip ms-1" style="color: <?= $bgCode ?>;"></i><?php endif; ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="color" name="theme_color" id="themeColorInput" class="form-control form-control-color w-100" value="<?= $settings['theme_color'] ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="color" name="bg_color" id="bgColorInput" class="form-control form-control-color w-100" value="<?= $currentBgColor ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Logo Yükle</label>
                            <?php if($settings['logo_path']): ?>
                                <div class="mb-2"><img src="../<?= $settings['logo_path'] ?>" height="50"></div>
                            <?php endif; ?>
                            <input type="file" name="logo" class="form-control">
                        </div>
                    </div>
                </div>

                <!-- YENİ: DUYURU ŞERİDİ AYARLARI -->
                <div class="card mb-4 border-primary">
                    <div class="card-header bg-primary text-white"><i class="fas fa-bullhorn"></i> Duyuru / Kampanya Şeridi</div>
                    <div class="card-body">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="announcement_active" id="announceCheck" 
                                   <?= $settings['announcement_active'] ? 'checked' : '' ?>>
                            <label class="form-check-label fw-bold" for="announceCheck">Şeridi Göster (Aktif)</label>
                        </div>
                        <div class="mb-2">
                            <label>Duyuru Metni</label>
                            <input type="text" name="announcement_text" class="form-control" 
                                   value="<?= $settings['announcement_text'] ?? 'Fırsatları kaçırmayın!' ?>" 
                                   placeholder="Örn: Bugün tüm tatlılarda %20 indirim!">
                        </div>
                        <small class="text-muted">Bu şerit menünün hemen altında sitenin tema rengiyle görünecektir.</small>
                    </div>
                </div>

                <!-- İletişim -->
                <div class="card mb-4">
                    <div class="card-header bg-dark text-white">İletişim Bilgileri (Footer)</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label>Adres</label>
                            <textarea name="address" class="form-control" rows="2"><?= $settings['address'] ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Telefon</label>
                            <input type="text" name="phone" class="form-control" value="<?= $settings['phone'] ?>">
                        </div>
                        <div class="mb-3">
                            <label>E-posta</label>
                            <input type="email" name="email" class="form-control" value="<?= $settings['email'] ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sağ Kolon -->
            <div class="col-md-6">
                <!-- Hero Ayarları -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">Giriş (Hero) Bölümü</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label>Büyük Başlık</label>
                            <input type="text" name="hero_title" class="form-control" value="<?= $settings['hero_title'] ?>">
                        </div>
                        <div class="mb-3">
                            <label>Alt Başlık</label>
                            <input type="text" name="hero_subtitle" class="form-control" value="<?= $settings['hero_subtitle'] ?>">
                        </div>
                        <div class="mb-3">
                            <label>Kapak Resmi</label>
                            <?php if($settings['hero_image']): ?>
                                <div class="mb-2"><img src="../<?= $settings['hero_image'] ?>" width="100" class="img-thumbnail"></div>
                            <?php endif; ?>
                            <input type="file" name="hero_image" class="form-control">
                        </div>
                    </div>
                </div>

                <!-- Menü ve Rezervasyon -->
                <div class="card mb-4 border-warning">
                    <div class="card-header bg-warning text-dark"><i class="fas fa-utensils"></i> Menü Sayfası Ayarları</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label>Menü Başlığı</label>
                            <input type="text" name="menu_title" class="form-control" value="<?= $settings['menu_title'] ?? 'Menümüz' ?>">
                        </div>
                        <div class="mb-3">
                            <label>Menü Alt Açıklaması</label>
                            <textarea name="menu_subtitle" class="form-control" rows="2"><?= $settings['menu_subtitle'] ?? '' ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="card mb-4 border-danger">
                    <div class="card-header bg-danger text-white"><i class="fas fa-calendar-alt"></i> Rezervasyon Sayfası Ayarları</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label>Sol Kısım Başlığı</label>
                            <input type="text" name="reservation_title" class="form-control" value="<?= $settings['reservation_title'] ?? 'Masayı Ayırt' ?>">
                        </div>
                        <div class="mb-3">
                            <label>Sol Kısım Açıklaması</label>
                            <textarea name="reservation_desc" class="form-control" rows="2"><?= $settings['reservation_desc'] ?? '' ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Hakkımızda -->
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">Hakkımızda Bölümü</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label>Başlık</label>
                            <input type="text" name="about_title" class="form-control" value="<?= $settings['about_title'] ?>">
                        </div>
                        <div class="mb-3">
                            <label>Açıklama Yazısı</label>
                            <textarea name="about_text" class="form-control" rows="5"><?= $settings['about_text'] ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold text-success"><i class="fab fa-whatsapp"></i> WhatsApp Numarası</label>
                            <input type="text" name="about_whatsapp" class="form-control" value="<?= $settings['about_whatsapp'] ?? '' ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <button type="submit" class="btn btn-success btn-lg w-100 mb-5"><i class="fas fa-save"></i> Ayarları Kaydet</button>
    </form>
</div>

<script>
function setTheme(mainColor, bgColor) {
    document.getElementById('themeColorInput').value = mainColor;
    document.getElementById('bgColorInput').value = bgColor;
}
</script>

<?php require_once 'includes/footer.php'; ?>