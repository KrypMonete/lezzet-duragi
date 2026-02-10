<?php require_once 'includes/header.php'; ?>

<!-- 1. HERO (GİRİŞ) BÖLÜMÜ -->
<header class="py-5" style="background: url('<?= $settings['hero_image'] ?>') no-repeat center center/cover; position: relative;">
    <div style="position: absolute; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.6);"></div>
    <div class="container position-relative py-5">
        <div class="row align-items-center py-5">
            <div class="col-lg-8 mx-auto text-center" style="text-shadow: 0 2px 4px rgba(0,0,0,0.8);">
                <h1 class="display-3 fw-bold mb-3 animate__animated animate__fadeInDown text-primary-custom">
                    <?= htmlspecialchars($settings['hero_title']) ?>
                </h1>
                <p class="lead mb-4 animate__animated animate__fadeInUp" style="color: #eee;">
                    <?= htmlspecialchars($settings['hero_subtitle']) ?>
                </p>
                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                    <a href="menu.php" class="btn btn-primary-custom btn-lg px-4 gap-3">Menüyü İncele</a>
                    <a href="reservation.php" class="btn btn-outline-light btn-lg px-4">Rezervasyon Yap</a>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- 2. HAKKIMIZDA BÖLÜMÜ -->
<section id="about" class="py-5">
    <div class="container py-4">
        <div class="row align-items-center">
            <div class="col-md-6 mb-4 mb-md-0">
                <img src="<?= $settings['logo_path'] ? $settings['logo_path'] : 'assets/img/about.jpg' ?>" 
                     class="img-fluid rounded shadow-lg" alt="Hakkımızda">
            </div>
            <div class="col-md-6">
                <h6 class="text-primary-custom fw-bold text-uppercase">Hikayemiz</h6>
                <h2 class="fw-bold mb-4"><?= htmlspecialchars($settings['about_title']) ?></h2>
                <p class="text-muted lead"><?= nl2br(htmlspecialchars($settings['about_text'])) ?></p>
                
                <!-- WHATSAPP BUTONU (YENİ AYAR KULLANILIYOR) -->
                <?php
                    // Admin panelinden gelen numarayı temizle (sadece rakam kalsın)
                    // Böylece kullanıcı yanlışlıkla boşluk veya parantez koysa bile link çalışır.
                    $rawPhone = $settings['about_whatsapp'] ?? '905550000000';
                    $cleanPhone = preg_replace('/[^0-9]/', '', $rawPhone);
                ?>
                <a href="https://wa.me/<?= $cleanPhone ?>?text=Merhaba, restoranınız hakkında bilgi almak istiyorum." 
                   target="_blank" 
                   class="btn btn-success mt-3 shadow-sm">
                    <i class="fab fa-whatsapp me-2"></i> Bize Ulaşın (WhatsApp)
                </a>
            </div>
        </div>
    </div>
</section>

<!-- 3. ŞEFİN ÖNERİSİ -->
<?php if(!empty($settings['chef_dish_name'])): ?>
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h6 class="text-primary-custom fw-bold text-uppercase">Özel Lezzet</h6>
            <h2 class="fw-bold"><?= htmlspecialchars($settings['chef_title']) ?></h2>
        </div>
        <div class="card border-0 shadow-sm overflow-hidden mb-3 mx-auto" style="max-width: 900px;">
            <div class="row g-0">
                <div class="col-md-6">
                    <?php if($settings['chef_dish_image']): ?>
                        <img src="<?= $settings['chef_dish_image'] ?>" class="img-fluid h-100 object-fit-cover" style="min-height: 300px;" alt="Şefin Önerisi">
                    <?php else: ?>
                        <div class="bg-secondary h-100 d-flex align-items-center justify-content-center text-white">Resim Yok</div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6 d-flex align-items-center">
                    <div class="card-body p-5">
                        <h3 class="card-title fw-bold mb-3"><?= htmlspecialchars($settings['chef_dish_name']) ?></h3>
                        <p class="card-text text-muted mb-4"><?= htmlspecialchars($settings['chef_dish_desc']) ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="text-primary-custom fw-bold mb-0"><?= number_format($settings['chef_dish_price'], 2) ?> ₺</h4>
                            <a href="menu.php" class="btn btn-dark btn-sm rounded-pill px-4">Sipariş Ver</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 4. SİZDEN GELENLER (Slider Yapısı) -->
