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

// Metric Statistics for OSH/KKP Incident Reports
$total_reports = 0;
$pending_reports = 0;
$resolved_reports = 0;

if (isset($conn) && $conn) {
    // Total KKP reports
    $res_total = $conn->query("SELECT COUNT(*) FROM hazard_reports");
    if ($res_total) { list($total_reports) = $res_total->fetch_row(); }

    // Pending/In Progress reports
    $res_pending = $conn->query("SELECT COUNT(*) FROM hazard_reports WHERE status = 'pending' OR status = 'in_progress'");
    if ($res_pending) { list($pending_reports) = $res_pending->fetch_row(); }

    // Resolved/Completed reports
    $res_resolved = $conn->query("SELECT COUNT(*) FROM hazard_reports WHERE status = 'resolved' OR status = 'completed' OR status = 'closed'");
    if ($res_resolved) { list($resolved_reports) = $res_resolved->fetch_row(); }

    $hide_public_nav = true;
}
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($lang, ENT_QUOTES, 'UTF-8'); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ($lang === 'en') ? 'OSH Incident Reports Management' : 'Pengurusan Laporan Insiden KKP'; ?> - BTMK</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        :root {
            --primary-blue: #002B49;
            --accent-gold: #D4AF37;
            --admin-red: #a91e2c;
            --bg-light: #f4f6f9;
            --text-dark: #2c3e50;
            --success-green: #28a745;
            --warning-orange: #ffc107;
            --info-blue: #17a2b8;
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
        
        /* Admin Hero Banner Styling */
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
            flex-wrap: wrap;
            gap: 15px;
        }
        .hero-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 600;
            border: 1px solid rgba(255, 255, 255, 0.25);
            transition: all 0.2s ease;
        }
        .btn-back:hover {
            background: var(--accent-gold);
            color: #000000;
            border-color: var(--accent-gold);
        }
        .admin-badge {
            background: var(--accent-gold);
            color: #000000;
            padding: 0.35rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        /* Metric Cards Grid */
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
        .stat-card.warning { border-top-color: var(--warning-orange); }
        .stat-card.success { border-top-color: var(--success-green); }

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
            background: rgba(255, 193, 7, 0.15);
            color: #d39e00;
        }
        .stat-card.success .stat-icon {
            background: rgba(40, 167, 69, 0.15);
            color: var(--success-green);
        }

        /* Content Table Card Container */
        .content-card {
            background: #ffffff;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            margin-bottom: 2rem;
        }
        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #eee;
            padding-bottom: 0.75rem;
            margin-bottom: 1.25rem;
            flex-wrap: wrap;
            gap: 10px;
        }
        .card-header-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .card-header-title svg {
            width: 22px;
            height: 22px;
            color: var(--primary-blue);
        }
        .card-header h3 {
            margin: 0;
            color: var(--primary-blue);
            font-size: 1.1rem;
            font-weight: 700;
        }

        /* Custom Table Styling */
        .table-responsive {
            overflow-x: auto;
        }
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.92rem;
        }
        .custom-table th {
            background-color: #f8f9fa;
            color: var(--primary-blue);
            text-align: left;
            padding: 12px;
            border-bottom: 2px solid #e9ecef;
            font-weight: 700;
        }
        .custom-table td {
            padding: 12px;
            border-bottom: 1px solid #e9ecef;
            color: #333;
        }
        .custom-table tr:hover {
            background-color: #f8f9fa;
        }

        /* Status Badges */
        .badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.78rem;
            font-weight: 700;
            display: inline-block;
        }
        .badge-pending { background-color: #fff3cd; color: #856404; }
        .badge-progress { background-color: #cce5ff; color: #004085; }
        .badge-resolved { background-color: #d4edda; color: #155724; }

        /* Footer Styling */
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

    <!-- Shared Top Navbar -->
    <?php 
    $navbar_path = __DIR__ . '/includes/navbar.php';
    if (file_exists($navbar_path)) {
        include_once $navbar_path;
    }
    ?>

    <div class="dashboard-container">
        <!-- Header Banner & Navigation Back Button -->
        <div class="admin-hero">
            <div>
                <h1 style="margin: 0 0 0.4rem 0;">
                    <?php echo ($lang === 'en') ? 'OSH Incident Reports' : 'Pengurusan Laporan Insiden KKP'; ?>
                </h1>
                <p style="margin:0; opacity:0.9;">
                    <?php echo ($lang === 'en') ? 'Monitor, review, and manage reported safety incidents and hazard cases' : 'Pantau, semak, dan urus laporan insiden keselamatan serta bahaya daripada warga kerja'; ?>
                </p>
            </div>
            <div class="hero-actions">
                <a href="dashboard_admin.php" class="btn-back">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    <span><?php echo ($lang === 'en') ? 'Back to Dashboard' : 'Kembali'; ?></span>
                </a>
                <span class="admin-badge"><?php echo ($lang === 'en') ? 'ADMIN MODE' : 'MOD PENTADBIR'; ?></span>
            </div>
        </div>

        <!-- Metric Cards Grid -->
        <div class="stats-grid">
            <!-- Total Reports -->
            <div class="stat-card">
                <div class="stat-info">
                    <h3><?php echo $total_reports; ?></h3>
                    <p><?php echo ($lang === 'en') ? 'Total Incident Reports' : 'Jumlah Laporan Insiden'; ?></p>
                </div>
               
            </div>

            <!-- Pending Reports -->
            <div class="stat-card warning">
                <div class="stat-info">
                    <h3><?php echo $pending_reports; ?></h3>
                    <p><?php echo ($lang === 'en') ? 'Pending Action' : 'Menunggu Tindakan'; ?></p>
                </div>
                
            </div>

            <!-- Resolved Reports -->
            <div class="stat-card success">
                <div class="stat-info">
                    <h3><?php echo $resolved_reports; ?></h3>
                    <p><?php echo ($lang === 'en') ? 'Resolved / Closed' : 'Selesai / Ditutup'; ?></p>
                </div>
                
            </div>
        </div>

        <!-- Main Content Card (Reports Table) -->
        <div class="content-card">
            <div class="card-header">
                <div class="card-header-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    <h3><?php echo ($lang === 'en') ? 'List of Incident Reports' : 'Senarai Laporan Insiden KKP'; ?></h3>
                </div>
            </div>

            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th># ID</th>
                            <th><?php echo ($lang === 'en') ? 'Reporter' : 'Pelapor'; ?></th>
                            <th><?php echo ($lang === 'en') ? 'Incident Type / Location' : 'Jenis Insiden / Lokasi'; ?></th>
                            <th><?php echo ($lang === 'en') ? 'Date Reported' : 'Tarikh Dilapor'; ?></th>
                            <th><?php echo ($lang === 'en') ? 'Status' : 'Status'; ?></th>
                            <th><?php echo ($lang === 'en') ? 'Action' : 'Tindakan'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (isset($conn) && $conn) {
                            $sql = "SELECT * FROM hazard_reports ORDER BY created_at DESC LIMIT 15";
                            $result = $conn->query($sql);

                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $status = strtolower($row['status'] ?? 'pending');
                                    
                                    // Map status to css badge
                                    $badge_class = 'badge-pending';
                                    if ($status === 'in_progress') {
                                        $badge_class = 'badge-progress';
                                    } elseif ($status === 'resolved' || $status === 'completed' || $status === 'closed') {
                                        $badge_class = 'badge-resolved';
                                    }

                                    $reporter_name = htmlspecialchars($row['reporter_name'] ?? $row['user_name'] ?? 'Aplikasi Staff', ENT_QUOTES, 'UTF-8');
                                    $title = htmlspecialchars($row['title'] ?? $row['incident_type'] ?? 'Aduan KKP', ENT_QUOTES, 'UTF-8');
                                    $location = htmlspecialchars($row['location'] ?? '-', ENT_QUOTES, 'UTF-8');

                                    echo "<tr>";
                                    echo "<td>#" . htmlspecialchars($row['id'] ?? '') . "</td>";
                                    echo "<td>" . $reporter_name . "</td>";
                                    echo "<td><strong>" . $title . "</strong><br><small style='color:#666;'>" . $location . "</small></td>";
                                    echo "<td>" . date('d/m/Y', strtotime($row['created_at'] ?? 'now')) . "</td>";
                                    echo "<td><span class='badge {$badge_class}'>" . strtoupper(str_replace('_', ' ', $status)) . "</span></td>";
                                    echo "<td>
                                            <a href='admin_view_hazard_report.php?id=" . ($row['id'] ?? '') . "' style='color: var(--primary-blue); font-weight: bold; text-decoration: none;'>Tinjau</a>
                                          </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' style='text-align: center; color: #777; padding: 20px;'>" . (($lang === 'en') ? 'No OSH incident reports found.' : 'Tiada rekod laporan insiden KKP dijumpai.') . "</td></tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sabah Pattern Border Bottom -->
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