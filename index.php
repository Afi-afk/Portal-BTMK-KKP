<?php
// 1. Mulakan sesi jika belum bermula
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Muat naik fail sandaran (Dependencies) menggunakan __DIR__
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/lang.php';
require_once __DIR__ . '/visitor_counter.php'; // Diubah untuk mengelakkan isu path

// 3. Tetapan bahasa lalai
$lang = isset($lang) ? $lang : 'bm';

// 4. Semak status log masuk
$is_logged_in = isset($_SESSION['role']);
$dashboard_url = $is_logged_in ? ($_SESSION['role'] === 'admin' ? 'dashboard_admin.php' : 'dashboard_staf.php') : 'login.php';

// 5. Semak mesej flash
$flash_success = isset($_SESSION['flash_success']) ? $_SESSION['flash_success'] : null;
$flash_error   = isset($_SESSION['flash_error']) ? $_SESSION['flash_error'] : null;

// 6. Padam mesej flash selepas dibaca
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>

<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($lang, ENT_QUOTES, 'UTF-8'); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo (isset($lang) && $lang === 'en') ? 'Home - Information Technology and Communication Division &amp; Occupational Safety Health Portal Yayasan Sabah Group' : 'Halaman Utama - Portal Bahagian Teknologi Maklumat dan Komunikasi &amp; Keselamatan Kesihatan Pekerjaan Kumpulan Yayasan Sabah'; ?></title>
    <link rel="stylesheet" href="assets/style.css?v=<?php echo time(); ?>">
    <style>
        :root {
            --primary-blue: #002B49;
            --accent-gold: #D4AF37;
            --sabah-red: #C8102E;
            --bg-light: #f8fafc;
            --card-border: #e2e8f0;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-light);
            margin: 0;
            padding: 0;
            color: #1e293b;
        }

        /* Royal Sabah Diamond Pattern */
        .sabah-pattern-border {
            height: 18px;
            width: 100%;
            background-color: var(--primary-blue);
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="32" height="18" viewBox="0 0 32 18"><path d="M16 0 L32 9 L16 18 L0 9 Z" fill="%23C8102E"/><path d="M16 3 L27 9 L16 15 L5 9 Z" fill="%23D4AF37"/><path d="M16 6 L22 9 L16 12 L10 9 Z" fill="%23002B49"/><circle cx="16" cy="9" r="1.5" fill="%23ffffff"/></svg>');
            background-repeat: repeat-x;
            background-size: 32px 18px;
            border-top: 1px solid var(--accent-gold);
            border-bottom: 1px solid var(--accent-gold);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.4);
        }

        /* Hero Slideshow Banner */
        .hero-slideshow-container {
            position: relative;
            width: 100%;
            min-height: 440px;
            overflow: hidden;
            background-color: #001524;
        }

        .slide-item {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 1s ease-in-out;
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .slide-item.active {
            opacity: 1;
        }

        .slide-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(0, 43, 73, 0.82), rgba(0, 21, 36, 0.88));
        }

        .hero-content {
            position: relative;
            z-index: 2;
            color: #ffffff;
            text-align: center;
            padding: 4rem 1.5rem;
            max-width: 900px;
            margin: 0 auto;
        }

        .hero-crests {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            margin-bottom: 1rem;
        }

        .hero-crests img {
            height: 58px;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0px 3px 6px rgba(0,0,0,0.6));
        }

        .hero-crests img.logo-ys-center {
            height: 65px;
            mix-blend-mode: multiply;
        }

        .hero-content h1 {
            font-size: 2.2rem;
            margin: 0.5rem 0;
            font-weight: 800;
            letter-spacing: 0.8px;
            text-shadow: 0px 2px 8px rgba(0,0,0,0.8);
        }

        .hero-content p {
            font-size: 1.1rem;
            color: var(--accent-gold);
            margin: 0 0 1.5rem 0;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-shadow: 0px 1px 4px rgba(0,0,0,0.8);
        }

        .btn-login-hero {
            display: inline-block;
            background: linear-gradient(135deg, var(--accent-gold), #f39c12);
            color: var(--primary-blue);
            padding: 0.85rem 2.2rem;
            border-radius: 50px;
            font-weight: bold;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.4);
            font-size: 0.95rem;
        }

        .btn-login-hero:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.6);
        }

        /* Slideshow Controls */
        .slide-dots {
            position: absolute;
            bottom: 15px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 3;
            display: flex;
            gap: 8px;
        }

        .dot {
            width: 12px;
            height: 12px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .dot.active {
            background: var(--accent-gold);
            width: 28px;
            border-radius: 10px;
        }

        .container {
            max-width: 1200px;
            margin: 2.5rem auto;
            padding: 0 1.5rem;
        }

        .section-title {
            color: var(--primary-blue);
            font-size: 1.5rem;
            margin-bottom: 1.2rem;
            border-left: 5px solid var(--accent-gold);
            padding-left: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* Visitor Counter Bar */
        .visitor-counter-bar {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 10px;
            padding: 0.9rem 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
            flex-wrap: wrap;
            gap: 10px;
        }

        .visitor-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
            color: #475569;
        }

        .visitor-number {
            font-weight: 800;
            font-size: 1.1rem;
            color: var(--primary-blue);
            background: #e2e8f0;
            padding: 0.2rem 0.6rem;
            border-radius: 6px;
            font-family: monospace;
        }

        /* Quick Links */
        .quick-links-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.2rem;
            margin-bottom: 2.5rem;
        }

        .quick-link-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 1.2rem;
            text-decoration: none;
            color: #333;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
            border: 1px solid var(--card-border);
            display: flex;
            align-items: center;
            gap: 14px;
            transition: all 0.25s ease;
            cursor: pointer;
        }

        .quick-link-card:hover {
            border-color: var(--primary-blue);
            box-shadow: 0 6px 18px rgba(0,0,0,0.08);
            transform: translateY(-3px);
        }

        .quick-link-icon {
            font-size: 1.8rem;
            background: #e2e8f0;
            padding: 0.6rem;
            border-radius: 10px;
        }

        .quick-link-text h4 {
            margin: 0;
            color: var(--primary-blue);
            font-size: 0.95rem;
        }

        .quick-link-text p {
            margin: 2px 0 0 0;
            font-size: 0.78rem;
            color: #64748b;
        }

        /* Profile & Vision Mission Section */
        .profile-section {
            background: #ffffff;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.04);
            margin-bottom: 2.5rem;
            border: 1px solid var(--card-border);
        }

        .profile-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 1rem;
        }

        .profile-box {
            background: #f8fafc;
            border-radius: 8px;
            padding: 1.4rem;
            border-top: 4px solid var(--primary-blue);
        }

        .profile-box.kkp-box {
            border-top-color: var(--accent-gold);
        }

        .profile-box h3 {
            margin-top: 0;
            color: var(--primary-blue);
            font-size: 1.15rem;
        }

        .profile-box p {
            font-size: 0.88rem;
            color: #475569;
            line-height: 1.6;
            margin-bottom: 0.8rem;
        }

        /* Department Scope Cards */
        .dept-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .dept-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 1.8rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.04);
            border-top: 5px solid var(--primary-blue);
            transition: transform 0.2s;
        }

        .dept-card:hover {
            transform: translateY(-4px);
        }

        .dept-card.kkp {
            border-top-color: var(--accent-gold);
        }

        .dept-card h2 {
            margin-top: 0;
            color: var(--primary-blue);
            font-size: 1.3rem;
        }

        .dept-card ul {
            padding-left: 1.2rem;
            color: #475569;
            line-height: 1.6;
            font-size: 0.92rem;
        }

        /* Announcements */
        .announcement-box {
            background: #ffffff;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.04);
            margin-bottom: 2.5rem;
            border: 1px solid var(--card-border);
            transition: background-color 0.4s ease;
        }

        .announcement-item {
            padding: 1rem 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .announcement-item:last-child {
            border-bottom: none;
        }

        .announcement-badge {
            display: inline-block;
            padding: 0.2rem 0.6rem;
            font-size: 0.75rem;
            border-radius: 4px;
            font-weight: bold;
            color: #fff;
            margin-bottom: 0.4rem;
        }

        .badge-btmk { background-color: var(--primary-blue); }
        .badge-kkp { background-color: #d97706; }

        /* Hotlines Section */
        .hotlines-section {
            background: #ffffff;
            border-radius: 12px;
            padding: 1.8rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.04);
            margin-bottom: 2.5rem;
            border: 1px solid var(--card-border);
            border-top: 5px solid var(--sabah-red);
        }

        .hotlines-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.2rem;
            margin-top: 1rem;
        }

        .hotline-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1.2rem;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .hotline-icon {
            font-size: 2rem;
            background: #fee2e2;
            color: var(--sabah-red);
            padding: 0.6rem;
            border-radius: 50%;
        }

        .hotline-info h4 {
            margin: 0;
            color: var(--primary-blue);
            font-size: 1rem;
        }

        .hotline-info p {
            margin: 3px 0 0 0;
            font-size: 0.88rem;
            color: #475569;
            font-weight: 600;
        }

        /* Side-by-Side Wrapper for Guide Center & Map */
        .guide-map-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2.5rem;
            align-items: stretch;
        }

        /* Guide Center */
        .guide-section {
            background: #ffffff;
            border-radius: 12px;
            padding: 1.8rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.04);
            border: 1px solid var(--card-border);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .keywords-container {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin: 0.8rem 0 1.2rem;
        }

        .keyword-tag {
            background-color: #f1f5f9;
            color: var(--primary-blue);
            padding: 0.35rem 0.7rem;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid #cbd5e1;
            transition: all 0.2s;
        }

        .keyword-tag:hover {
            background-color: var(--primary-blue);
            color: #ffffff;
            border-color: var(--primary-blue);
        }

        .guide-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0.8rem;
            margin-bottom: 1.2rem;
        }

        .guide-item {
            background: #f8fafc;
            padding: 0.8rem 1rem;
            border-radius: 8px;
            border-left: 4px solid var(--primary-blue);
        }

        .guide-item h4 {
            margin: 0 0 0.2rem 0;
            color: var(--primary-blue);
            font-size: 0.95rem;
        }

        .guide-item p {
            margin: 0;
            font-size: 0.82rem;
            color: #64748b;
        }

        .search-bottom-box {
            margin-top: auto;
            padding-top: 1rem;
            border-top: 1px dashed #cbd5e1;
            display: flex;
            gap: 10px;
        }

        .search-bottom-box input {
            flex: 1;
            padding: 0.7rem 1rem;
            border: 1px solid #cbd5e1;
            border-radius: 30px;
            font-size: 0.85rem;
            outline: none;
        }

        .search-bottom-box input:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(0, 43, 73, 0.1);
        }

        /* Map Section */
        .map-section {
            background: #ffffff;
            border-radius: 12px;
            padding: 1.8rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.04);
            border: 1px solid var(--card-border);
            display: flex;
            flex-direction: column;
        }

        .map-container {
            width: 100%;
            flex: 1;
            min-height: 380px;
            border-radius: 8px;
            overflow: hidden;
            margin-top: 1rem;
        }

        /* Pulse Highlight effect for targeted section */
        .highlight-target {
            animation: highlightPulse 1.5s ease-in-out;
        }

        @keyframes highlightPulse {
            0% { box-shadow: 0 0 0 0px rgba(212, 175, 55, 0.7); }
            50% { box-shadow: 0 0 0 12px rgba(212, 175, 55, 0); }
            100% { box-shadow: 0 4px 12px rgba(0,0,0,0.04); }
        }

        /* Responsive layout */
        @media (max-width: 992px) {
            .guide-map-wrapper {
                grid-template-columns: 1fr;
            }
            .map-container {
                min-height: 300px;
            }
            .hero-content h1 {
                font-size: 1.7rem;
            }
        }

        /* Footer */
        .footer {
            background-color: var(--primary-blue);
            color: #ffffff;
            text-align: center;
            padding: 2rem 1.5rem;
            font-size: 0.85rem;
        }
    </style>

    <!-- Library SweetAlert2 untuk paparan pop-up yang cantik -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Papar Pop-up Berjaya
    <?php if ($flash_success): ?>
        Swal.fire({
            title: 'Berjaya!',
            text: <?php echo json_encode($flash_success); ?>,
            icon: 'success',
            confirmButtonColor: '#2563eb',
            confirmButtonText: 'OK'
        });
    <?php endif; ?>

    // Papar Pop-up Ralat (jika ada)
    <?php if ($flash_error): ?>
        Swal.fire({
            title: 'Ralat!',
            text: <?php echo json_encode($flash_error); ?>,
            icon: 'error',
            confirmButtonColor: '#dc2626',
            confirmButtonText: 'Tutup'
        });
    <?php endif; ?>
});
</script>

