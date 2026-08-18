<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security Check: Strict Admin Only Access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/lang.php';

$lang = isset($lang) ? $lang : 'bm';
$full_name = htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username'], ENT_QUOTES, 'UTF-8');

// System-wide Aggregates for Admin
$total_users = 0;
$pending_ict_tickets = 0;
$pending_kkp_reports = 0;

if (isset($conn) && $conn) {
    // Total registered users
    $res_users = $conn->query("SELECT COUNT(*) FROM users");
    if ($res_users) { list($total_users) = $res_users->fetch_row(); }

    // Open/Pending ICT tickets
    $res_ict = $conn->query("SELECT COUNT(*) FROM tickets WHERE status = 'pending' OR status = 'open'");
    if ($res_ict) { list($pending_ict_tickets) = $res_ict->fetch_row(); }

    // Pending OSH hazard reports
    $res_kkp = $conn->query("SELECT COUNT(*) FROM hazard_reports WHERE status = 'pending'");
    if ($res_kkp) { list($pending_kkp_reports) = $res_kkp->fetch_row(); }

    $hide_public_nav = true; 
}
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($lang, ENT_QUOTES, 'UTF-8'); ?>">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ($lang === 'en') ? 'Admin Control Centre' : 'Pusat Kawalan Pentadbir'; ?> - BTMK & KKP</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        
        
        :root {
            --primary-blue: #002B49;
            --accent-gold: #D4AF37;
            --admin-red: #a91e2c;
            --bg-light: #f4f6f9;
            --text-dark: #2c3e50;
        }
        body {
            background-color: var(--bg-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
        }
        .dashboard-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }
        .admin-hero {
            background: linear-gradient(135deg, #001a2d, var(--primary-blue));
            color: #ffffff;
            padding: 2rem;
            border-radius: 10px;
            border-left: 6px solid var(--accent-gold);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.12);
        }
        .admin-badge {
            background: var(--accent-gold);
            color: #000;
            padding: 0.35rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: #ffffff;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border-top: 4px solid var(--primary-blue);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .stat-card.warning { border-top-color: var(--accent-gold); }
        .stat-card.danger { border-top-color: var(--admin-red); }
        .stat-info h3 {
            margin: 0;
            font-size: 2.2rem;
            color: #222;
            line-height: 1;
        }
        .stat-info p {
            margin: 8px 0 0 0;
            color: #666;
            font-size: 0.88rem;
            font-weight: 600;
        }
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 43, 73, 0.08);
            color: var(--primary-blue);
            flex-shrink: 0;
        }
        .stat-card.warning .stat-icon {
            background: rgba(212, 175, 55, 0.15);
            color: #b8931d;
        }
        .stat-card.danger .stat-icon {
            background: rgba(169, 30, 44, 0.1);
            color: var(--admin-red);
        }
        .admin-modules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 1.5rem;
        }
        .module-card {
            background: #ffffff;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .module-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-bottom: 2px solid #eee;
            padding-bottom: 0.75rem;
            margin-bottom: 1rem;
        }
        .module-header svg {
            width: 22px;
            height: 22px;
            color: var(--primary-blue);
            flex-shrink: 0;
        }
        .module-header h3 {
            margin: 0;
            color: var(--primary-blue);
            font-size: 1.1rem;
            font-weight: 700;
        }
        .admin-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.85rem;
            margin-bottom: 0.6rem;
            background: #f8f9fa;
            color: #333;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.92rem;
            transition: all 0.2s ease;
            border: 1px solid #e9ecef;
        }
        .admin-link-content {
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }
        .admin-link-content svg {
            width: 16px;
            height: 16px;
            color: #6c757d;
            transition: color 0.2s;
        }
        .admin-link:hover {
            background: var(--primary-blue);
            color: #ffffff;
            border-color: var(--primary-blue);
        }
        .admin-link:hover .admin-link-content svg,
        .admin-link:hover .arrow-icon {
            color: #ffffff;
        }
        .arrow-icon {
            width: 16px;
            height: 16px;
            color: #adb5bd;
            transition: transform 0.2s, color 0.2s;
        }
        .admin-link:hover .arrow-icon {
            transform: translateX(3px);
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
</head>
<body>

    <!-- Shared Top Navbar & Language Bar -->
    <?php include 'includes/navbar.php'; ?>

    <div class="dashboard-container">
        <!-- Admin Header -->
        <div class="admin-hero">
            <div>
                <h1 style="margin: 0 0 0.4rem 0;"><?php echo ($lang === 'en') ? 'Admin Control Centre' : 'Pusat Kawalan Pentadbir'; ?></h1>
                <p style="margin:0; opacity:0.9;"><?php echo ($lang === 'en') ? 'System Oversight & Management Panel' : 'Panel Pengurusan & Pengawasan Sistem BTMK & KKP'; ?></p>
            </div>
            <span class="admin-badge"><?php echo ($lang === 'en') ? 'ADMIN MODE' : 'MOD PENTADBIR'; ?></span>
        </div>

        <!-- System Overview Metrics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-info">
                    <h3><?php echo $total_users; ?></h3>
                    <p><?php echo ($lang === 'en') ? 'Registered Users' : 'Jumlah Pengguna Berdaftar'; ?></p>
                </div>
    
            </div>

            <div class="stat-card warning">
                <div class="stat-info">
                    <h3><?php echo $pending_ict_tickets; ?></h3>
                    <p><?php echo ($lang === 'en') ? 'Pending ICT Support' : 'Bantuan/Aduan ICT Menunggu Tindakan'; ?></p>
                </div>
                
            </div>

            <div class="stat-card danger">
                <div class="stat-info">
                    <h3><?php echo $pending_kkp_reports; ?></h3>
                    <p><?php echo ($lang === 'en') ? 'Pending OSH Incident Reports' : 'Laporan Hazad KKP Belum Selesai'; ?></p>
                </div>
                
            </div>
        </div>

        <!-- Admin Management Modules -->
        <div class="admin-modules-grid">
            <!-- User & Security Management -->
            <div class="module-card">
                <div class="module-header">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
                    <h3><?php echo ($lang === 'en') ? 'User Management' : 'Pengurusan Pengguna'; ?></h3>
                </div>
                <a href="admin_users.php" class="admin-link">
                    <div class="admin-link-content">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                        <span><?php echo ($lang === 'en') ? 'Manage User Accounts' : 'Urus Akaun Pengguna'; ?></span>
                    </div>
                    <svg class="arrow-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </a>
                <a href="admin_register_user.php" class="admin-link">
                    <div class="admin-link-content">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
                        <span><?php echo ($lang === 'en') ? 'Register New Staff / Admin' : 'Daftar Staf / Pentadbir Baru'; ?></span>
                    </div>
                    <svg class="arrow-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </a>
            </div>

            <!-- BTMK ICT Management -->
            <div class="module-card">
                <div class="module-header">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                    <h3><?php echo ($lang === 'en') ? 'ICT Operations & Tickets' : 'Pengurusan Helpdesk ICT'; ?></h3>
                </div>
                <a href="admin_ict_reports.php" class="admin-link">
                    <div class="admin-link-content">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg>
                        <span><?php echo ($lang === 'en') ? 'Manage All ICT Support Tickets' : 'Urus Semua Bantuan/Aduan ICT'; ?></span>
                    </div>
                    <svg class="arrow-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </a>
                <a href="admin_ict_notices.php" class="admin-link">
                    <div class="admin-link-content">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 17H2a3 3 0 0 0 3-3V9a7 7 0 0 1 14 0v5a3 3 0 0 0 3 3zm-8 4a2 2 0 0 1-4 0"></path></svg>
                        <span><?php echo ($lang === 'en') ? 'Publish ICT Announcements' : 'Siar Pengumuman ICT'; ?></span>
                    </div>
                    <svg class="arrow-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </a>
            </div>
<!-- KKP OSH Management -->
            <div class="module-card">
                <div class="module-header">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    <h3><?php echo ($lang === 'en') ? 'OSH & Hazard Audits' : 'Pengurusan Aduan & Audit KKP'; ?></h3>
                </div>
                <a href="admin_kkp_reports.php" class="admin-link">
                    <div class="admin-link-content">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                        <span><?php echo ($lang === 'en') ? 'Review OSH Hazard Reports' : 'Semak Laporan Hazad / Insiden'; ?></span>
                    </div>
                    <svg class="arrow-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </a>
                <a href="admin_kkp_notices.php" class="admin-link">
                    <div class="admin-link-content">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                        <span><?php echo ($lang === 'en') ? 'Publish Safety Bulletins' : 'Siar Buletin Keselamatan KKP'; ?></span>
                    </div>
                    <svg class="arrow-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </a>
            </div>

            <!-- KIV: Modul Organization & System Settings
            <div class="module-card">
                <div class="module-header">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                    <h3><?php echo ($lang === 'en') ? 'Organization & System Info' : 'Pengurusan Maklumat & Portal'; ?></h3>
                </div>
                <a href="admin_organization.php" class="admin-link">
                    <div class="admin-link-content">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16"></path><path d="M1 21h22"></path><path d="M9 7h1"></path><path d="M9 11h1"></path><path d="M14 7h1"></path><path d="M14 11h1"></path></svg>
                        <span><?php echo ($lang === 'en') ? 'Update Organization Profile' : 'Kemaskini Profil Organisasi'; ?></span>
                    </div>
                    <svg class="arrow-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </a>
                <a href="admin_emergency_contacts.php" class="admin-link">
                    <div class="admin-link-content">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                        <span><?php echo ($lang === 'en') ? 'Manage Emergency Contacts' : 'Kemaskini Talian Kecemasan'; ?></span>
                    </div>
                    <svg class="arrow-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </a>
            </div>
            -->

        </div> <!-- Penutup untuk grid / container modul (sangat penting) -->
    </div> <!-- Penutup untuk main container (sangat penting) -->

    <!-- Sabah Pattern Divider Bottom -->
    <div class="sabah-pattern-border"></div>

    <!-- Public Footer -->
    <footer class="footer" style="width: 100%; clear: both;">
        &copy; <?php echo date('Y'); ?> 
        <?php 
            if (isset($lang) && $lang === 'en') {
                echo 'Yayasan Sabah Group. All Rights Reserved.';
            } else {
                echo 'Kumpulan Yayasan Sabah. Hak Cipta Terpelihara.';
            }
        ?> 
        <br>
        <small style="opacity: 0.8; display: block; margin-top: 4px;">
            <?php 
                if (isset($lang) && $lang === 'en') {
                    echo 'Information Technology &amp; Communication Division (BTMK) | Occupational Safety &amp; Health Unit (OSH)';
                } else {
                    echo 'Bahagian Teknologi Maklumat &amp; Komunikasi (BTMK) | Unit Keselamatan &amp; Kesihatan Pekerjaan (KKP)';
                }
            ?>
        </small>
    </footer>

</body>
</html>