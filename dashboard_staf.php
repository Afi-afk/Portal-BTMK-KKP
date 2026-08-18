<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security Check: Only logged-in staff/users can access
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

// If admin visits staff dashboard, redirect to admin area
if ($_SESSION['role'] === 'admin') {
    header("Location: dashboard_admin.php");
    exit();
}

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/lang.php';

$lang = isset($lang) ? $lang : 'bm';
$user_id = $_SESSION['user_id'];
$full_name = htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username'], ENT_QUOTES, 'UTF-8');

// Fetch user-specific stats
$ict_tickets_count = 0;
$hazard_reports_count = 0;

if (isset($conn) && $conn) {
    // Count active ICT support tickets for user
    $stmt_ict = $conn->prepare("SELECT COUNT(*) FROM tickets WHERE user_id = ?");
    if ($stmt_ict) {
        $stmt_ict->bind_param("i", $user_id);
        $stmt_ict->execute();
        $stmt_ict->bind_result($ict_tickets_count);
        $stmt_ict->fetch();
        $stmt_ict->close();
    }

    // Count OSH/KKP hazard reports submitted by user (Menggunakan jadual 'hazard_reports')
    $stmt_hazard = $conn->prepare("SELECT COUNT(*) FROM hazard_reports WHERE user_id = ?");
    if ($stmt_hazard) {
        $stmt_hazard->bind_param("i", $user_id);
        $stmt_hazard->execute();
        $stmt_hazard->bind_result($hazard_reports_count);
        $stmt_hazard->fetch();
        $stmt_hazard->close();
    }

    $hide_public_nav = true; 
}
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($lang, ENT_QUOTES, 'UTF-8'); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ($lang === 'en') ? 'Staff Dashboard' : 'Dashboard Warga YS'; ?> - BTMK & KKP</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        :root {
            --primary-blue: #002B49;
            --accent-gold: #D4AF37;
            --bg-light: #f4f6f9;
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
        .welcome-card {
            background: linear-gradient(135deg, var(--primary-blue), #001a2d);
            color: #ffffff;
            padding: 2rem;
            border-radius: 10px;
            border-left: 6px solid var(--accent-gold);
            margin-bottom: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .welcome-card h1 {
            margin: 0 0 0.5rem 0;
            font-size: 1.8rem;
        }
        .welcome-card p {
            margin: 0;
            color: #e0e6ed;
            font-size: 0.95rem;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
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
        .stat-card.gold { border-top-color: var(--accent-gold); }
        .stat-info h3 {
            margin: 0;
            font-size: 2rem;
            color: var(--primary-blue);
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
        .stat-card.gold .stat-icon {
            background: rgba(212, 175, 55, 0.15);
            color: #b8931d;
        }
        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        .panel-card {
            background: #ffffff;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .panel-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-bottom: 2px solid #eee;
            padding-bottom: 0.75rem;
            margin-bottom: 1.2rem;
        }
        .panel-header svg {
            width: 22px;
            height: 22px;
            color: var(--primary-blue);
            flex-shrink: 0;
        }
        .panel-header h3 {
            margin: 0;
            color: var(--primary-blue);
            font-size: 1.15rem;
            font-weight: 700;
        }
        .action-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.85rem;
            margin-bottom: 0.75rem;
            background: #f8f9fa;
            color: #333;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.92rem;
            transition: all 0.2s ease;
            border: 1px solid #e9ecef;
        }
        .action-link-content {
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }
        .action-link-content svg {
            width: 16px;
            height: 16px;
            color: #6c757d;
            transition: color 0.2s;
        }
        .action-link:hover {
            background: var(--primary-blue);
            color: #ffffff;
            border-color: var(--primary-blue);
        }
        .action-link:hover .action-link-content svg,
        .action-link:hover .arrow-icon {
            color: #ffffff;
        }
        .action-link.secondary {
            background: #f8f9fa;
        }
        .arrow-icon {
            width: 16px;
            height: 16px;
            color: #adb5bd;
            transition: transform 0.2s, color 0.2s;
        }
        .action-link:hover .arrow-icon {
            transform: translateX(3px);
        }
    </style>
</head>
<body>

    <!-- Shared Top Navbar & Language Bar -->
    <?php include 'includes/navbar.php'; ?>

    <div class="dashboard-container">
        <!-- Welcome Hero Banner -->
        <div class="welcome-card">
            <h1><?php echo ($lang === 'en') ? 'Welcome' : 'Selamat Datang'; ?>, <?php echo $full_name; ?></h1>
            <p><?php echo ($lang === 'en') ? 'Portal Services Panel for Kumpulan Yayasan Sabah Staff' : 'Panel Perkhidmatan Portal Warga Kumpulan Yayasan Sabah'; ?></p>
        </div>

        <!-- Personal Summary Metrics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-info">
                    <h3><?php echo $ict_tickets_count; ?></h3>
                    <p><?php echo ($lang === 'en') ? 'ICT Supports' : 'Bantuan/Aduan ICT'; ?></p>
                </div>
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                </div>
            </div>

            <div class="stat-card gold">
                <div class="stat-info">
                    <h3><?php echo $hazard_reports_count; ?></h3>
                    <p><?php echo ($lang === 'en') ? 'OSH Hazard Reports' : 'Laporan Insiden KKP'; ?></p>
                </div>
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                </div>
            </div>
        </div>

        <!-- Quick Action Panels -->
        <div class="actions-grid">
            <!-- ICT Support Panel -->
            <div class="panel-card">
                <div class="panel-header">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                    <h3><?php echo ($lang === 'en') ? 'ICT Helpdesk Services' : 'Perkhidmatan Helpdesk ICT'; ?></h3>
                </div>

                <a href="ticket_create.php" class="action-link">
                    <div class="action-link-content">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span><?php echo ($lang === 'en') ? 'Submit New ICT Support/Help' : 'Buka Bantuan/Aduan ICT Baru'; ?></span>
                    </div>
                    <svg class="arrow-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </a>

                <a href="ticket_list.php" class="action-link secondary">
                    <div class="action-link-content">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                        <span><?php echo ($lang === 'en') ? 'View Support Form History' : 'Semak Status Bantuan/Aduan Saya'; ?></span>
                    </div>
                    <svg class="arrow-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </a>
            </div>

            <!-- OSH/KKP Incident Panel -->
            <div class="panel-card">
                <div class="panel-header">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    <h3><?php echo ($lang === 'en') ? 'Occupational Safety & Health' : 'Keselamatan & Kesihatan Pekerjaan'; ?></h3>
                </div>

                <a href="kkp_report_create.php" class="action-link">
                    <div class="action-link-content">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span><?php echo ($lang === 'en') ? 'Report Workplace Hazard' : 'Hantar Laporan Insiden'; ?></span>
                    </div>
                    <svg class="arrow-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </a>

                <a href="kkp_report_list.php" class="action-link secondary">
                    <div class="action-link-content">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                        <span><?php echo ($lang === 'en') ? 'My OSH Reports' : 'Rekod Laporan KKP Saya'; ?></span>
                    </div>
                    <svg class="arrow-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </a>
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
                    echo 'Information Technology &amp; Communication Division (BTMK) | Occupational Safety &amp; Health Unit (OSH)';
                } else {
                    echo 'Bahagian Teknologi Maklumat &amp; Komunikasi (BTMK) | Unit Keselamatan &amp; Kesihatan Pekerjaan (KKP)';
                }
            ?>
        </small>
    </div>

</body>
</html>