</body>
</html>
</head>
<body>

    <!-- Shared Top Navbar & Language Switcher -->
    <?php include 'includes/navbar.php'; ?>

    <!-- Traditional Sabah Pattern Top Motif -->
    <div class="sabah-pattern-border"></div>

    <!-- Hero Banner with Photo Slideshow -->
    <div class="hero-slideshow-container">
        <!-- Slide 1: Menara Tun Mustapha -->
        <div class="slide-item active" style="background-image: url('assets/images/menara_tun_mustapha.jpg');">
            <div class="slide-overlay"></div>
        </div>
        <!-- Slide 2: ICT Infrastructure & Data Center -->
        <div class="slide-item" style="background-image: url('https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&w=1600&q=80');">
            <div class="slide-overlay"></div>
        </div>
        <!-- Slide 3: Occupational Safety & Health (OSH/KKP) -->
        <div class="slide-item" style="background-image: url('https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&w=1600&q=80');">
            <div class="slide-overlay"></div>
        </div>

      <div class="hero-content">
    <div class="hero-crests">
        <img src="https://upload.wikimedia.org/wikipedia/commons/2/26/Coat_of_arms_of_Malaysia.svg" alt="Jata Malaysia">
        <img src="assets/images/yayasan_sabah.png" alt="Logo Kumpulan Yayasan Sabah" class="logo-ys-center">
        <img src="https://upload.wikimedia.org/wikipedia/commons/6/68/Coat_of_arms_of_Sabah.svg" alt="Jata Sabah">
    </div>
    <h1>
        <?php 
            if (isset($lang) && $lang === 'en') {
                echo 'INFORMATION TECHNOLOGY AND COMMUNICATION DIVISION &amp; OCCUPATIONAL SAFETY HEALTH PORTAL';
            } else {
                echo 'PORTAL BAHAGIAN TEKNOLOGI MAKLUMAT DAN KOMUNIKASI &amp; KESELAMATAN KESIHATAN PEKERJAAN';
            }
        ?>
    </h1>
    <p>
        <?php 
            if (isset($lang) && $lang === 'en') {
                echo 'YAYASAN SABAH GROUP';
            } else {
                echo 'KUMPULAN YAYASAN SABAH';
            }
        ?>
    </p>

    <?php if ($is_logged_in): ?>
        <a href="<?php echo $dashboard_url; ?>" class="btn-login-hero">
            &rarr; <?php echo ($lang === 'en') ? 'Go to My Dashboard' : 'Ke Dashboard Saya'; ?>
        </a>
    <?php else: ?>
        <a href="login.php" class="btn-login-hero">
            <?php echo ($lang === 'en') ? 'Login Staff' : 'Log Masuk Staf'; ?>
        </a>
    <?php endif; ?>
</div>

<style>
/* 1. Force the parent container to reset padding and strictly center */
.hero-content {
    width: 100% !important;
    max-width: 900px !important;
    margin-left: auto !important;
    margin-right: auto !important;
    padding-left: 0 !important;
    padding-right: 0 !important;
    text-align: center !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: center !important;
}

/* Anjakkan blok logo sikit ke kiri */
.hero-crests {
    display: flex !important;
    flex-direction: row !important;
    justify-content: center !important;
    align-items: center !important;
    width: 100% !important;
    margin: 0 auto 1.5rem auto !important;
    padding: 0 !important;
    gap: 20px !important;
    transform: translateX(-20px) !important; 
}

/* Pastikan tiada margin pelik pada gambar */
.hero-crests img {
    margin: 0 !important;
    padding: 0 !important;
    height: 58px;
    width: auto;
    object-fit: contain;
    filter: drop-shadow(0px 3px 6px rgba(0,0,0,0.5));
}

/* Bulatan Putih pada Logo Yayasan Sabah */
.hero-crests img.logo-ys-center {
    height: 68px;
    width: 68px; /* Pastikan nisbah 1:1 untuk bulatan sempurna */
    background-color: #ffffff !important;
    border-radius: 50% !important;
    padding: 6px !important; /* Ruang bernafas dalam bulatan */
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3); /* Bayang halus supaya logo kelihatan timbul */
}
</style>
        <!-- Slide Dots -->
        <div class="slide-dots">
            <span class="dot active" onclick="currentSlide(0)"></span>
            <span class="dot" onclick="currentSlide(1)"></span>
            <span class="dot" onclick="currentSlide(2)"></span>
        </div>
    </div>

    <!-- Sabah Pattern Divider -->
    <div class="sabah-pattern-border"></div>

    <div class="container">

      <!-- Live Visitor Statistics Counter -->
<div class="visitor-counter-bar">
    <div class="visitor-item">
        <!-- Person / User Icon -->
        <svg class="visitor-icon" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
        </svg>
        <span><?php echo (isset($lang) && $lang === 'en') ? 'Total Portal Visits:' : 'Jumlah Pelawat Portal:'; ?></span>
        <span class="visitor-number" id="totalVisits"><?php echo number_format($totalVisits); ?></span>
    </div>
    
    <div class="visitor-item">
        <!-- Eye / Online Icon -->
        <svg class="visitor-icon" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5-5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
        </svg>
        <span><?php echo (isset($lang) && $lang === 'en') ? 'Online Today:' : 'Pelawat Hari Ini:'; ?></span>
        <span class="visitor-number" id="todayVisits"><?php echo number_format($todayVisits); ?></span>
    </div>
</div>

<style>
/* Visitor Counter Styling */
.visitor-counter-bar {
    display: flex;
    align-items: center;
    gap: 20px;
    font-size: 0.85rem;
    color: #475569;
}

.visitor-item {
    display: flex;
    align-items: center;
    gap: 6px;
}

.visitor-icon {
    color: #d97706; /* Matching theme orange/amber color */
    flex-shrink: 0;
}

.visitor-number {
    font-weight: 700;
    color: #0f172a;
}
</style>

<!-- Quick Links / Akses Pantas -->
<h2 class="section-title"><?php echo (isset($lang) && $lang === 'en') ? 'Public Services & Resources' : 'Akses Pantas Awam & Sumber'; ?></h2>

<div class="quick-links-grid">
    <!-- 1. Pengumuman & Notis -->
    <a onclick="switchPortalPanel('announcements')" class="quick-link-card">
        <div class="quick-link-text">
            <h4><?php echo (isset($lang) && $lang === 'en') ? 'Notices & Advisories' : 'Pengumuman & Notis'; ?></h4>
            <p><?php echo (isset($lang) && $lang === 'en') ? 'System maintenance & safety alerts' : 'Notis penyelenggaraan & keselamatan'; ?></p>
        </div>
    </a>

    <!-- 2. Pusat Panduan -->
    <a onclick="switchPortalPanel('guide-center')" class="quick-link-card">
        <div class="quick-link-text">
            <h4><?php echo (isset($lang) && $lang === 'en') ? 'Guide Center' : 'Pusat Panduan'; ?></h4>
            <p><?php echo (isset($lang) && $lang === 'en') ? 'SOPs, FAQs & Guidelines' : 'Panduan pengguna & SOP rasmi'; ?></p>
        </div>
    </a>

    <!-- 3. Tiket Bantuan ICT (Diperkemas dengan Gaya Biru) -->
    <a href="login.php" class="quick-link-card highlight-ict">
        <div class="quick-link-text">
            <h4><?php echo (isset($lang) && $lang === 'en') ? 'ICT Support' : 'Bantuan ICT'; ?></h4>
            <p><?php echo (isset($lang) && $lang === 'en') ? 'Login required for staff requests' : 'Log masuk untuk borang bantuan'; ?></p>
        </div>
    </a>

    <!-- 4. Lapor Insiden / Hazard KKP (Gaya Jingga/Amber) -->
    <a onclick="openHazardModal()" class="quick-link-card highlight-hazard">
        <div class="quick-link-text">
            <h4><?php echo (isset($lang) && $lang === 'en') ? 'Report Safety Hazard' : 'Laporkan Insiden KKP'; ?></h4>
            <p><?php echo (isset($lang) && $lang === 'en') ? 'Direct OSH incident & risk reporting' : 'Buka borang laporan keselamatan KKP'; ?></p>
        </div>
    </a>

    <!-- 5. Lokasi & Peta Premis -->
    <a onclick="switchPortalPanel('location-map')" class="quick-link-card">
        <div class="quick-link-text">
            <h4><?php echo (isset($lang) && $lang === 'en') ? 'Location & Map' : 'Lokasi & Peta Premis'; ?></h4>
            <p><?php echo (isset($lang) && $lang === 'en') ? 'Menara Tun Mustapha, Likas' : 'Menara Tun Mustapha, Likas'; ?></p>
        </div>
    </a>
</div>

<style>
/* Styling Khas Kad Tiket Bantuan ICT (Biru) */
.quick-link-card.highlight-ict {
    border-left: 4px solid #0284c7;
}

.quick-link-card.highlight-ict:hover {
    background-color: #f0f9ff;
    border-color: #0369a1;
}

/* Styling Khas Kad KKP (Jingga) */
.quick-link-card.highlight-hazard {
    border-left: 4px solid #d97706;
}

