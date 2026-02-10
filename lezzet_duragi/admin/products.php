<?php
require_once 'includes/header.php';

// --- SİLME İŞLEMİ ---
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Önce eski resmi bul ve sil (sunucuda yer kaplamasın)
    $stmt = $pdo->prepare("SELECT image_path FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $img = $stmt->fetchColumn();
    
    if ($img && file_exists("../" . $img)) {
        unlink("../" . $img); // Dosyayı klasörden sil
    }

    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    
    header("Location: products.php?success=silindi");
    exit;
}

// --- DÜZENLEME MODU İÇİN VERİ ÇEKME (GET İsteği) ---
$editMode = false;
$editProduct = [];

if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $editProduct = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($editProduct) $editMode = true;
}

// --- EKLEME VE GÜNCELLEME İŞLEMİ (POST İsteği) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Düzenleme ise ID'yi hidden input'tan al
    $id = $_POST['product_id'] ?? ''; 

    // Resim Yükleme İşlemleri
    // Eğer düzenleme yapıyorsak ve yeni resim seçilmemişse eskisi kalsın
    $image_path = '';
    if (!empty($id)) {
        // Mevcut resim yolunu veritabanından çekelim ki kaybolmasın
        $stmtOld = $pdo->prepare("SELECT image_path FROM products WHERE id = ?");
        $stmtOld->execute([$id]);
        $image_path = $stmtOld->fetchColumn();
    }
    
    if (!empty($_FILES['image']['name'])) {
        $upload_dir = '../uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true); // Klasör yoksa oluştur
        
        $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (in_array($file_ext, $allowed)) {
            $new_name = "yemek_" . time() . "." . $file_ext;
            $target = $upload_dir . $new_name;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                // Eğer eski resim varsa ve yeni resim yüklendiyse eskisini sil
                if (!empty($image_path) && file_exists("../" . $image_path)) {
                    unlink("../" . $image_path);
                }
                $image_path = 'uploads/' . $new_name;
            }
        } else {
            $error = "Sadece JPG, PNG ve WEBP formatları yüklenebilir.";
        }
    }

    if (!isset($error)) {
        if (!empty($id)) {
            // ID VARSA -> GÜNCELLEME (UPDATE)
            // Buradaki mantığı düzelttik: Artık $editMode'a değil, formdan gelen ID'ye bakıyoruz.
            $sql = "UPDATE products SET category_id=?, name=?, description=?, price=?, image_path=?, is_active=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$category_id, $name, $description, $price, $image_path, $is_active, $id]);
            $success = "Ürün güncellendi.";
            
            // İşlem bitince temiz sayfaya dön
            echo "<script>window.location.href='products.php';</script>";
        } else {
            // ID YOKSA -> EKLEME (INSERT)
            $sql = "INSERT INTO products (category_id, name, description, price, image_path, is_active) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$category_id, $name, $description, $price, $image_path, $is_active]);
            $success = "Yeni ürün eklendi.";
        }
    }
}

// Ürünleri Listele
$sql = "SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        ORDER BY p.id DESC";
$products = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// Kategorileri Çek
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
    <h2 class="mb-4">Yemek ve Menü Yönetimi</h2>

    <div class="row">
        <!-- SOL: Ürün Ekleme/Düzenleme Formu -->
        <div class="col-md-4">
            <div class="card border-<?= $editMode ? 'warning' : 'dark' ?>">
                <div class="card-header <?= $editMode ? 'bg-warning text-dark' : 'bg-dark text-white' ?>">
                    <?= $editMode ? '<i class="fas fa-edit"></i> Ürünü Düzenle' : '<i class="fas fa-plus"></i> Yeni Yemek Ekle' ?>
                </div>
                <div class="card-body">
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    <?php if(isset($success) || (isset($_GET['success']) && $_GET['success']=='silindi')): ?>
                        <div class="alert alert-success">İşlem Başarılı!</div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data" action="products.php">
                        <!-- GİZLİ ID ALANI: Güncelleme için kritik önem taşıyor -->
                        <input type="hidden" name="product_id" value="<?= $editMode ? $editProduct['id'] : '' ?>">

                        <div class="mb-3">
                            <label>Kategori</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Seçiniz...</option>
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= ($editMode && $editProduct['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Yemek Adı</label>
                            <input type="text" name="name" class="form-control" required 
                                   value="<?= $editMode ? $editProduct['name'] : '' ?>">
                        </div>

                        <div class="mb-3">
                            <label>Açıklama (İçindekiler vs.)</label>
                            <textarea name="description" class="form-control" rows="2"><?= $editMode ? $editProduct['description'] : '' ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label>Fiyat (TL)</label>
                            <div class="input-group">
                                <input type="number" step="0.50" name="price" class="form-control" required 
                                       value="<?= $editMode ? $editProduct['price'] : '' ?>">
                                <span class="input-group-text">₺</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Yemek Fotoğrafı</label>
                            <?php if($editMode && $editProduct['image_path']): ?>
                                <div class="mb-2">
                                    <img src="../<?= $editProduct['image_path'] ?>" width="80" class="rounded">
                                </div>
                            <?php endif; ?>
                            <input type="file" name="image" class="form-control">
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="is_active" id="activeCheck" 
                                   <?= (!$editMode || ($editMode && $editProduct['is_active'])) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="activeCheck">
                                Menüde Göster (Aktif)
                            </label>
                        </div>

                        <button type="submit" class="btn <?= $editMode ? 'btn-warning' : 'btn-primary' ?> w-100">
                            <?= $editMode ? 'Değişiklikleri Kaydet' : 'Yemeği Ekle' ?>
                        </button>
                        
                        <?php if($editMode): ?>
                            <a href="products.php" class="btn btn-secondary w-100 mt-2">İptal</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>

        <!-- SAĞ: Ürün Listesi -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-secondary text-white">Menü Listesi</div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="80">Resim</th>
                                <th>Yemek Adı</th>
                                <th>Kategori</th>
                                <th>Fiyat</th>
                                <th>Durum</th>
                                <th class="text-end">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $prod): ?>
                            <tr>
                                <td>
                                    <?php if($prod['image_path']): ?>
                                        <img src="../<?= $prod['image_path'] ?>" width="60" height="40" class="rounded object-fit-cover">
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Resim Yok</span>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold"><?= htmlspecialchars($prod['name']) ?></td>
                                <td><span class="badge bg-info text-dark"><?= htmlspecialchars($prod['category_name']) ?></span></td>
                                <td class="text-success fw-bold"><?= number_format($prod['price'], 2) ?> ₺</td>
                                <td>
                                    <?php if($prod['is_active']): ?>
                                        <span class="badge bg-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Pasif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <a href="products.php?edit=<?= $prod['id'] ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                    <a href="products.php?delete=<?= $prod['id'] ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Bu yemeği silmek istediğinize emin misiniz?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <?php if(empty($products)): ?>
                                <tr><td colspan="6" class="text-center p-3">Henüz yemek eklenmemiş.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>