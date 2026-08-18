<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../lang.php';
?>

<!-- Immediate Scroll Restoration Script -->
<script>
(function() {
    if ('scrollRestoration' in history) {
        history.scrollRestoration = 'manual';
    }

    const savedPos = localStorage.getItem('ys_lang_scroll_pos');
    if (savedPos !== null) {
        document.documentElement.classList.add('no-smooth-scroll');
        window.scrollTo(0, parseInt(savedPos, 10));
    }
})();

function switchLanguage(selectedLang) {
    const currentPos = window.scrollY || window.pageYOffset || document.documentElement.scrollTop;
    localStorage.setItem('ys_lang_scroll_pos', currentPos);

    const currentUrl = new URL(window.location.href);
    currentUrl.hash = '';
    currentUrl.searchParams.set('lang', selectedLang);
    window.location.href = currentUrl.toString();
}
</script>

<style>
    :root {
        --primary-blue: #002B49;
        --dark-blue: #001A2D;
        --accent-gold: #D4AF37;
        --accent-gold-hover: #f1c40f;
        --bg-light: #f4f6f9;
        --danger-red: #a91e2c;
        --danger-red-hover: #821722;
    }

    /* Universal Box-Sizing for Header elements */
    .top-utility-bar, .top-utility-bar *, 
    .top-header, .top-header * {
        box-sizing: border-box;
    }

    /* Top Utility Bar */
    .top-utility-bar {
        background-color: var(--dark-blue);
        color: #ffffff;
        font-size: 0.85rem;
        padding: 0.5rem 2rem;
        width: 100%;
        border-bottom: 2px solid var(--accent-gold);
    }

    .utility-container {
        width: 100%;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .utility-left {
        display: flex;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }

    .utility-right {
        display: flex;
        align-items: center;
        margin-left: auto;
    }

    .utility-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .utility-item strong {
        color: var(--accent-gold);
    }

    .lang-selector {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .lang-btn {
        background: transparent;
        border: none;
        color: rgba(255, 255, 255, 0.7);
        cursor: pointer;
        font-weight: bold;
        font-size: 0.9rem;
        padding: 4px 8px;
        border-radius: 4px;
        transition: all 0.2s;
    }

    .lang-btn.active {
        color: var(--primary-blue);
        background-color: var(--accent-gold);
        text-decoration: none;
    }

    .lang-divider {
        color: var(--accent-gold);
        font-weight: bold;
    }

    /* Main Sticky Header Navigation */
    .top-header {
        background-color: var(--primary-blue);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        position: sticky;
        top: 0;
        z-index: 1000;
        width: 100%;
        border-bottom: 3px solid var(--accent-gold);
    }

    .header-container {
        width: 100%;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.6rem 2rem;
        flex-wrap: nowrap;
        gap: 1.5rem;
    }

    .brand-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        min-width: 0;
        flex: 1 1 auto;
    }

    .crest-img {
        height: 46px;
        width: auto;
        flex-shrink: 0;
        object-fit: contain;
        filter: drop-shadow(0px 2px 4px rgba(0,0,0,0.4));
    }

    .crest-img.ys-circle-logo {
        height: 46px;
        width: 46px;
        background-color: #ffffff;
        border-radius: 50%;
        padding: 4px;
        box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.35);
    }

    .brand-text {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .brand-title {
        text-decoration: none;
        color: #ffffff;
        font-weight: 800;
        font-size: 1.1rem;
        line-height: 1.2;
        letter-spacing: 0.3px;
        text-shadow: 0px 1px 3px rgba(0,0,0,0.4);
    }

    .brand-sub {
        font-size: 0.78rem;
        color: var(--accent-gold);
        font-weight: 700;
        letter-spacing: 0.5px;
        margin-top: 1px;
    }

    /* Navigation Links & Buttons */
    .nav-links {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 1.25rem;
        flex-wrap: nowrap;
        justify-content: flex-end;
        flex-shrink: 0;
    }

    .nav-links > a:not(.btn-dashboard):not(.btn-logout):not(.btn-login),
    .dropdown-toggle {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.92rem;
        font-weight: 600;
        padding: 0.5rem 0.25rem;
        text-decoration: none;
        color: #ffffff;
        white-space: nowrap;
        cursor: pointer;
        transition: color 0.2s ease;
    }

    .nav-links > a:hover,
    .dropdown-toggle:hover {
        color: var(--accent-gold);
    }

    .btn-dashboard {
        background-color: var(--accent-gold);
        color: var(--primary-blue);
        padding: 0.5rem 1rem;
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.92rem;
        font-weight: bold;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        white-space: nowrap;
        transition: background-color 0.2s ease, transform 0.2s ease;
    }

    .btn-dashboard:hover {
        background-color: var(--accent-gold-hover);
        transform: translateY(-1px);
    }

    .btn-logout {
        background-color: var(--danger-red);
        color: #ffffff;
        padding: 0.5rem 0.9rem;
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.88rem;
        font-weight: 700;
        margin-left: 6px;
        white-space: nowrap;
        transition: background-color 0.2s ease;
    }

    .btn-logout:hover {
        background-color: var(--danger-red-hover);
    }

    .btn-login {
        background-color: var(--accent-gold);
        color: var(--primary-blue);
        padding: 0.5rem 0.9rem;
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.88rem;
        font-weight: 700;
        white-space: nowrap;
        transition: background-color 0.2s ease, transform 0.2s ease;
    }

    .btn-login:hover {
        background-color: var(--accent-gold-hover);
        transform: translateY(-1px);
    }

    /* Dropdown Styling */
    .nav-dropdown {
        position: relative;
        display: inline-flex;
        align-items: center;
    }

    .dropdown-menu {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        background-color: #ffffff;
        min-width: 200px;
        box-shadow: 0px 8px 18px rgba(0, 0, 0, 0.15);
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        z-index: 1000;
        padding: 0.4rem 0;
    }

    .dropdown-menu a {
        color: #1e293b !important;
        padding: 0.6rem 1rem !important;
        text-decoration: none;
        display: block !important;
        font-size: 0.88rem !important;
        font-weight: 500 !important;
        transition: background-color 0.2s ease, color 0.2s ease;
    }

    .dropdown-menu a:hover {
        background-color: #f1f5f9;
        color: var(--primary-blue) !important;
    }

    .nav-dropdown:hover .dropdown-menu {
        display: block;
    }

    /* Responsive adjustments for mobile screens */
    @media (max-width: 900px) {
        .utility-container {
            justify-content: center;
            text-align: center;
            padding: 0.3rem;
        }

        .utility-left {
            justify-content: center;
            font-size: 0.78rem;
            gap: 10px;
        }

        .utility-right {
            margin-left: 0;
            justify-content: center;
            width: 100%;
        }

        .header-container {
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 1rem;
            gap: 15px;
        }

        .brand-wrapper {
            flex-direction: column;
            gap: 8px;
            text-align: center;
        }

        .brand-title {
            font-size: 1rem;
        }

        .brand-sub {
            font-size: 0.75rem;
        }

        .nav-links {
            justify-content: center;
            gap: 1rem;
            width: 100%;
            flex-wrap: wrap;
        }
    }
</style>

<!-- Top Contact & Language Utility Bar -->
<div class="top-utility-bar">
    <div class="utility-container">
        <div class="utility-left">
            <div class="utility-item">
                <span><strong><?php echo ($lang === 'en') ? 'BTMK Helpdesk:' : 'Talian BTMK:'; ?></strong> +60 88-123456 | helpdesk@ys.sabah.gov.my</span>
            </div>
            <div class="utility-item">
                <span><strong><?php echo ($lang === 'en') ? 'OSH Emergency:' : 'Kecemasan KKP:'; ?></strong> +60 88-654321</span>
            </div>
        </div>
        <div class="utility-right">
            <div class="lang-selector">
                <span class="lang-icon"></span>
                <span class="lang-label"><?php echo isset($L['language']) ? $L['language'] : 'Bahasa'; ?>:</span>
                <button type="button" 
                        onclick="switchLanguage('bm')" 
                        class="lang-btn <?php echo (isset($lang) && $lang === 'bm') || (isset($current_lang) && ($current_lang === 'bm' || $current_lang === 'ms')) ? 'active' : ''; ?>">BM</button>
                <span class="lang-divider">|</span>
                <button type="button" 
                        onclick="switchLanguage('en')" 
                        class="lang-btn <?php echo (isset($lang) && $lang === 'en') || (isset($current_lang) && $current_lang === 'en') ? 'active' : ''; ?>">EN</button>
            </div>
        </div>
    </div>
</div>

<!-- Main Sticky Header Navigation -->
<header class="top-header">
    <div class="header-container">
        
        <!-- Logos & Title -->
        <div class="brand-wrapper">
            <img src="https://upload.wikimedia.org/wikipedia/commons/2/26/Coat_of_arms_of_Malaysia.svg"
                 alt="Jata Malaysia"
                 class="crest-img">
            
            <img src="assets/images/yayasan_sabah.png"  
                 alt="Yayasan Sabah"
                 class="crest-img ys-circle-logo">  
            
            <img src="https://upload.wikimedia.org/wikipedia/commons/6/68/Coat_of_arms_of_Sabah.svg"
                 alt="Jata Sabah Maju Jaya"
                 class="crest-img">

            <div class="brand-text">
                <a href="index.php" class="brand-title">
                    <?php 
                        if (isset($lang) && $lang === 'en') {
                            echo 'INFORMATION TECHNOLOGY AND COMMUNICATION DIVISION &amp; OCCUPATIONAL SAFETY HEALTH PORTAL';
                        } else {
                            echo 'PORTAL BAHAGIAN TEKNOLOGI MAKLUMAT DAN KOMUNIKASI &amp; KESELAMATAN KESIHATAN PEKERJAAN';
                        }
                    ?>
                </a>
                <span class="brand-sub">
                    <?php 
                        if (isset($lang) && $lang === 'en') {
                            echo 'YAYASAN SABAH GROUP';
                        } else {
                            echo 'KUMPULAN YAYASAN SABAH';
                        }
                    ?>
                </span>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="nav-links">
            <?php if (empty($hide_public_nav)): ?>
                <!-- Dropdown Menu for Home -->
                <div class="nav-dropdown">
                    <a href="index.php" class="dropdown-toggle">
                        <?php echo isset($L['home']) ? $L['home'] : 'Utama'; ?> &#9662;
                    </a>
                    <div class="dropdown-menu">
                        <a href="index.php#about-us">
                            <?php echo ($lang === 'en') ? 'Organizational Profile' : 'Profil Organisasi'; ?>
                        </a>
                        <a href="index.php#dept-services">
                            <?php echo ($lang === 'en') ? 'Services & Scope' : 'Perkhidmatan & Peranan'; ?>
                        </a>
                    </div>
                </div>

                <a href="index.php#announcements"><?php echo ($lang === 'en') ? 'Notices' : 'Pengumuman'; ?></a>
                <a href="index.php#hotlines"><?php echo ($lang === 'en') ? 'Contacts' : 'Hubungi Kami'; ?></a>
            <?php endif; ?>
            
            <!-- User Controls -->
            <?php if (isset($_SESSION['role'])): 
                $safeRole = htmlspecialchars($_SESSION['role'], ENT_QUOTES, 'UTF-8');
                $dashboard_file = ($_SESSION['role'] === 'admin') ? 'dashboard_admin.php' : 'dashboard_staf.php';
            ?>
                <a href="<?php echo $dashboard_file; ?>" class="btn-dashboard">
                    <?php echo isset($L['dashboard']) ? $L['dashboard'] : 'Dashboard'; ?> (<?php echo strtoupper($safeRole); ?>)
                </a>

                <a href="logout.php" class="btn-logout">
                    <?php echo ($lang === 'en') ? 'Logout' : 'Log Keluar'; ?>
                </a>
            <?php else: ?>
                <a href="login.php" class="btn-login">
                    <?php echo ($lang === 'en') ? 'Login' : 'Log Masuk'; ?>
                </a>
            <?php endif; ?>
        </nav>

    </div>
</header>

<!-- Floating Scroll Toggle Button -->
<button id="scrollToggleBtn" aria-label="Scroll Page" title="Scroll Page" style="position: fixed; bottom: 20px; right: 20px; z-index: 999; background: var(--primary-blue); color: var(--accent-gold); border: 2px solid var(--accent-gold); width: 45px; height: 45px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; box-shadow: 0 4px 12px rgba(0,0,0,0.3); transition: transform 0.2s;">
    <span id="scrollArrow">↓</span>
</button>

<!-- Scroll Toggle & Restoration Logic -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const savedPos = localStorage.getItem('ys_lang_scroll_pos');
    if (savedPos !== null) {
        window.scrollTo(0, parseInt(savedPos, 10));
        setTimeout(function() {
            document.documentElement.classList.remove('no-smooth-scroll');
            localStorage.removeItem('ys_lang_scroll_pos');
        }, 100);
    }

    const scrollBtn = document.getElementById('scrollToggleBtn');
    const scrollArrow = document.getElementById('scrollArrow');
    const bottomThreshold = 100;

    function updateScrollState() {
        const scrolledToBottom = (window.innerHeight + window.scrollY) >= (document.documentElement.scrollHeight - bottomThreshold);
        if (scrolledToBottom) {
            scrollArrow.textContent = '↑';
            scrollBtn.setAttribute('title', 'Scroll to Top');
        } else {
            scrollArrow.textContent = '↓';
            scrollBtn.setAttribute('title', 'Scroll to Bottom');
        }
    }

    if (scrollBtn) {
        scrollBtn.addEventListener('click', function() {
            const scrolledToBottom = (window.innerHeight + window.scrollY) >= (document.documentElement.scrollHeight - bottomThreshold);
            if (scrolledToBottom) {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                window.scrollTo({ top: document.documentElement.scrollHeight, behavior: 'smooth' });
            }
        });
    }

    window.addEventListener('scroll', updateScrollState, { passive: true });
    updateScrollState();
});
</script>