<!-- FOOTER -->
<footer class="bg-dark text-white pt-5 pb-3 mt-5">
    <div class="container">
        <div class="row">
            <!-- Sol: Logo ve Slogan -->
            <div class="col-md-4 mb-4">
                <h4 class="text-primary-custom fw-bold"><?= $settings['site_title'] ?></h4>
                <p class="text-secondary mt-3">
                    Enfes lezzetlerin ve keyifli anların buluşma noktası. 
                    Damak tadınıza uygun, özenle hazırlanmış menümüzle hizmetinizdeyiz.
                </p>
               
            </div>

            <!-- Orta: Hızlı Linkler -->
            <div class="col-md-4 mb-4 text-center">
                <h5 class="text-white mb-3">Hızlı Erişim</h5>
                <ul class="list-unstyled">
                    <li><a href="index.php" class="text-secondary text-decoration-none">Ana Sayfa</a></li>
                    <li><a href="menu.php" class="text-secondary text-decoration-none">Menü</a></li>
                    <li><a href="reservation.php" class="text-secondary text-decoration-none">Rezervasyon Yap</a></li>
                    <li><a href="index.php#contact" class="text-secondary text-decoration-none">İletişim</a></li>
                </ul>
            </div>

            <!-- Sağ: İletişim (Admin Panelinden Geliyor) -->
            <div class="col-md-4 mb-4">
                <h5 class="text-white mb-3">İletişim Bilgileri</h5>
                <ul class="list-unstyled text-secondary">
                    <li class="mb-2"><i class="fas fa-map-marker-alt me-2 text-primary-custom"></i> <?= nl2br($settings['address']) ?></li>
                    <li class="mb-2"><i class="fas fa-phone me-2 text-primary-custom"></i> <?= $settings['phone'] ?></li>
                    <li class="mb-2"><i class="fas fa-envelope me-2 text-primary-custom"></i> <?= $settings['email'] ?></li>
                    <li class="mb-2"><i class="fas fa-clock me-2 text-primary-custom"></i> <?= $settings['working_hours'] ?></li>
                </ul>
            </div>
        </div>
        <hr class="border-secondary">
        <div class="text-center text-secondary small">
            &copy; <?= date('Y') ?> <?= $settings['site_title'] ?>. Tüm hakları saklıdır.
        </div>
    </div>
</footer>

<!-- JS Dosyaları -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>