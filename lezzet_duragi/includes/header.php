<?php
session_start();
require_once 'db.php';

// Site Ayarlarını Veritabanından Çek
$stmt = $pdo->query("SELECT * FROM settings WHERE id = 1");
$settings = $stmt->fetch(PDO::FETCH_ASSOC);

// Varsayılan değerler
$themeColor = $settings['theme_color'] ?? '#ff6347';
$bgColor = $settings['bg_color'] ?? '#ffffff';
$fontFamily = $settings['font_family'] ?? 'Poppins';
$siteTitle = $settings['site_title'] ?? 'Lezzet Durağı';

// --- ZEKİ RENK AYARLAYICI ---
function getContrastColor($hexColor) {
    $hexColor = str_replace('#', '', $hexColor);
    $r = hexdec(substr($hexColor, 0, 2));
    $g = hexdec(substr($hexColor, 2, 2));
    $b = hexdec(substr($hexColor, 4, 2));
    $brightness = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
    return $brightness > 128 ? '#212529' : '#ffffff';
}

$textColor = getContrastColor($bgColor);
$isDark = ($textColor == '#ffffff'); 

$navbarClass = $isDark ? 'navbar-dark bg-dark' : 'navbar-light bg-white';

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($siteTitle) ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=<?= urlencode($fontFamily) ?>:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: <?= $themeColor ?>;
            --bg-color: <?= $bgColor ?>;
            --text-color: <?= $textColor ?>;
            --muted-color: <?= $isDark ? '#adb5bd' : '#6c757d' ?>;
            --card-bg: <?= $isDark ? '#212529' : '#ffffff' ?>;
            --light-bg: <?= $isDark ? '#2c3e50' : '#f8f9fa' ?>;
            --border-color: <?= $isDark ? '#495057' : 'rgba(0,0,0,.125)' ?>;
        }
        
        body { 
            font-family: '<?= $fontFamily ?>', sans-serif; 
            background-color: var(--bg-color);
            color: var(--text-color);
        }

        h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6 { color: var(--text-color); }
        .text-muted, .text-secondary { color: var(--muted-color) !important; }
        .bg-light { background-color: var(--light-bg) !important; color: var(--text-color) !important; }
        .bg-white { background-color: var(--card-bg) !important; color: var(--text-color) !important; }
        .card { background-color: var(--card-bg); color: var(--text-color); border: 1px solid var(--border-color); }
        .dropdown-menu { background-color: var(--card-bg); border-color: var(--border-color); }
        .dropdown-item { color: var(--text-color); }
        .dropdown-item:hover { background-color: var(--light-bg); color: var(--text-color); }
        .dropdown-divider { border-top-color: var(--border-color); }

        .text-primary-custom { color: var(--primary-color) !important; }
        .bg-primary-custom { background-color: var(--primary-color) !important; }
        .btn-primary-custom { background-color: var(--primary-color); border-color: var(--primary-color); color: #fff; }
        .btn-primary-custom:hover { filter: brightness(85%); color: #fff; }
        
        .navbar-brand img { max-height: 40px; }
        .nav-link { font-weight: 500; color: var(--text-color); transition: 0.3s; } 
        .nav-link:hover { color: var(--primary-color) !important; }
        .nav-link.active { color: var(--primary-color) !important; font-weight: 700; border-bottom: 2px solid var(--primary-color); }
        
        /* DUYURU ŞERİDİ STİLİ */
        .announcement-bar {
            background-color: var(--primary-color);
            color: #fff;
            text-align: center;
            padding: 8px 0;
            font-size: 0.9rem;
            font-weight: 600;
            animation: fadeInDown 0.5s ease-out;
        }
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg <?= $navbarClass ?> shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
            <?php if(!empty($settings['logo_path'])): ?>
                <img src="<?= $settings['logo_path'] ?>" alt="Logo">
            <?php endif; ?>
            <h3 class="text-primary-custom fw-bold mb-0" style="font-size: 1.5rem;"><?= htmlspecialchars($siteTitle) ?></h3>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link <?= ($current_page == 'index.php') ? 'active' : '' ?>" href="index.php">Ana Sayfa</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php#about">Hakkımızda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($current_page == 'menu.php') ? 'active' : '' ?>" href="menu.php">Menü</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($current_page == 'reservation.php') ? 'active' : '' ?>" href="reservation.php">Rezervasyon</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php#contact">İletişim</a>
                </li>
            </ul>

            <div class="d-flex">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <div class="dropdown">
                        <button class="btn btn-outline-<?= $isDark ? 'light' : 'dark' ?> dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> <?= explode(' ', $_SESSION['name'])[0] ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php if($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'personel'): ?>
                                <li><a class="dropdown-item" href="admin/index.php">Yönetim Paneli</a></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="profile.php">Profilim</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php">Çıkış Yap</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary-custom me-2 px-4">Giriş Yap</a>
                    <a href="register.php" class="btn btn-outline-<?= $isDark ? 'light' : 'dark' ?> px-4">Kayıt Ol</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- DUYURU ŞERİDİ (NAVBAR'IN HEMEN ALTINDA) -->
<?php if(isset($settings['announcement_active']) && $settings['announcement_active']): ?>
    <div class="announcement-bar">
        <i class="fas fa-bullhorn me-2"></i> <?= htmlspecialchars($settings['announcement_text']) ?>
    </div>
<?php endif; ?>