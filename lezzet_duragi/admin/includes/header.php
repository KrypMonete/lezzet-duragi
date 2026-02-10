<?php
ob_start(); // HATA DÜZELTİCİ: Çıktı tamponlamayı başlat (En tepeye ekledik)
session_start();
require_once '../includes/db.php';

// GÜVENLİK DUVARI
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'personel'])) {
    header("Location: login.php");
    exit;
}

// Şu anki sayfanın adını öğrenelim
$current_page = basename($_SERVER['PHP_SELF']);

// --- BİLDİRİM SORGULARI ---

// 1. Bekleyen Yorum Sayısı
$stmtComment = $pdo->query("SELECT COUNT(*) FROM comments WHERE is_approved = 0");
$pendingCommentCount = $stmtComment->fetchColumn();

// 2. Bekleyen Rezervasyon Sayısı
$stmtRes = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status = 'bekliyor'");
$pendingReservationCount = $stmtRes->fetchColumn();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yönetim Paneli | Lezzet Durağı</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { min-height: 100vh; background: #f8f9fa; }
        
        /* SABİT SIDEBAR */
        .sidebar { 
            width: 250px; 
            background: #2c3e50; 
            color: #fff; 
            height: 100vh; 
            position: fixed; 
            top: 0;
            left: 0;
            display: flex; 
            flex-direction: column; 
            overflow-y: auto; 
            z-index: 1000;
        }
        
        .sidebar .nav-link { 
            color: #b0b8c1; 
            margin-bottom: 5px; 
            transition: 0.3s; 
            border-radius: 5px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
        }
        
        .sidebar .nav-link:hover { color: #fff; background: rgba(255,255,255,0.1); }
        
        .sidebar .nav-link.active { 
            color: #fff; 
            background: #ff6347;
            font-weight: bold;
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
        }
        
        .sidebar .nav-link i.fa-fw { width: 25px; } 
        
        /* İÇERİK ALANI (Menü sabit olduğu için sağa itildi) */
        .content { 
            margin-left: 250px; 
            padding: 20px; 
            min-height: 100vh;
        }
    </style>
</head>
<body>

<!-- SOL MENÜ -->
<div class="sidebar p-3">
    <h4 class="text-center mb-4 text-white">Lezzet Durağı</h4>
    <div class="text-center mb-3">
        <small class="text-muted">Hoşgeldin,</small><br>
        <strong><?= $_SESSION['name'] ?></strong><br>
        <span class="badge bg-secondary"><?= ucfirst($_SESSION['role']) ?></span>
    </div>
    <hr>
    
    <!-- Ana Menü Linkleri -->
    <ul class="nav flex-column mb-auto">
        <li class="nav-item">
            <a href="index.php" class="nav-link <?= $current_page == 'index.php' ? 'active' : '' ?>">
                <span><i class="fas fa-home fa-fw"></i> Dashboard</span>
            </a>
        </li>

        <!-- SADECE ADMIN GÖREBİLİR -->
        <?php if($_SESSION['role'] == 'admin'): ?>
        <li class="nav-item">
            <a href="settings.php" class="nav-link <?= $current_page == 'settings.php' ? 'active' : '' ?>">
                <span><i class="fas fa-cog fa-fw"></i> Site Ayarları</span>
            </a>
        </li>
        <?php endif; ?>

        <li class="nav-item">
            <a href="categories.php" class="nav-link <?= $current_page == 'categories.php' ? 'active' : '' ?>">
                <span><i class="fas fa-list fa-fw"></i> Kategoriler</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="products.php" class="nav-link <?= $current_page == 'products.php' ? 'active' : '' ?>">
                <span><i class="fas fa-hamburger fa-fw"></i> Yemekler & Menü</span>
            </a>
        </li>
        
        <!-- REZERVASYONLAR (BİLDİRİMLİ) -->
        <li class="nav-item">
            <a href="reservations.php" class="nav-link <?= $current_page == 'reservations.php' ? 'active' : '' ?>">
                <span><i class="fas fa-calendar-check fa-fw"></i> Rezervasyonlar</span>
                
                <?php if($pendingReservationCount > 0): ?>
                    <span class="badge bg-danger rounded-pill"><?= $pendingReservationCount ?></span>
                <?php endif; ?>
            </a>
        </li>
        
        <!-- MÜŞTERİ YORUMLARI (BİLDİRİMLİ) -->
        <li class="nav-item">
            <a href="comments.php" class="nav-link <?= $current_page == 'comments.php' ? 'active' : '' ?>">
                <span><i class="fas fa-comments fa-fw"></i> Müşteri Yorumları</span>
                
                <?php if($pendingCommentCount > 0): ?>
                    <span class="badge bg-danger rounded-pill"><?= $pendingCommentCount ?></span>
                <?php endif; ?>
            </a>
        </li>

        <!-- SADECE ADMIN GÖREBİLİR -->
        <?php if($_SESSION['role'] == 'admin'): ?>
        <li class="nav-item">
            <a href="users.php" class="nav-link <?= $current_page == 'users.php' ? 'active' : '' ?>">
                <span><i class="fas fa-users fa-fw"></i> Kullanıcılar</span>
            </a>
        </li>
        <?php endif; ?>
    </ul>

    <!-- Alt Menü -->
    <ul class="nav flex-column mt-4 pt-3 border-top border-secondary">
        <li class="nav-item mb-2">
            <a href="../index.php" target="_blank" class="nav-link text-warning">
                <span><i class="fas fa-globe fa-fw"></i> Siteyi Görüntüle</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="logout.php" class="nav-link text-danger">
                <span><i class="fas fa-sign-out-alt fa-fw"></i> Çıkış Yap</span>
            </a>
        </li>
    </ul>
</div>

<!-- İÇERİK BAŞLANGICI -->
<div class="content">