<?php
$commentsQuery = $pdo->query("SELECT c.*, u.name as user_name FROM comments c LEFT JOIN users u ON c.user_id = u.id WHERE c.is_approved = 1 ORDER BY c.created_at DESC");
$allComments = $commentsQuery->fetchAll(PDO::FETCH_ASSOC);
$chunks = array_chunk($allComments, 3);
?>

<?php if(count($chunks) > 0): ?>
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h6 class="text-primary-custom fw-bold text-uppercase">Müşteri Deneyimleri</h6>
            <h2 class="fw-bold">Sizden Gelenler</h2>
        </div>
        
        <div id="commentsCarousel" class="carousel slide carousel-dark" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php foreach($chunks as $index => $chunk): ?>
                    <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                        <div class="row">
                            <?php foreach($chunk as $comment): ?>
                            <div class="col-md-4 mb-4">
                                <div class="card h-100 border-0 shadow-sm p-3">
                                    <div class="card-body d-flex flex-column">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-primary-custom text-white rounded-circle d-flex justify-content-center align-items-center fw-bold" style="width: 50px; height: 50px; font-size: 1.2rem;">
                                                <?= strtoupper(substr($comment['user_name'], 0, 1)) ?>
                                            </div>
                                            <div class="ms-3">
                                                <h5 class="mb-0 fw-bold"><?= htmlspecialchars($comment['user_name']) ?></h5>
                                                <small class="text-muted"><?= date('d.m.Y', strtotime($comment['created_at'])) ?></small>
                                            </div>
                                        </div>
                                        <div class="mb-3 text-warning">
                                            <?php for($i=0; $i<$comment['rating']; $i++) echo '<i class="fas fa-star"></i>'; ?>
                                        </div>
                                        <p class="card-text text-muted flex-grow-1">
                                            "<?= htmlspecialchars($comment['comment']) ?>"
                                        </p>
                                        
                                        <?php if(!empty($comment['reply'])): ?>
                                            <div class="mt-3 p-2 bg-light border-start border-4 border-secondary rounded small">
                                                <strong class="text-primary-custom" style="font-size: 0.85rem;">
                                                    <i class="fas fa-reply me-1"></i> Restoranın Cevabı:
                                                </strong>
                                                <div class="text-muted fst-italic mt-1">
                                                    <?= htmlspecialchars($comment['reply']) ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php if(count($chunks) > 1): ?>
                <button class="carousel-control-prev" type="button" data-bs-target="#commentsCarousel" data-bs-slide="prev" style="width: 5%; justify-content: flex-start;">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Önceki</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#commentsCarousel" data-bs-slide="next" style="width: 5%; justify-content: flex-end;">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Sonraki</span>
                </button>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 5. İLETİŞİM BÖLÜMÜ -->
<section id="contact" class="py-5 bg-light">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto">
                <h6 class="text-primary-custom fw-bold text-uppercase">Bize Ulaşın</h6>
                <h2 class="fw-bold">İletişim & Konum</h2>
                <p class="text-muted">Sorularınız için bizimle iletişime geçebilir veya restoranımızı ziyaret edebilirsiniz.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 text-center mb-4">
                <div class="bg-white p-4 rounded shadow-sm h-100">
                    <i class="fas fa-map-marker-alt fa-3x text-primary-custom mb-3"></i>
                    <h5 class="fw-bold">Adresimiz</h5>
                    <p class="text-muted"><?= nl2br(htmlspecialchars($settings['address'])) ?></p>
                </div>
            </div>
            <div class="col-md-4 text-center mb-4">
                <div class="bg-white p-4 rounded shadow-sm h-100">
                    <i class="fas fa-phone fa-3x text-primary-custom mb-3"></i>
                    <h5 class="fw-bold">Telefon</h5>
                    <p class="text-muted"><?= htmlspecialchars($settings['phone']) ?></p>
                </div>
            </div>
            <div class="col-md-4 text-center mb-4">
                <div class="bg-white p-4 rounded shadow-sm h-100">
                    <i class="fas fa-clock fa-3x text-primary-custom mb-3"></i>
                    <h5 class="fw-bold">Çalışma Saatleri</h5>
                    <p class="text-muted"><?= htmlspecialchars($settings['working_hours']) ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>