.quick-link-card.highlight-hazard:hover {
    background-color: #fffbeb;
    border-color: #b45309;
}
</style>

    <!-- Combined Organization Profile Section (BTMK & KKP) -->
<div class="profile-section" id="about-us">
    <h2 class="section-title" style="border:none; padding:0; margin-bottom:0.5rem;">
        <?php echo (isset($lang) && $lang === 'en') ? 'Organizational Profile' : 'Profil Organisasi'; ?>
    </h2>
    <p style="color: #64748b; font-size: 0.9rem; margin-top:0;">
        <?php echo (isset($lang) && $lang === 'en') ? 'Learn about our core mission in driving digital innovation and safeguarding workplace safety for Yayasan Sabah.' : 'Mengenali visi, misi dan komitmen kami dalam memacu digitalisasi serta menjamin keselamatan tempat kerja Kumpulan Yayasan Sabah.'; ?>
    </p>

    <!-- Combined Container -->
    <div class="combined-container">
        <div class="slide-content">
            
            <!-- Left Column: Info Boxes -->
            <div class="info-panel">
                <!-- Info Box BTMK -->
                <div class="info-box btmk-theme">
                    <h5 class="info-box-title">
                        <?php echo (isset($lang) && $lang === 'en') ? 'IT &amp; Communication Division (BTMK)' : 'Bahagian Teknologi Maklumat dan Komunikasi (BTMK)'; ?>
                    </h5>
                    <p>
                        <strong><?php echo (isset($lang) && $lang === 'en') ? 'Vision:' : 'Visi:'; ?></strong> 
                        <?php echo (isset($lang) && $lang === 'en') ? 'To be the catalyst for an excellent, secure, and competitive digital transformation for Yayasan Sabah Group.' : 'Menjadi pemangkin transformasi digital yang cemerlang, selamat, dan berdaya saing bagi Kumpulan Yayasan Sabah.'; ?>
                    </p>
                    <p>
                        <strong><?php echo (isset($lang) && $lang === 'en') ? 'Mission:' : 'Misi:'; ?></strong> 
                        <?php echo (isset($lang) && $lang === 'en') ? 'To provide efficient ICT services, modern network infrastructure, and cybersecurity protection.' : 'Menyediakan perkhidmatan ICT yang cekap, infrastruktur rangkaian moden, serta perlindungan keselamatan siber.'; ?>
                    </p>
                </div>

                <!-- Info Box KKP -->
                <div class="info-box kkp-theme">
                    <h5 class="info-box-title">
                        <?php echo (isset($lang) && $lang === 'en') ? 'Occupational Safety Health (KKP)' : 'Keselamatan Kesihatan Pekerjaan (KKP)'; ?>
                    </h5>
                    <p>
                        <strong><?php echo (isset($lang) && $lang === 'en') ? 'Vision:' : 'Visi:'; ?></strong> 
                        <?php echo (isset($lang) && $lang === 'en') ? 'To create a safe, healthy, and hazard-free work environment.' : 'Mewujudkan persekitaran kerja yang selamat, sihat, dan bebas daripada kemalangan.'; ?>
                    </p>
                    <p>
                        <strong><?php echo (isset($lang) && $lang === 'en') ? 'Mission:' : 'Misi:'; ?></strong> 
                        <?php echo (isset($lang) && $lang === 'en') ? 'To ensure OSH compliance, conduct HIRARC assessments, and foster a safety culture.' : 'Memastikan pematuhan KKP, melaksanakan penilaian HIRARC, serta membudayakan amalan keselamatan.'; ?>
                    </p>
                </div>
            </div>

            <!-- Right Column: Combined Org Chart -->
            <div class="chart-panel">
                <h4 class="chart-title">
                    <?php echo (isset($lang) && $lang === 'en') ? 'Information Technology and Communication Division & Occupational Safety Health (KKP)' : 'Carta Organisasi Bahagian Teknologi Maklumat dan Komunikasi & Keselamatan Kesihatan Pekerjaan'; ?>
                </h4>
                
                <div class="org-chart">
                    <!-- Top Level: Division Head -->
                    <div class="node root">
                        <div class="person-icon-wrapper light">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="person-icon"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                        </div>
                        <span><?php echo (isset($lang) && $lang === 'en') ? 'Head of ICT Division' : 'Ketua Bahagian Teknologi Maklumat dan Komunikasi'; ?></span>
                    </div>
                    
                    <div class="chart-line-v"></div>

                    <!-- Level 2 Connector (Main Branching) -->
                    <div class="branch-connector-main"></div>
                    
                    <!-- Level 2: Main Branches (BTMK Sub-Units & KKP) -->
                    <div class="nodes-row">
                        
                        <!-- BTMK Branch -->
                        <div class="branch-group btmk-branch">
                            <div class="branch-header btmk-header">
                                <?php echo (isset($lang) && $lang === 'en') ? 'ICT Operations &amp; Systems' : 'Operasi &amp; Sistem ICT (BTMK)'; ?>
                            </div>
                            
                            <div class="chart-line-v"></div>
                            
                            <!-- Sub Connector BTMK (3 Columns Perfect Bridge) -->
                            <div class="branch-connector-sub btmk-sub-bridge"></div>
                            
                            <!-- BTMK Sub-Units -->
                            <div class="sub-nodes-row">
                                <div class="node-col">
                                    <div class="node child">
                                        <div class="person-icon-wrapper dark">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="person-icon"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                        </div>
                                        <span><?php echo (isset($lang) && $lang === 'en') ? 'Infrastruktur &amp; Rangkaian' : 'Infrastruktur &amp; Rangkaian'; ?></span>
                                    </div>
                                </div>
                                <div class="node-col">
                                    <div class="node child">
                                        <div class="person-icon-wrapper dark">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="person-icon"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                        </div>
                                        <span><?php echo (isset($lang) && $lang === 'en') ? 'Pembangunan Sistem' : 'Pembangunan Sistem'; ?></span>
                                    </div>
                                </div>
                                <div class="node-col">
                                    <div class="node child">
                                        <div class="person-icon-wrapper dark">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="person-icon"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                        </div>
                                        <span><?php echo (isset($lang) && $lang === 'en') ? 'Keselamatan Siber &amp; Sokongan' : 'Keselamatan Siber &amp; Sokongan'; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- KKP Branch -->
                        <div class="branch-group kkp-branch">
                            <div class="branch-header kkp-header">
                                <?php echo (isset($lang) && $lang === 'en') ? 'Safety Health Section (KKP)' : 'Seksyen Keselamatan Kesihatan Pekerjaan (KKP)'; ?>
                            </div>
                            
                            <div class="chart-line-v"></div>

                            <!-- Sub Connector KKP (2 Columns Perfect Bridge) -->
                            <div class="branch-connector-sub kkp-sub-bridge"></div>
                            
                            <!-- KKP Sub-Units -->
                            <div class="sub-nodes-row">
                                <div class="node-col">
                                    <div class="node child kkp-child">
                                        <div class="person-icon-wrapper dark-kkp">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="person-icon"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                        </div>
                                        <span><?php echo (isset($lang) && $lang === 'en') ? 'Jawatankuasa KKP' : 'Jawatankuasa KKP'; ?></span>
                                    </div>
                                </div>
                                <div class="node-col">
                                    <div class="node child kkp-child">
                                        <div class="person-icon-wrapper dark-kkp">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="person-icon"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                        </div>
                                        <span><?php echo (isset($lang) && $lang === 'en') ? 'Pasukan HIRARC &amp; Kecemasan' : 'Pasukan HIRARC &amp; Kecemasan'; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
.profile-section {
    font-family: Arial, sans-serif;
    margin-top: 1rem;
}

