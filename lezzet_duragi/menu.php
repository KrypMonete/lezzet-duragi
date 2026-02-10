<?php 
require_once 'includes/header.php'; 

// 1. Kategorileri Çek
$stmt = $pdo->query("SELECT * FROM categories ORDER BY id ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2. Ürünleri Kategoriyle Beraber Çek (Aktif olanları)
$stmt = $pdo->query("SELECT * FROM products WHERE is_active = 1 ORDER BY id DESC");
$allProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ürünleri kategori ID'sine göre gruplayalım (Performans için)
$productsByCat = [];
foreach($allProducts as $prod) {
    $productsByCat[$prod['category_id']][] = $prod;
}
?>

<!-- MENÜ BAŞLIĞI (Hero) -->
<div class="py-5 bg-light text-center">
    <div class="container">
        <!-- DÜZELTME: Başlık ve Açıklama Veritabanından (Settings) Geliyor -->
        <h1 class="display-4 fw-bold animate__animated animate__fadeIn"><?= htmlspecialchars($settings['menu_title'] ?? 'Menümüz') ?></h1>
        <p class="lead text-muted"><?= htmlspecialchars($settings['menu_subtitle'] ?? 'Usta şeflerimizin ellerinden çıkan eşsiz lezzetleri keşfedin.') ?></p>
    </div>
</div>

<!-- MENÜ İÇERİĞİ -->
<div class="container py-5">
    
    <!-- KATEGORİ FİLTRELERİ (Nav Pills) -->
    <ul class="nav nav-pills justify-content-center mb-5 animate__animated animate__fadeInUp" id="menu-tabs" role="tablist">
        <!-- 'Hepsi' Sekmesi -->
        <li class="nav-item me-2" role="presentation">
            <button class="nav-link active rounded-pill px-4" id="pills-all-tab" data-bs-toggle="pill" data-bs-target="#pills-all" type="button" role="tab">
                <i class="fas fa-th-large me-2"></i>Tümü
            </button>
        </li>
        
        <!-- Veritabanından Gelen Kategoriler -->
        <?php foreach($categories as $cat): ?>
        <li class="nav-item me-2" role="presentation">
            <button class="nav-link rounded-pill px-4" id="pills-cat<?= $cat['id'] ?>-tab" data-bs-toggle="pill" data-bs-target="#pills-cat<?= $cat['id'] ?>" type="button" role="tab">
                <i class="fas <?= $cat['icon'] ?> me-2"></i><?= htmlspecialchars($cat['name']) ?>
            </button>
        </li>
        <?php endforeach; ?>
    </ul>

    <!-- ÜRÜN LİSTELERİ (Tab Content) -->
    <div class="tab-content" id="menu-tabContent">
        
        <!-- 1. TAB: TÜM ÜRÜNLER -->
        <div class="tab-pane fade show active" id="pills-all" role="tabpanel">
            <div class="row g-4">
                <?php if(empty($allProducts)): ?>
                    <div class="text-center text-muted py-5">Henüz menüye ürün eklenmemiş.</div>
                <?php endif; ?>

                <?php foreach($allProducts as $product): ?>
                    <!-- Ürün Kartı -->
                    <div class="col-md-6 col-lg-4 animate__animated animate__fadeIn">
                        <div class="card h-100 shadow-sm border-0 product-card">
                            <div class="overflow-hidden position-relative" style="height: 250px;">
                                <?php if($product['image_path']): ?>
                                    <img src="<?= $product['image_path'] ?>" class="card-img-top w-100 h-100 object-fit-cover transition-img" alt="<?= htmlspecialchars($product['name']) ?>">
                                <?php else: ?>
                                    <div class="w-100 h-100 bg-secondary d-flex align-items-center justify-content-center text-white">Resim Yok</div>
                                <?php endif; ?>
                                <span class="badge bg-primary-custom position-absolute top-0 end-0 m-3 fs-6">
                                    <?= number_format($product['price'], 2) ?> ₺
                                </span>
                            </div>
                            <div class="card-body text-center">
                                <h5 class="card-title fw-bold"><?= htmlspecialchars($product['name']) ?></h5>
                                <p class="card-text text-muted small text-truncate-2">
                                    <?= htmlspecialchars($product['description']) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 2. DİĞER KATEGORİ TABLARI (Döngü ile oluşturuyoruz) -->
        <?php foreach($categories as $cat): ?>
        <div class="tab-pane fade" id="pills-cat<?= $cat['id'] ?>" role="tabpanel">
            <div class="row g-4">
                <?php 
                // Bu kategoriye ait ürün var mı?
                $catProducts = $productsByCat[$cat['id']] ?? [];
                
                if(empty($catProducts)): ?>
                    <div class="text-center text-muted py-5">Bu kategoride henüz ürün yok.</div>
                <?php endif; ?>

                <?php foreach($catProducts as $product): ?>
                    <div class="col-md-6 col-lg-4 animate__animated animate__fadeIn">
                        <div class="card h-100 shadow-sm border-0 product-card">
                            <div class="overflow-hidden position-relative" style="height: 250px;">
                                <?php if($product['image_path']): ?>
                                    <img src="<?= $product['image_path'] ?>" class="card-img-top w-100 h-100 object-fit-cover transition-img" alt="<?= htmlspecialchars($product['name']) ?>">
                                <?php else: ?>
                                    <div class="w-100 h-100 bg-secondary d-flex align-items-center justify-content-center text-white">Resim Yok</div>
                                <?php endif; ?>
                                <span class="badge bg-primary-custom position-absolute top-0 end-0 m-3 fs-6">
                                    <?= number_format($product['price'], 2) ?> ₺
                                </span>
                            </div>
                            <div class="card-body text-center">
                                <h5 class="card-title fw-bold"><?= htmlspecialchars($product['name']) ?></h5>
                                <p class="card-text text-muted small">
                                    <?= htmlspecialchars($product['description']) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
        
    </div>
</div>

<!-- Ufak bir CSS Efekti -->
<style>
    /* Resimlerin üzerine gelince büyüme efekti */
    .product-card:hover .transition-img {
        transform: scale(1.1);
    }
    .transition-img {
        transition: transform 0.5s ease;
    }
    
    /* Aktif sekme (Nav Pill) */
    .nav-pills .nav-link.active {
        background-color: var(--primary-color) !important;
        color: #fff !important; /* DÜZELTME: Aktifken yazı rengi BEYAZ olsun */
    }
    
    /* Pasif sekme */
    .nav-pills .nav-link {
        color: var(--text-color);
        background-color: var(--light-bg);
    }
    
    /* Hover efekti */
    .nav-pills .nav-link:hover {
        background-color: rgba(0,0,0,0.1); /* Hafif karartma */
    }
</style>

<?php require_once 'includes/footer.php'; ?>