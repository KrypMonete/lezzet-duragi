<?php
require_once 'includes/header.php';

// --- SİLME İŞLEMİ ---
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Önce bu kategoride ürün var mı kontrol et
    $check = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
    $check->execute([$id]);
    $count = $check->fetchColumn();

    if ($count > 0) {
        $error = "Bu kategoriye ait ürünler var! Önce onları silmelisiniz.";
    } else {
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: categories.php?success=silindi");
        exit;
    }
}

// --- DÜZENLEME MODU İÇİN VERİ ÇEKME ---
$editMode = false;
$editCategory = [];

if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    $editCategory = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($editCategory) {
        $editMode = true;
    }
}

// --- EKLEME VE GÜNCELLEME İŞLEMİ ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $icon = $_POST['icon']; // FontAwesome class'ı
    $id = $_POST['category_id'] ?? ''; // Hidden input'tan gelen ID (Varsa güncelleme yapacağız)

    if (!empty($name)) {
        if (!empty($id)) {
            // GÜNCELLEME (UPDATE)
            $stmt = $pdo->prepare("UPDATE categories SET name = ?, icon = ? WHERE id = ?");
            $stmt->execute([$name, $icon, $id]);
            $success = "Kategori başarıyla güncellendi.";
            // Güncelleme bitince temiz sayfaya dönelim (edit modundan çıkmak için)
            echo "<script>setTimeout(function(){ window.location.href='categories.php'; }, 1500);</script>";
        } else {
            // EKLEME (INSERT)
            $stmt = $pdo->prepare("INSERT INTO categories (name, icon) VALUES (?, ?)");
            $stmt->execute([$name, $icon]);
            $success = "Kategori başarıyla eklendi.";
        }
    } else {
        $error = "Lütfen kategori adını yazınız.";
    }
}

// Kategorileri Listele
$categories = $pdo->query("SELECT * FROM categories ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
    <h2 class="mb-4">Kategori Yönetimi</h2>

    <div class="row">
        <!-- SOL: Kategori Ekleme/Düzenleme Formu -->
        <div class="col-md-4">
            <div class="card border-<?= $editMode ? 'warning' : 'dark' ?>">
                <div class="card-header <?= $editMode ? 'bg-warning text-dark' : 'bg-dark text-white' ?>">
                    <?= $editMode ? '<i class="fas fa-edit"></i> Kategoriyi Düzenle' : '<i class="fas fa-plus"></i> Yeni Kategori Ekle' ?>
                </div>
                <div class="card-body">
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    <?php if(isset($success) || (isset($_GET['success']) && $_GET['success']=='silindi')): ?>
                        <div class="alert alert-success">İşlem Başarılı!</div>
                    <?php endif; ?>

                    <!-- Form action'ı boş bırakıyoruz ki aynı sayfaya post etsin -->
                    <form method="POST" action="categories.php">
                        
                        <!-- Eğer düzenleme modundaysak ID'yi gizli olarak gönderelim -->
                        <?php if($editMode): ?>
                            <input type="hidden" name="category_id" value="<?= $editCategory['id'] ?>">
                        <?php endif; ?>

                        <div class="mb-3">
                            <label>Kategori Adı</label>
                            <input type="text" name="name" class="form-control" 
                                   value="<?= $editMode ? $editCategory['name'] : '' ?>" 
                                   placeholder="Örn: Tatlılar" required>
                        </div>
                        
                        <div class="mb-3">
                            <label>İkon Seçimi</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-icons"></i></span>
                                <select name="icon" class="form-select">
                                    <?php
                                    $icons = [
                                        'fa-utensils' => '🍴 Genel / Çatal Bıçak',
                                        'fa-concierge-bell' => '🛎️ Servis / Spesiyal',
                                        'fa-fire' => '🔥 Sıcak / Izgara',
                                        'fa-leaf' => '🍃 Sağlıklı / Vegan',
                                        'fa-hamburger' => '🍔 Hamburger',
                                        'fa-pizza-slice' => '🍕 Pizza',
                                        'fa-hotdog' => '🌭 Sosisli / Fast Food',
                                        'fa-drumstick-bite' => '🍗 Tavuk / Et',
                                        'fa-fish' => '🐟 Deniz Ürünleri',
                                        'fa-bread-slice' => '🍞 Ekmek / Hamur İşi',
                                        'fa-cheese' => '🧀 Peynir / Meze',
                                        'fa-bowl-rice' => '🍜 Çorba / Makarna',
                                        'fa-coffee' => '☕ Kahve',
                                        'fa-mug-hot' => '🍵 Çay / Sıcak İçecek',
                                        'fa-wine-glass' => '🍷 Şarap / Kadeh',
                                        'fa-beer' => '🍺 Bira / Soğuk İçecek',
                                        'fa-cocktail' => '🍹 Kokteyl',
                                        'fa-ice-cream' => '🍨 Dondurma / Tatlı',
                                        'fa-cookie' => '🍪 Kurabiye',
                                        'fa-birthday-cake' => '🎂 Pasta',
                                        'fa-carrot' => '🥕 Sebze / Salata',
                                        'fa-apple-alt' => '🍎 Meyve',
                                        'fa-pepper-hot' => '🌶️ Acı / Baharatlı'
                                    ];
                                    foreach($icons as $val => $label):
                                        $selected = ($editMode && $editCategory['icon'] == $val) ? 'selected' : '';
                                    ?>
                                        <option value="<?= $val ?>" <?= $selected ?>><?= $label ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <small class="text-muted">Menüde ismin yanında görünecek ikon.</small>
                        </div>

                        <button type="submit" class="btn <?= $editMode ? 'btn-warning' : 'btn-success' ?> w-100">
                            <?= $editMode ? 'Değişiklikleri Kaydet' : 'Ekle' ?>
                        </button>
                        
                        <?php if($editMode): ?>
                            <a href="categories.php" class="btn btn-secondary w-100 mt-2">İptal</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>

        <!-- SAĞ: Kategori Listesi Tablosu -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Mevcut Kategoriler</div>
                <div class="card-body p-0">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>İkon</th>
                                <th>Kategori Adı</th>
                                <th class="text-end">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td><?= $cat['id'] ?></td>
                                <td><i class="fas <?= $cat['icon'] ?> fa-lg text-primary"></i></td>
                                <td class="fw-bold"><?= htmlspecialchars($cat['name']) ?></td>
                                <td class="text-end">
                                    <!-- Düzenle Butonu -->
                                    <a href="categories.php?edit=<?= $cat['id'] ?>" class="btn btn-sm btn-warning me-1">
                                        <i class="fas fa-edit"></i> Düzenle
                                    </a>

                                    <!-- Silme Butonu (Javascript Onaylı) -->
                                    <a href="categories.php?delete=<?= $cat['id'] ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Bu kategoriyi silmek istediğinize emin misiniz?');">
                                        <i class="fas fa-trash"></i> Sil
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>

                            <?php if(empty($categories)): ?>
                                <tr><td colspan="4" class="text-center">Henüz kategori eklenmemiş.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>