.combined-container {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

/* Content Layout */
.slide-content {
    display: flex;
    gap: 1.5rem;
    align-items: stretch;
}
.info-panel {
    flex: 0.8;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}
.chart-panel {
    flex: 1.4;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 1rem;
    display: flex;
    flex-direction: column;
    align-items: center;
}
.chart-title {
    margin: 0 0 1rem 0;
    font-size: 1rem;
    color: #334155;
    text-align: center;
}

/* Info Boxes Styling */
.info-box {
    padding: 0.85rem 1rem;
    border-radius: 6px;
    font-size: 0.85rem;
}
.info-box p {
    margin: 0.3rem 0;
}
.info-box-title {
    margin: 0 0 0.4rem 0;
    font-size: 0.95rem;
    font-weight: bold;
}
.info-box.btmk-theme {
    background: #eff6ff;
    border-left: 4px solid #2563eb;
    color: #1e3a8a;
}
.info-box.kkp-theme {
    background: #fefce8;
    border-left: 4px solid #eab308;
    color: #713f12;
}

/* CSS Org Chart Layout */
.org-chart {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;
}
.node {
    padding: 0.5rem 0.6rem;
    border-radius: 6px;
    font-size: 0.8rem;
    text-align: center;
    font-weight: 600;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;
}
.node.root {
    background: #1e40af;
    color: #ffffff;
    width: 50%;
}
.node.child {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #334155;
    height: 100%;
    width: 100%;
    box-sizing: border-box;
}
.node.child.kkp-child {
    border-color: #fde047;
    background: #fffdf0;
}

/* Branch Groups */
.nodes-row {
    display: flex;
    justify-content: space-between;
    width: 100%;
    gap: 1.5rem;
}
.branch-group {
    display: flex;
    flex-direction: column;
    align-items: center;
}
.btmk-branch {
    flex: 1.4;
}
.kkp-branch {
    flex: 1;
}

/* Branch Headers */
.branch-header {
    padding: 0.4rem 0.6rem;
    border-radius: 5px;
    font-size: 0.8rem;
    font-weight: bold;
    text-align: center;
    width: 100%;
    box-sizing: border-box;
}
.btmk-header {
    background: #dbeafe;
    color: #1e40af;
    border: 1px solid #bfdbfe;
}
.kkp-header {
    background: #fef08a;
    color: #854d0e;
    border: 1px solid #fef08a;
}

.sub-nodes-row {
    display: flex;
    justify-content: space-between;
    width: 100%;
    gap: 0.5rem;
}

/* Person Icon Styling */
.person-icon-wrapper {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.person-icon-wrapper.light {
    background: rgba(255, 255, 255, 0.25);
    color: #ffffff;
}
.person-icon-wrapper.dark {
    background: #e2e8f0;
    color: #64748b;
}
.person-icon-wrapper.dark-kkp {
    background: #fef08a;
    color: #a16207;
}
.person-icon {
    width: 15px;
    height: 15px;
}

/* Base Line Styling */
.chart-line-v {
    width: 2px;
    height: 14px;
    background-color: #94a3b8;
}

/* 1. Root to Main Branches Connector */
.branch-connector-main {
    position: relative;
    width: 58%;
    height: 14px;
    border-top: 2px solid #94a3b8;
}
.branch-connector-main::before,
.branch-connector-main::after {
    content: '';
    position: absolute;
    top: 0;
    width: 2px;
    height: 100%;
    background-color: #94a3b8;
}
.branch-connector-main::before { left: 0; }
.branch-connector-main::after { right: 0; }

/* 2. Sub-Units Connectors (Guaranteed Continuous Bridge) */
.branch-connector-sub {
    position: relative;
    width: 100%;
    height: 14px;
}

/* BTMK Sub Connector (3 Columns) */
.btmk-sub-bridge {
    border-top: 2px solid #94a3b8;
    width: 66.66%; /* Buka dari pusat lajur 1 hingga pusat lajur 3 */
}
.btmk-sub-bridge::before,
.btmk-sub-bridge::after {
    content: '';
    position: absolute;
    top: 0;
    width: 2px;
    height: 100%;
    background-color: #94a3b8;
}
.btmk-sub-bridge::before { left: 0; }
.btmk-sub-bridge::after { right: 0; }

/* Garisan tengah untuk lajur ke-2 BTMK */
.btmk-sub-bridge::shadow, 
.btmk-branch .sub-nodes-row {
    position: relative;
}
.btmk-sub-bridge {
    background: linear-gradient(to bottom, transparent 0%, transparent 100%);
}
.btmk-sub-bridge-center {
    position: absolute;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 2px;
    height: 100%;
    background-color: #94a3b8;
}

/* KKP Sub Connector (2 Columns) */
.kkp-sub-bridge {
    border-top: 2px solid #94a3b8;
    width: 50%; /* Buka dari pusat lajur 1 hingga pusat lajur 2 */
}
.kkp-sub-bridge::before,
.kkp-sub-bridge::after {
    content: '';
    position: absolute;
    top: 0;
    width: 2px;
    height: 100%;
    background-color: #94a3b8;
}
.kkp-sub-bridge::before { left: 0; }
.kkp-sub-bridge::after { right: 0; }

.node-col {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* Responsive View */
@media (max-width: 992px) {
    .slide-content {
        flex-direction: column;
    }
    .nodes-row {
        flex-direction: column;
        gap: 1rem;
    }
    .branch-connector-main,
    .branch-connector-sub {
        display: none;
    }
    .node.root {
        width: 100%;
    }
}
</style>

      <!-- Dept Scope -->
<h2 class="section-title" id="dept-services">
    <?php echo ($lang === 'en') ? 'Department Services & Scope' : 'Perkhidmatan & Peranan Bahagian'; ?>
</h2>

<div class="dept-grid">
    <!-- 1st Card: BTMK (Clickable to login.php) -->
    <div class="dept-card" onclick="window.location.href='login.php';" style="cursor: pointer;">
        <h2>
            <span style="font-size: 1.2rem; margin-right: 6px;"></span>Bahagian Teknologi Maklumat dan Komunikasi (BTMK)
        </h2>
        <small><strong><?php echo ($lang === 'en') ? 'Information Technology and Communication Division' : 'Bahagian Teknologi Maklumat dan Komunikasi'; ?></strong></small>
        <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 0.8rem 0;">
        <ul>
            <li>
                <strong><?php echo ($lang === 'en') ? 'ICT Infrastructure Management' : 'Pengurusan Infrastruktur ICT'; ?>:</strong> 
                <?php echo ($lang === 'en') ? 'Main network architecture, server maintenance & data center operations.' : 'Rangkaian Rangka Utama, penyelenggaraan pelayan & pusat data.'; ?>
            </li>
            <li>
                <strong><?php echo ($lang === 'en') ? 'Cybersecurity & Governance' : 'Keselamatan Siber & Tatakelola'; ?>:</strong> 
                <?php echo ($lang === 'en') ? 'Protection of official data, firewall management & security audits.' : 'Perlindungan data rasmi, pengurusan firewall & audit keselamatan.'; ?>
            </li>
            <li>
                <strong><?php echo ($lang === 'en') ? 'Technical Helpdesk' : 'Bantuan Helpdesk Teknikal'; ?>:</strong> 
                <?php echo ($lang === 'en') ? 'Internal application support, hardware troubleshooting & email systems.' : 'Sokongan aplikasi dalaman, pembaikan perkakasan & e-mel.'; ?>
            </li>
            <li>
                <strong><?php echo ($lang === 'en') ? 'Wi-Fi & Network Access' : 'Capaian Rangkaian & Wi-Fi'; ?>:</strong> 
                <?php echo ($lang === 'en') ? 'Guest portal access, staff VPN & bandwidth management.' : 'Pengurusan akses Wi-Fi tetamu, VPN staf & jalur lebar.'; ?>
            </li>
        </ul>
        <div style="margin-top: 1rem; padding-top: 0.8rem; border-top: 1px dashed #e2e8f0;">
            <a href="login.php" onclick="event.stopPropagation();" style="color: var(--primary-blue); font-weight: bold; font-size: 0.85rem; text-decoration: none;">
               <?php echo ($lang === 'en') ? 'Submit Helpdesk Ticket &rarr;' : 'Hantar Aduan BTMK &rarr;'; ?>
            </a>
        </div>
    </div>

    <!-- 2nd Card: KKP (Clickable to open Hazard Modal) -->
    <div class="dept-card kkp" onclick="openHazardModal()" style="cursor: pointer;">
        <h2>
            <span style="font-size: 1.2rem; margin-right: 6px;"></span>
            <?php echo (isset($lang) && $lang === 'en') ? 'Occupational Safety Health (OSH)' : 'Keselamatan Kesihatan Pekerjaan (KKP)'; ?>
        </h2>
        <small><strong><?php echo (isset($lang) && $lang === 'en') ? 'Occupational Safety Health Unit' : 'Unit Keselamatan Kesihatan Pekerjaan'; ?></strong></small>
        <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 0.8rem 0;">
        <ul>
            <li>
                <strong><?php echo (isset($lang) && $lang === 'en') ? 'Hazard Identification (HIRARC)' : 'Pemeriksaan Insiden &amp; HIRARC'; ?>:</strong> 
                <?php echo (isset($lang) && $lang === 'en') ? 'Workplace risk assessments, workplace inspections &amp; risk mitigation.' : 'Penilaian risiko tempat kerja, pemeriksaan premis &amp; mitigasi bahaya.'; ?>
            </li>
            <li>
                <strong><?php echo (isset($lang) && $lang === 'en') ? 'Safety Policies &amp; Protocol' : 'Polisi &amp; Protokol Kecemasan'; ?>:</strong> 
                <?php echo (isset($lang) && $lang === 'en') ? 'Emergency response plans, fire drill coordination &amp; OSH compliance.' : 'Pelan tindakan kecemasan, latihan kebakaran &amp; pematuhan akta.'; ?>
            </li>
            <li>
                <strong><?php echo (isset($lang) && $lang === 'en') ? 'Incident Reporting' : 'Laporan Insiden &amp; Audit Premis'; ?>:</strong> 
                <?php echo (isset($lang) && $lang === 'en') ? 'Investigation of workplace accidents, hazard reporting &amp; safety audits.' : 'Siasatan kemalangan, borang aduan insiden &amp; audit keselamatan.'; ?>
            </li>
            <li>
                <strong><?php echo (isset($lang) && $lang === 'en') ? 'Safety Induction' : 'Induksi &amp; Panduan Keselamatan'; ?>:</strong> 
                <?php echo (isset($lang) && $lang === 'en') ? 'Safety briefings for contractors, visitors &amp; PPE requirements.' : 'Taklimat keselamatan kontraktor, pelawat &amp; syarat PPE.'; ?>
            </li>
        </ul>
        
        <!-- Trigger Button for Modal -->
        <div style="margin-top: 1rem; padding-top: 0.8rem; border-top: 1px dashed #e2e8f0;">
            <a onclick="event.stopPropagation(); openHazardModal();" style="color: #d97706; font-weight: bold; font-size: 0.85rem; text-decoration: none; cursor: pointer;">
                <?php echo (isset($lang) && $lang === 'en') ? 'Report Safety Hazard &rarr;' : 'Laporkan Insiden KKP &rarr;'; ?>
            </a>
        </div>
    </div>
</div>

<!-- OSH Hazard Reporting Modal Window -->
<div id="hazardModal" class="hazard-modal-overlay" style="display: none;">
    <div class="hazard-modal-box" onclick="event.stopPropagation();">
        <div class="hazard-modal-header">
            <h3>Laporan Insiden / Hazard KKP</h3>
            <span class="hazard-close-btn" onclick="closeHazardModal()">&times;</span>
        </div>
        <form action="submit_hazard.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Jenis Insiden</label>
                <select name="incident_type" required class="form-control">
                    <option value="" disabled selected>-- Pilih Jenis Insiden --</option>
                    <option value="Hampir Kemalangan">Hampir Kemalangan</option>
                    <option value="Keadaan Tidak Selamat">Keadaan Tidak Selamat</option>
                    <option value="Tingkah Laku Tidak Selamat">Tingkah Laku Tidak Selamat</option>
                    <option value="Kemalangan Tempat Kerja">Kemalangan Tempat Kerja</option>
                </select>
            </div>

            <div class="form-group">
                <label><?php echo (isset($lang) && $lang === 'en') ? 'Incident Location (Menara Tun Mustapha)' : 'Lokasi Kejadian (Menara Tun Mustapha)'; ?></label>
                <select name="location" required class="form-control" style="height: auto; max-height: 200px; overflow-y: auto;">
                    <option value="" disabled selected><?php echo (isset($lang) && $lang === 'en') ? '-- Select Location --' : '-- Sila Pilih Lokasi --'; ?></option>

                    <optgroup label="<?php echo (isset($lang) && $lang === 'en') ? 'Basement' : 'Aras Bawah (Basement)'; ?>">
                        <option value="Basement - HR & Admin (Vehicle Pool)">
                            <?php echo (isset($lang) && $lang === 'en') ? 'Basement - HR & Admin Division (Vehicle Pool)' : 'Basement - Bahagian Sumber Manusia & Pentadbiran (Sektor Kolam Kenderaan)'; ?>
                        </option>
                        <option value="Basement - Energy & Facilities Division">
                            <?php echo (isset($lang) && $lang === 'en') ? 'Basement - Energy & Facilities Division' : 'Basement - Bahagian Tenaga & Fasiliti'; ?>
                        </option>
                    </optgroup>

                    <!-- Podium -->
                    <optgroup label="Podium">
                        <option value="Podium - Library"><?php echo (isset($lang) && $lang === 'en') ? 'Podium - Tun Fuad Stephens Research Library' : 'Podium - Perpustakaan Penyelidikan Tun Fuad Stephens'; ?></option>
                        <option value="Podium - Tun Mustapha Gallery"><?php echo (isset($lang) && $lang === 'en') ? 'Podium - Tun Mustapha Gallery' : 'Podium - Galeri Tun Mustapha'; ?></option>
                        <option value="Podium - Galleria Artisan"><?php echo (isset($lang) && $lang === 'en') ? 'Podium - Galleria Artisan' : 'Podium - Galleria Artisan'; ?></option>
                        <option value="Podium - Galleria Artisan II"><?php echo (isset($lang) && $lang === 'en') ? 'Podium - Galleria Artisan II (Menara Kinabalu)' : 'Podium - Galleria Artisan II (Menara Kinabalu)'; ?></option>
                        <option value="Podium - Tun Ahmad Raffae Auditorium"><?php echo (isset($lang) && $lang === 'en') ? 'Podium - Tun Ahmad Raffae Auditorium' : 'Podium - Auditorium Tun Ahmad Raffae'; ?></option>
                        <option value="Podium - Tun Hamdan Theatre"><?php echo (isset($lang) && $lang === 'en') ? 'Podium - Tun Hamdan Theatre' : 'Podium - Teater Tun Hamdan'; ?></option>
                        <option value="Podium - Multivision Room"><?php echo (isset($lang) && $lang === 'en') ? 'Podium - Multivision Room' : 'Podium - Bilik Multivisi'; ?></option>
                        <option value="Podium - Auditorium Meeting Room"><?php echo (isset($lang) && $lang === 'en') ? 'Podium - Auditorium Meeting Room' : 'Podium - Bilik Mesyuarat Auditorium'; ?></option>
                        <option value="Podium - Security Counter"><?php echo (isset($lang) && $lang === 'en') ? 'Podium - Security Counter' : 'Podium - Kaunter Keselamatan'; ?></option>
                    </optgroup>

                    <!-- Aras / Level 4 -->
                    <optgroup label="<?php echo (isset($lang) && $lang === 'en') ? 'Level 4' : 'Tingkat 4'; ?>">
                        <option value="Level 4 - Education Development (Higher Education Loan)">
                            <?php echo (isset($lang) && $lang === 'en') ? 'Level 4 - Education Development (Higher Education Loan)' : 'Tingkat 4 - Bahagian Pembangunan Pendidikan (Pinjaman Pengajian Tinggi)'; ?>
                        </option>
                        <option value="Level 4 - Education Development (Legal Matters)">
                            <?php echo (isset($lang) && $lang === 'en') ? 'Level 4 - Education Development (Legal Matters)' : 'Tingkat 4 - Bahagian Pembangunan Pendidikan (Hal Ehwal Undang-Undang)'; ?>
                        </option>
                    </optgroup>

                    <!-- Aras / Level 5 - 7 -->
                    <optgroup label="<?php echo (isset($lang) && $lang === 'en') ? 'Level 5 to 7' : 'Tingkat 5 hingga 7'; ?>">
                        <option value="Level 5 - Education Development (Admin & Finance)">
                            <?php echo (isset($lang) && $lang === 'en') ? 'Level 5 - Education Development (Admin & Finance)' : 'Tingkat 5 - Bahagian Pembangunan Pendidikan (Pentadbiran & Kewangan)'; ?>
                        </option>
                        <option value="Level 6 - Innoprise Plantations Berhad">
                            <?php echo (isset($lang) && $lang === 'en') ? 'Level 6 - Innoprise Plantations Berhad' : 'Tingkat 6 - Innoprise Plantations Berhad'; ?>
                        </option>
                        <option value="Level 7 - Education Development (Scholarship)">
                            <?php echo (isset($lang) && $lang === 'en') ? 'Level 7 - Education Development (Scholarship)' : 'Tingkat 7 - Bahagian Pembangunan Pendidikan (Biasiswa)'; ?>
                        </option>
                    </optgroup>

                    <!-- Aras / Level 8 - 10 -->
                    <optgroup label="<?php echo (isset($lang) && $lang === 'en') ? 'Level 8 to 10' : 'Tingkat 8 hingga 10'; ?>">
                        <option value="Level 8 - Integrity & Enrichment Division">
                            <?php echo (isset($lang) && $lang === 'en') ? 'Level 8 - Integrity & Enrichment Division' : 'Tingkat 8 - Bahagian Integriti & Pengayaan'; ?>
                        </option>
                        <option value="Level 8 - ICT & OSH Division">
                            <?php echo (isset($lang) && $lang === 'en') ? 'Level 8 - ICT & Occupational Safety Health Division' : 'Tingkat 8 - Bahagian ICT & Keselamatan Kesihatan Pekerjaan'; ?>
                        </option>
                        <option value="Level 9 - Conservation & Env. Management (Sabah Nature Club)">
                            <?php echo (isset($lang) && $lang === 'en') ? 'Level 9 - Conservation & Env. Management (Sabah Nature Club)' : 'Tingkat 9 - Bahagian Pemuliharaan & Pengurusan Alam Sekitar (Kelab Pencinta Alam Sabah)'; ?>
                        </option>
                        <option value="Level 9 - Research Division">
                            <?php echo (isset($lang) && $lang === 'en') ? 'Level 9 - Research Division' : 'Tingkat 9 - Bahagian Penyelidikan'; ?>
                        </option>
                        <option value="Level 10 - Corporate Secretary's Office">
                            <?php echo (isset($lang) && $lang === 'en') ? 'Level 10 - Corporate Secretary\'s Office' : 'Tingkat 10 - Pejabat Setiausaha Korporat'; ?>
                        </option>
                    </optgroup>

                    <!-- Aras / Level 11 - 14 -->
                    <optgroup label="<?php echo (isset($lang) && $lang === 'en') ? 'Level 11 to 14' : 'Tingkat 11 hingga 14'; ?>">
                        <option value="Level 11 - West Coast South Zone">
                            <?php echo (isset($lang) && $lang === 'en') ? 'Level 11 - West Coast South Zone' : 'Tingkat 11 - ZON Pantai Barat Selatan'; ?>
                        </option>
                        <option value="Level 11 - Borneo Security Centre Sdn. Bhd.">
                            <?php echo (isset($lang) && $lang === 'en') ? 'Level 11 - Borneo Security Centre Sdn. Bhd.' : 'Tingkat 11 - Borneo Security Centre Sdn. Bhd.'; ?>
                        </option>
                        <option value="Level 12 - Conservation & Environmental Management">
                            <?php echo (isset($lang) && $lang === 'en') ? 'Level 12 - Conservation & Environmental Management Division' : 'Tingkat 12 - Bahagian Pemuliharaan & Pengurusan Alam Sekitar'; ?>
                        </option>
                        <option value="Level 13 - Chief Coordinator of Zone Admin">
                            <?php echo (isset($lang) && $lang === 'en') ? 'Level 13 - Chief Coordinator of Zone Administration\'s Office' : 'Tingkat 13 - Pejabat Ketua Penyelaras Pentadbiran Zon'; ?>
                        </option>
                        <option value="Level 13 - Integrity & Enrichment Division">
                            <?php echo (isset($lang) && $lang === 'en') ? 'Level 13 - Integrity & Enrichment Division' : 'Tingkat 13 - Bahagian Integriti & Pengayaan'; ?>
                        </option>
                        <option value="Level 14 - Real Estate Division">
                            <?php echo (isset($lang) && $lang === 'en') ? 'Level 14 - Real Estate Division' : 'Tingkat 14 - Bahagian Hartanah'; ?>
                        </option>
                    </optgroup>

                    <!-- Aras / Level 15 - 19 -->
                    <optgroup label="<?php echo (isset($lang) && $lang === 'en') ? 'Level 15 to 19' : 'Tingkat 15 hingga 19'; ?>">
                        <option value="Level 15 - Corporate Communications Division">
                            <?php echo (isset($lang) && $lang === 'en') ? 'Level 15 - Corporate Communications Division' : 'Tingkat 15 - Bahagian Komunikasi Korporat'; ?>
                        </option>
                        <option value="Level 16 - Research Division">
                            <?php echo (isset($lang) && $lang === 'en') ? 'Level 16 - Research Division' : 'Tingkat 16 - Bahagian Penyelidikan'; ?>
                        </option>
                        <option value="Level 19 - Director of Yayasan Sabah's Office">
                            <?php echo (isset($lang) && $lang === 'en') ? 'Level 19 - Director of Yayasan Sabah\'s Office' : 'Tingkat 19 - Pejabat Pengarah Pengurusan Yayasan Sabah'; ?>
                        </option>
                        <option value="Level 19 - Executive Assistant to Director">
                            <?php echo (isset($lang) && $lang === 'en') ? 'Level 19 - Executive Assistant to the Director' : 'Tingkat 19 - Pembantu Eksekutif Kepada Pengarah'; ?>
                        </option>
                        <option value="Level 19 - Energy & Facilities Division">
                            <?php echo (isset($lang) && $lang === 'en') ? 'Level 19 - Energy & Facilities Division' : 'Tingkat 19 - Bahagian Tenaga & Fasiliti'; ?>
                        </option>
                    </optgroup>

                    <!-- Aras / Level 20 - 22 -->
                    <optgroup label="<?php echo (isset($lang) && $lang === 'en') ? 'Level 20 to 22' : 'Tingkat 20 hingga 22'; ?>">
                        <option value="Level 20 - HR & Admin (HR Management Unit)">
                            <?php echo (isset($lang) && $lang === 'en') ? 'Level 20 - HR & Admin (HR Management Unit)' : 'Tingkat 20 - HR & Pentadbiran (Unit Pengurusan Sumber Manusia)'; ?>
                        </option>
                        <option value="Level 20 - Energy & Facilities Division">
                            <?php echo (isset($lang) && $lang === 'en') ? 'Level 20 - Energy & Facilities Division' : 'Tingkat 20 - Bahagian Tenaga & Fasiliti'; ?>
                        </option>
                        <option value="Level 21 - HR & Admin (HR Management Unit)">
                            <?php echo (isset($lang) && $lang === 'en') ? 'Level 21 - HR & Admin (HR Management Unit)' : 'Tingkat 21 - HR & Pentadbiran (Unit Pengurusan Sumber Manusia)'; ?>
                        </option>
                        <option value="Level 22 - ICT & OSH Division">
                            <?php echo (isset($lang) && $lang === 'en') ? 'Level 22 - ICT & Occupational Safety Health Division' : 'Tingkat 22 - Bahagian ICT & Keselamatan Kesihatan Pekerjaan'; ?>
                        </option>
                    </optgroup>

                    <!-- Aras / Level 23 - 27 -->
                    <optgroup label="<?php echo (isset($lang) && $lang === 'en') ? 'Level 23 to 27' : 'Tingkat 23 hingga 27'; ?>">
                        <option value="Level 23 - Internal Audit Division">
                            <?php echo (isset($lang) && $lang === 'en') ? 'Level 23 - Internal Audit Division' : 'Tingkat 23 - Bahagian Audit Dalaman'; ?>
                        </option>
                        <option value="Level 23 - Inno Resource Development Sdn. Bhd.">
                            <?php echo (isset($lang) && $lang === 'en') ? 'Level 23 - Inno Resource Development Sdn. Bhd.' : 'Tingkat 23 - Inno Resource Development Sdn. Bhd.'; ?>
                        </option>
                        <option value="Level 24 - Accounts & Financial Services">
                            <?php echo (isset($lang) && $lang === 'en') ? 'Level 24 - Accounts & Financial Services Division' : 'Tingkat 24 - Bahagian Akaun & Perkhidmatan Kewangan'; ?>
                        </option>
                        <option value="Level 25 - Accounts & Financial Services">
                            <?php echo (isset($lang) && $lang === 'en') ? 'Level 25 - Accounts & Financial Services Division' : 'Tingkat 25 - Bahagian Akaun & Perkhidmatan Kewangan'; ?>
                        </option>
                        <option value="Level 26 - Education Loan Collection Division">
                            <?php echo (isset($lang) && $lang === 'en') ? 'Level 26 - Education Loan Collection Division' : 'Tingkat 26 - Bahagian Kutipan Pinjaman Pendidikan'; ?>
                        </option>
                        <option value="Level 27 - Accounts & Financial Division">
                            <?php echo (isset($lang) && $lang === 'en') ? 'Level 27 - Accounts & Financial Division' : 'Tingkat 27 - Bahagian Akaun & Kewangan'; ?>
                        </option>
                    </optgroup>

                    <!-- Others / Lain-lain -->
                    <optgroup label="<?php echo (isset($lang) && $lang === 'en') ? 'Others' : 'Lain-lain Lokasi'; ?>">
                        <option value="Other - Parking Area">
                            <?php echo (isset($lang) && $lang === 'en') ? 'Outside Building / Parking Lot' : 'Luar Bangunan / Kawasan Tempat Letak Kereta'; ?>
                        </option>
                        <option value="Other - Unlisted Location">
                            <?php echo (isset($lang) && $lang === 'en') ? 'Others (Please Specify in Description)' : 'Lain-lain (Sila Nyatakan dalam Keterangan)'; ?>
                        </option>
                    </optgroup>
                </select>
            </div>

            <div class="form-group">
                <label><?php echo (isset($lang) && $lang === 'en') ? 'Incident Description' : 'Keterangan Insiden'; ?></label>
                <textarea name="description" rows="3" placeholder="<?php echo (isset($lang) && $lang === 'en') ? 'Describe the hazard or incident details...' : 'Jelaskan butiran insiden atau kronologi kejadian...'; ?>" required class="form-control"></textarea>
            </div>

            <div class="form-group">
                <label><?php echo (isset($lang) && $lang === 'en') ? 'Attach Photo Evidence' : 'Muat Naik Gambar (Jika ada)'; ?></label>
                <input type="file" name="attachment" accept="image/*" class="form-control">
            </div>

            <div class="hazard-modal-actions">
                <button type="button" class="btn-cancel" onclick="closeHazardModal()"><?php echo (isset($lang) && $lang === 'en') ? 'Cancel' : 'Batal'; ?></button>
                <button type="submit" class="btn-submit"><?php echo (isset($lang) && $lang === 'en') ? 'Submit Report' : 'Hantar Laporan'; ?></button>
            </div>
        </form>
    </div>
</div>

<style>
/* Modal Overlay Styling */
.hazard-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(15, 23, 42, 0.65);
    backdrop-filter: blur(4px);
    z-index: 9999;
    display: flex;
    justify-content: center;
    align-items: center;
}

.hazard-modal-box {
    background: #ffffff;
    width: 90%;
    max-width: 500px;
    border-radius: 10px;
    padding: 1.5rem;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
}

.hazard-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 0.75rem;
    margin-bottom: 1rem;
}

.hazard-modal-header h3 {
    margin: 0;
    font-size: 1.1rem;
    color: #0f172a;
}

.hazard-close-btn {
    font-size: 1.5rem;
    cursor: pointer;
    color: #64748b;
}

.hazard-close-btn:hover {
    color: #ef4444;
}

.form-group {
    margin-bottom: 1rem;
    text-align: left;
}

.form-group label {
    display: block;
    font-size: 0.85rem;
    font-weight: 600;
    color: #334155;
    margin-bottom: 0.3rem;
}

.form-control {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-size: 0.88rem;
    box-sizing: border-box;
}

.form-control:focus {
    outline: none;
    border-color: #d97706;
}

.hazard-modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 1.25rem;
}

.btn-cancel {
    background: #e2e8f0;
    color: #475569;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
}

.btn-submit {
    background: #d97706;
    color: #ffffff;
    border: none;
    padding: 0.5rem 1.2rem;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
}

.btn-submit:hover {
    background: #b45309;
}
</style>

<script>
function openHazardModal() {
    document.getElementById('hazardModal').style.display = 'flex';
}

function closeHazardModal() {
    document.getElementById('hazardModal').style.display = 'none';
}

// Close modal when clicking on background outside box
window.onclick = function(event) {
    var modal = document.getElementById('hazardModal');
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
</script>
        <!-- Public Announcements Section -->
        <div class="announcement-box" id="announcements">
            <h2 class="section-title" style="border:none; padding:0; margin-bottom:1rem;">
                <?php echo (isset($lang) && $lang === 'en') ? 'Public Advisories & Notices' : 'Pengumuman & Notis Awam'; ?>
            </h2>

            <!-- Announcement Item 1: BTMK -->
            <div class="announcement-item">
                <span class="announcement-badge badge-btmk">
                    <?php echo (isset($lang) && $lang === 'en') ? 'BTMK NOTICE' : 'NOTIS BTMK'; ?>
                </span>
                <div style="font-weight:bold; font-size: 1rem;">
                    <?php 
                        if (isset($lang) && $lang === 'en') {
                            echo 'Scheduled Server &amp; Portal Maintenance';
                        } else {
                            echo 'Penyelenggaraan Berjadual Server &amp; Portal';
                        }
                    ?>
                </div>
                <div style="font-size:0.8rem; color:#888;"> 
                    <?php echo (isset($lang) && $lang === 'en') ? 'August 12, 2026 | 10:00 PM - 02:00 AM' : '12 Ogos 2026 | 10:00 PM - 02:00 AM'; ?>
                </div>
                <p style="font-size: 0.88rem; color: #555; margin: 0.4rem 0 0;">
                    <?php 
                        if (isset($lang) && $lang === 'en') {
                            echo 'The portal system and internal ticketing services may experience temporary interruption during this maintenance window.';
                        } else {
                            echo 'Sistem portal dan perkhidmatan bantuan dalaman mungkin mengalami gangguan sementara dalam tempoh penyelenggaraan ini.';
                        }
                    ?>
                </p>
            </div>

            <!-- Announcement Item 2: KKP / OSH -->
            <div class="announcement-item">
                <span class="announcement-badge badge-kkp">
                    <?php echo (isset($lang) && $lang === 'en') ? 'OSH ADVISORY' : 'KHIDMAT NASIHAT KKP'; ?>
                </span>
                <div style="font-weight:bold; font-size: 1rem;">
                    <?php 
                        if (isset($lang) && $lang === 'en') {
                            echo 'Fire Drill &amp; Building Safety Drill at YS Tower';
                        } else {
                            echo 'Latihan Kebakaran &amp; Keselamatan Bangunan Menara YS';
                        }
                    ?>
                </div>
                <div style="font-size:0.8rem; color:#888;"> 
                    <?php echo (isset($lang) && $lang === 'en') ? 'August 25, 2026' : '25 Ogos 2026'; ?>
                </div>
                <p style="font-size: 0.88rem; color: #555; margin: 0.4rem 0 0;">
                    <?php 
                        if (isset($lang) && $lang === 'en') {
                            echo 'All visitors and staff at Menara Yayasan Sabah are requested to follow Safety Officers instructions during the exercise.';
                        } else {
                            echo 'Semua pelawat dan warga di Menara Yayasan Sabah diminta mematuhi arahan Pegawai Keselamatan semasa latihan dijalankan.';
                        }
                    ?>
                </p>
            </div>
        </div>

        <!-- Hotlines & Contact Directory Section -->
        <div class="hotlines-section" id="hotlines">
            <h2 class="section-title" style="border:none; padding:0; margin-bottom:0.5rem;">
                <?php echo (isset($lang) && $lang === 'en') ? 'Emergency & Direct Hotlines' : 'Talian Kecemasan & Hubungi Kami'; ?>
            </h2>
            <p style="color: #64748b; font-size: 0.9rem; margin-top:0;">
                <?php echo (isset($lang) && $lang === 'en') ? 'Get in touch directly with our BTMK technical team or OSH safety division.' : 'Hubungi terus pasukan teknikal BTMK atau Unit Keselamatan Kesihatan Pekerjaan.'; ?>
            </p>
            <div class="hotlines-grid">
                <div class="hotline-card">
                    <span class="hotline-icon"></span>
                    <div class="hotline-info">
                        <h4>
                            <?php echo (isset($lang) && $lang === 'en') ? 'BTMK Helpdesk Line' : 'Talian Helpdesk BTMK'; ?>
                        </h4>
                        <p>+60 88-123456</p>
                        <small style="color:#64748b;">helpdesk@ys.sabah.gov.my</small>
                    </div>
                </div>
                <div class="hotline-card">
                    <span class="hotline-icon"></span>
                    <div class="hotline-info">
                        <h4>
                            <?php echo (isset($lang) && $lang === 'en') ? 'OSH Emergency Hotline (24/7)' : 'Talian Kecemasan KKP'; ?>
                        </h4>
                        <p>+60 88-654321</p>
                        <small style="color:#64748b;">kkp@ys.sabah.gov.my</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Side-by-Side Grid: Pusat Panduan (Left) & Location Map (Right) -->
        <div class="guide-map-wrapper">
         <div class="guide-section" id="guide-center">
    <div>
        <h2 class="section-title" style="border:none; padding:0; margin-bottom: 0.5rem;">
            <?php echo (isset($lang) && $lang === 'en') ? 'Guide Center & Knowledge Base' : 'Pusat Panduan & Maklumat'; ?>
        </h2>
        <p style="color: #64748b; font-size: 0.88rem; margin-top:0;">
            <?php echo (isset($lang) && $lang === 'en') ? 'Select popular keywords or search for specific guides and procedures below.' : 'Pilih kata kunci popular atau buat carian borang, SOP, dan panduan teknikal di bawah.'; ?>
        </p>

        <!-- Tag Kata Kunci Popular -->
        <div class="keywords-container">
            <span class="keyword-tag" onclick="filterGuide('wi-fi')">
                <?php echo (isset($lang) && $lang === 'en') ? '# Guest Wi-Fi' : '# Wi-Fi Tetamu'; ?>
            </span>
            <span class="keyword-tag" onclick="filterGuide('btmk')">
                <?php echo (isset($lang) && $lang === 'en') ? '# Helpdesk Ticket' : '# Bantuan BTMK'; ?>
            </span>
            <span class="keyword-tag" onclick="filterGuide('hazard')">
                <?php echo (isset($lang) && $lang === 'en') ? '# Hazard Report' : '# Laporan Insiden'; ?>
            </span>
            <span class="keyword-tag" onclick="filterGuide('kontraktor')">
                <?php echo (isset($lang) && $lang === 'en') ? '# Safety Induction' : '# Induksi Kontraktor'; ?>
            </span>
            <span class="keyword-tag active-tag" onclick="filterGuide('all')">
                <?php echo (isset($lang) && $lang === 'en') ? 'Show All' : 'Papar Semua'; ?>
            </span>
        </div>

        <!-- Kad Panduan Grid -->
        <div class="guide-grid" id="guideGridContainer">
            
            <!-- 1. Panduan Wi-Fi Tetamu -->
            <div class="guide-item" data-keywords="wi-fi internet guest tetamu wireless daftar" onclick="openGuideModal('wifi')">
                <h4>
                    <?php echo (isset($lang) && $lang === 'en') ? 'Guest Wi-Fi Guide' : 'Panduan Wi-Fi Tetamu'; ?>
                </h4>
                <p>
                    <?php 
                        if (isset($lang) && $lang === 'en') {
                            echo 'How to register and obtain internet connectivity while visiting Tun Mustapha Tower.';
                        } else {
                            echo 'Cara daftar dan dapatkan akses sambungan internet semasa berada di Menara Tun Mustapha.';
                        }
                    ?>
                </p>
            </div>

            <!-- 2. Borang Tiket BTMK -->
            <div class="guide-item" data-keywords="bantuan ict helpdesk btmk komputer email printer" onclick="openGuideModal('btmk')">
                <h4>
                    <?php echo (isset($lang) && $lang === 'en') ? 'BTMK Support Form' : 'Borang Bantuan/Aduan BTMK'; ?>
                </h4>
                <p>
                    <?php 
                        if (isset($lang) && $lang === 'en') {
                            echo 'Step-by-step to submit tickets for computer issues, official emails, and printer troubleshooting.';
                        } else {
                            echo 'Langkah-langkah membuat aduan masalah sistem-sistem dan e-mel rasmi.';
                        }
                    ?>
                </p>
            </div>

            <!-- 3. Pengurusan Hazard KKP -->
            <div class="guide-item" data-keywords="hirarc laporan insiden kkp osh report keselamatan bahaya" onclick="openGuideModal('hazard')">
                <h4>
                    <?php echo (isset($lang) && $lang === 'en') ? 'OSH Hazard Management' : 'Pengurusan Insiden KKP'; ?>
                </h4>
                <p>
                    <?php 
                        if (isset($lang) && $lang === 'en') {
                            echo 'Guidelines for reporting safety risks across building areas and facilities.';
                        } else {
                            echo 'Panduan melaporkan sebarang risiko keselamatan di kawasan bangunan dan fasiliti.';
                        }
                    ?>
                </p>
            </div>

            <!-- 4. Keselamatan Kontraktor -->
            <div class="guide-item" data-keywords="kontraktor contractor induksi keselamatan safety ppe kerja premis" onclick="openGuideModal('kontraktor')">
                <h4>
                    <?php echo (isset($lang) && $lang === 'en') ? 'Contractor Safety' : 'Keselamatan Kontraktor'; ?>
                </h4>
                <p>
                    <?php 
                        if (isset($lang) && $lang === 'en') {
                            echo 'Check-in procedures and mandatory PPE compliance for technical works on site.';
                        } else {
                            echo 'Prosedur daftar masuk dan pemakaian PPE bagi kerja-kerja teknikal di premis.';
                        }
                    ?>
                </p>
            </div>

        </div>
    </div>

    <!-- Ruang Carian Cepat -->
    <div class="search-bottom-box">
        <input type="text" id="guideSearchInput" onkeyup="searchGuides()" placeholder="<?php echo (isset($lang) && $lang === 'en') ? 'Search guide center...' : 'Cari pusat panduan...'; ?>">
    </div>
</div>

<!-- ========================================== -->
<!-- 2. POPUP MODAL CONTAINER                   -->
<!-- ========================================== -->
<div id="guideModal" class="modal-overlay" onclick="closeGuideModal(event)">
    <div class="modal-content" onclick="event.stopPropagation()">
        <span class="modal-close" onclick="closeGuideModal()">&times;</span>
        <div id="modalBody">
            <!-- Kandungan panduan akan dimasukkan melalui JavaScript -->
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- 3. STYLESHEET (CSS)                        -->
<!-- ========================================== -->
<style>
/* Layout Cad Teras */
.guide-section {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    background: #ffffff;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    border: 1px solid #e2e8f0;
}

.keywords-container {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 1.25rem;
}

.keyword-tag {
    background-color: #f1f5f9;
    color: #475569;
    font-size: 0.78rem;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.2s ease;
    border: 1px solid #e2e8f0;
}

.keyword-tag:hover, .keyword-tag.active-tag {
    background-color: #002B49;
    color: #ffffff;
    border-color: #002B49;
}

.guide-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 12px;
    margin-bottom: 1.5rem;
}

.guide-item {
    display: flex;
    flex-direction: column;
    padding: 1rem;
    background-color: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
}

.guide-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.08);
    border-color: #002B49;
    background-color: #ffffff;
}

.guide-item h4 {
    margin: 0 0 6px 0;
    font-size: 0.95rem;
    color: #002B49;
    font-weight: 700;
}

.guide-item p {
    margin: 0;
    font-size: 0.82rem;
    color: #64748b;
    line-height: 1.45;
}

.search-bottom-box {
    margin-top: auto;
    padding-top: 1rem;
    border-top: 1px solid #f1f5f9;
}

.search-bottom-box input {
    width: 100%;
    padding: 10px 14px;
    border-radius: 6px;
    border: 1px solid #cbd5e1;
    font-size: 0.9rem;
    outline: none;
    transition: border-color 0.2s ease;
    box-sizing: border-box;
}

.search-bottom-box input:focus {
    border-color: #002B49;
    box-shadow: 0 0 0 3px rgba(0,43,73,0.1);
}

/* Gaya Popup Modal */
.modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.6);
    z-index: 2000;
    justify-content: center;
    align-items: center;
    backdrop-filter: blur(3px);
}

.modal-content {
    background-color: #ffffff;
    width: 90%;
    max-width: 600px;
    padding: 2rem;
    border-radius: 12px;
    position: relative;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    animation: modalSlide 0.25s ease-out;
}

@keyframes modalSlide {
    from { transform: translateY(-15px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.modal-close {
    position: absolute;
    top: 12px;
    right: 18px;
    font-size: 1.8rem;
    font-weight: bold;
    color: #64748b;
    cursor: pointer;
    line-height: 1;
}

.modal-close:hover {
    color: #002B49;
}
</style>

<!-- ========================================== -->
<!-- 4. JAVASCRIPT LOGIC                        -->
<!-- ========================================== -->
<script>
// Data Kandungan Panduan untuk Sesi Demo
const guideData = {
    'wifi': {
        title: '<?php echo (isset($lang) && $lang === "en") ? "Guest Wi-Fi Guide" : "Panduan Wi-Fi Tetamu"; ?>',
        content: `
            <p style="margin-top:0; color:#334155;"><strong><?php echo (isset($lang) && $lang === "en") ? "Tun Mustapha Tower Guest Wi-Fi Access Procedure:" : "Prosedur Akses Rangkaian Wi-Fi Tetamu Menara Tun Mustapha:"; ?></strong></p>
            <ol style="line-height: 1.8; padding-left: 20px; color: #475569; font-size: 0.9rem;">
                <li><?php echo (isset($lang) && $lang === "en") ? "Enable Wi-Fi on your device and connect to SSID <strong>'YS-Guest-WiFi'</strong>." : "Aktifkan Wi-Fi pada peranti anda dan sambung ke SSID <strong>'YS-Guest-WiFi'</strong>."; ?></li>
                <li><?php echo (isset($lang) && $lang === "en") ? "Web browser will automatically open the login portal." : "Pelayar web (Browser) akan dibuka secara automatik ke portal log masuk."; ?></li>
                <li><?php echo (isset($lang) && $lang === "en") ? "Fill in brief information (Name, Phone No. & Email)." : "Isikan maklumat ringkas (Nama, No. Telefon & E-mel)."; ?></li>
                <li><?php echo (isset($lang) && $lang === "en") ? "Click the <strong>'Register & Connect'</strong> button." : "Klik butang <strong>'Daftar & Sambung'</strong>."; ?></li>
            </ol>
            <div style="background:#f1f5f9; padding:12px; border-left:4px solid #002B49; border-radius:4px; margin-top:15px; font-size:0.85rem; color:#475569;">
                <strong><?php echo (isset($lang) && $lang === "en") ? "Notice:" : "Perhatian:"; ?></strong> <?php echo (isset($lang) && $lang === "en") ? "This access is valid for 24 hours. For recurring official matters, please contact BTMK Secretariat." : "Akses ini sah selama 24 jam. Bagi urusan rasmi berulang, sila hubungi Urus Setia BTMK."; ?>
            </div>
            <div style="margin-top:20px; text-align:right;">
                <button onclick="closeGuideModal()" style="background:#002B49; color:#fff; border:none; padding:8px 16px; border-radius:6px; cursor:pointer; font-size:0.88rem; font-weight:600;">
                    <?php echo (isset($lang) && $lang === "en") ? "Understood & Close" : "Faham & Tutup"; ?>
                </button>
            </div>
        `
    },
    'btmk': {
        title: '<?php echo (isset($lang) && $lang === "en") ? "BTMK Support Form" : "Borang Bantuan BTMK"; ?>',
        content: `
            <p style="margin-top:0; color:#334155;"><strong><?php echo (isset($lang) && $lang === "en") ? "ICT Helpdesk Submission Guide:" : "Panduan Penghantaran Aduan ICT:"; ?></strong></p>
            <ul style="line-height: 1.8; padding-left: 20px; color: #475569; font-size: 0.9rem;">
                <li><?php echo (isset($lang) && $lang === "en") ? "Log in to the system using your account credentials." : "Log masuk ke sistem dengan peranan akaun anda."; ?></li>
                <li><?php echo (isset($lang) && $lang === "en") ? "Select issue category: <em>Computer, Official Email, Network, or Printer</em>." : "Pilih kategori isu: <em>Sistem, E-mel Rasmi atau Rangkaian."; ?></li>
                <li><?php echo (isset($lang) && $lang === "en") ? "Include location/floor details and a brief description of the technical issue." : "Sertakan maklumat lokasi/aras dan penerangan ringkas mengenai isu teknikal."; ?></li>
                <li><?php echo (isset($lang) && $lang === "en") ? "BTMK officers will take action based on the priority level of the complaint." : "Pegawai BTMK akan mengambil tindakan mengikut tahap keutamaan aduan."; ?></li>
            </ul>
            <div style="margin-top:20px; text-align:right;">
                <button onclick="closeGuideModal()" style="background:#002B49; color:#fff; border:none; padding:8px 16px; border-radius:6px; cursor:pointer; font-size:0.88rem; font-weight:600;">
                    <?php echo (isset($lang) && $lang === "en") ? "Understood & Close" : "Faham & Tutup"; ?>
                </button>
            </div>
        `
    },
    'hazard': {
        title: '<?php echo (isset($lang) && $lang === "en") ? "OSH Hazard Management" : "Pengurusan Insiden KKP"; ?>',
        content: `
            <p style="margin-top:0; color:#334155;"><strong><?php echo (isset($lang) && $lang === "en") ? "Hazard & Safety Risk Reporting Procedure:" : "Prosedur Pelaporan Insiden & Risiko Keselamatan:"; ?></strong></p>
            <p style="color: #475569; line-height: 1.6; font-size: 0.9rem;">
                <?php echo (isset($lang) && $lang === "en") ? "Every staff member and visitor is required to report any potential physical hazards such as:" : "Setiap staf dan pelawat dikehendaki melaporkan sebarang potensi bahaya fizikal seperti:"; ?>
            </p>
            <ul style="line-height: 1.8; padding-left: 20px; color: #475569; font-size: 0.9rem;">
                <li><?php echo (isset($lang) && $lang === "en") ? "Slippery/damaged floors without warning signs." : "Lantai licin/rosak tanpa tanda amaran."; ?></li>
                <li><?php echo (isset($lang) && $lang === "en") ? "Exposed or damaged electrical wiring." : "Pendawaian elektrik yang terdedah atau rosak."; ?></li>
                <li><?php echo (isset($lang) && $lang === "en") ? "Damaged safety facilities (fire extinguishers, blocked emergency exits)." : "Kerosakan fasiliti keselamatan (alat pemadam api, pintu kecemasan terhalang)."; ?></li>
            </ul>
            <div style="margin-top:20px; text-align:right;">
                <button onclick="closeGuideModal()" style="background:#002B49; color:#fff; border:none; padding:8px 16px; border-radius:6px; cursor:pointer; font-size:0.88rem; font-weight:600;">
                    <?php echo (isset($lang) && $lang === "en") ? "Understood & Close" : "Faham & Tutup"; ?>
                </button>
            </div>
        `
    },
    'kontraktor': {
        title: '<?php echo (isset($lang) && $lang === "en") ? "Contractor Safety" : "Keselamatan Kontraktor"; ?>',
        content: `
            <p style="margin-top:0; color:#334155;"><strong><?php echo (isset($lang) && $lang === "en") ? "Induction & Contractor Work Requirements:" : "Syarat Induksi & Syarat Kerja Kontraktor:"; ?></strong></p>
            <ol style="line-height: 1.8; padding-left: 20px; color: #475569; font-size: 0.9rem;">
                <li><?php echo (isset($lang) && $lang === "en") ? "Mandatory registration at the Ground Floor Security Counter before starting work." : "Pendaftaran wajib di Kaunter Keselamatan Aras Bawah sebelum kerja bermula."; ?></li>
                <li><?php echo (isset($lang) && $lang === "en") ? "Wear a valid contractor pass and PPE equipment (Safety shoes, helmet if applicable)." : "Memakai pas kontraktor sah dan peralatan PPE (Kasut keselamatan, topi keledar jika berkaitan)."; ?></li>
                <li><?php echo (isset($lang) && $lang === "en") ? "<em>Permit To Work (PTW)</em> application is required for high-risk work." : "Permohonan <em>Permit To Work (PTW)</em> diperlukan bagi kerja berisiko tinggi."; ?></li>
            </ol>
            <div style="margin-top:20px; text-align:right;">
                <button onclick="closeGuideModal()" style="background:#002B49; color:#fff; border:none; padding:8px 16px; border-radius:6px; cursor:pointer; font-size:0.88rem; font-weight:600;">
                    <?php echo (isset($lang) && $lang === "en") ? "Understood & Close" : "Faham & Tutup"; ?>
                </button>
            </div>
        `
    }
};

// Buka Modal Popup
function openGuideModal(type) {
    const data = guideData[type];
    if (data) {
        document.getElementById('modalBody').innerHTML = `
            <h3 style="color:#002B49; margin-top:0; border-bottom:2px solid #D4AF37; padding-bottom:8px; font-size:1.15rem;">${data.title}</h3>
            <div style="margin-top:12px;">${data.content}</div>
        `;
        document.getElementById('guideModal').style.display = 'flex';
    }
}

// Tutup Modal Popup
function closeGuideModal(event) {
    document.getElementById('guideModal').style.display = 'none';
}

// Carian Panduan
function searchGuides() {
    let input = document.getElementById('guideSearchInput').value.toLowerCase();
    let items = document.querySelectorAll('.guide-item');

    items.forEach(function(item) {
        let keywords = item.getAttribute('data-keywords').toLowerCase();
        let title = item.querySelector('h4').innerText.toLowerCase();
        let desc = item.querySelector('p').innerText.toLowerCase();

        if (keywords.includes(input) || title.includes(input) || desc.includes(input)) {
            item.style.display = "flex";
        } else {
            item.style.display = "none";
        }
    });
}

// Tapisan Kata Kunci (Filter Tag)
function filterGuide(category) {
    let items = document.querySelectorAll('.guide-item');
    let tags = document.querySelectorAll('.keyword-tag');

    tags.forEach(tag => tag.classList.remove('active-tag'));
    if (event && event.target) {
        event.target.classList.add('active-tag');
    }

    if (category === 'all' || category === 'Semua') {
        items.forEach(item => item.style.display = "flex");
        return;
    }

    items.forEach(function(item) {
        let keywords = item.getAttribute('data-keywords').toLowerCase();
        if (keywords.includes(category.toLowerCase())) {
            item.style.display = "flex";
        } else {
            item.style.display = "none";
        }
    });
}
</script>

            <!-- Location & Interactive Map Section (RIGHT) -->
            <div class="map-section" id="location-map">
                <h2 class="section-title" style="border:none; padding:0; margin-bottom:0.5rem;">
                    <?php echo (isset($lang) && $lang === 'en') ? 'Location & Main Headquarters' : 'Lokasi & Ibu Pejabat'; ?>
                </h2>
                <p style="color: #64748b; font-size: 0.88rem; margin-top:0;">
                    Menara Tun Mustapha, Teluk Likas, 88400 Kota Kinabalu, Sabah.
                </p>
                <div class="map-container">
                    <iframe 
                        width="100%" 
                        height="100%" 
                        style="border:0;" 
                        loading="lazy" 
                        allowfullscreen 
                        referrerpolicy="no-referrer-when-downgrade" 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3968.1023773128916!2d116.1070544!3d5.9984628!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x323b6928e3ad5f57%3A0xc36762aa8e74a8a5!2sMenara%20Tun%20Mustapha!5e0!3m2!1sms!2smy!4v1700000000000!5m2!1sms!2smy">
                    </iframe>
                </div>
            </div>
        </div>

    </div>

    <!-- Sabah Pattern Divider Bottom -->
    <div class="sabah-pattern-border"></div>

    <!-- Public Footer -->
<div class="footer">
    &copy; <?php echo date('Y'); ?> 
    <?php 
        if (isset($lang) && $lang === 'en') {
            echo 'Yayasan Sabah Group. All Rights Reserved.';
        } else {
            echo 'Kumpulan Yayasan Sabah. Hak Cipta Terpelihara.';
        }
    ?> 
    <br>
    <small style="opacity: 0.8;">
        <?php 
            if (isset($lang) && $lang === 'en') {
                echo 'Information Technology and Communication Division (BTMK) | Occupational Safety &amp; Health Unit (OSH)';
            } else {
                echo 'Bahagian Teknologi Maklumat dan Komunikasi (BTMK) | Unit Keselamatan Kesihatan Pekerjaan (KKP)';
            }
        ?>
    </small>
</div>
    <script>
    // --- Hero Slideshow Logic ---
    let slideIndex = 0;
    const slides = document.querySelectorAll('.slide-item');
    const dots = document.querySelectorAll('.dot');

    function showSlides() {
        slides.forEach((slide, idx) => {
            slide.classList.remove('active');
            dots[idx].classList.remove('active');
        });
        slideIndex++;
        if (slideIndex > slides.length) { slideIndex = 1; }
        slides[slideIndex - 1].classList.add('active');
        dots[slideIndex - 1].classList.add('active');
        setTimeout(showSlides, 5000); // Change image every 5 seconds
    }

    function currentSlide(n) {
        slides.forEach((slide, idx) => {
            slide.classList.remove('active');
            dots[idx].classList.remove('active');
        });
        slideIndex = n;
        slides[slideIndex].classList.add('active');
        dots[slideIndex].classList.add('active');
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (slides.length > 0) {
            setTimeout(showSlides, 5000);
        }
        initVisitorCounter();
    });

    // --- Visitor Counter Logic (LocalStorage base) ---
    function initVisitorCounter() {
        let visits = localStorage.getItem('ys_portal_visits') || 12480;
        let todayVisits = localStorage.getItem('ys_portal_today') || 142;

        if (!sessionStorage.getItem('ys_visited')) {
            visits = parseInt(visits) + 1;
            todayVisits = parseInt(todayVisits) + 1;
            localStorage.setItem('ys_portal_visits', visits);
            localStorage.setItem('ys_portal_today', todayVisits);
            sessionStorage.setItem('ys_visited', 'true');
        }

        document.getElementById('totalVisits').textContent = parseInt(visits).toLocaleString();
        document.getElementById('todayVisits').textContent = parseInt(todayVisits).toLocaleString();
    }

    // --- Navigation Panel Switcher ---
    window.switchPortalPanel = function(targetId) {
        const targetElement = document.getElementById(targetId) || document.querySelector('.' + targetId + '-panel');
        if (targetElement) {
            targetElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
            targetElement.classList.remove('highlight-target');
            void targetElement.offsetWidth;
            targetElement.classList.add('highlight-target');
        }
    };

    function filterGuide(keyword) {
        var input = document.getElementById('guideSearchInput');
        if (keyword === 'Semua') {
            input.value = '';
        } else {
            input.value = keyword;
        }
        searchGuides();
    }

    function searchGuides() {
        var input = document.getElementById('guideSearchInput');
        var filter = input.value.toLowerCase();
        var container = document.getElementById('guideGridContainer');
        var items = container.getElementsByClassName('guide-item');

        for (var i = 0; i < items.length; i++) {
            var text = items[i].textContent || items[i].innerText;
            var keywords = items[i].getAttribute('data-keywords') || '';
            if (text.toLowerCase().indexOf(filter) > -1 || keywords.toLowerCase().indexOf(filter) > -1) {
                items[i].style.display = "";
            } else {
                items[i].style.display = "none";
            }
        }
    }
    </script>

</body>
</html>