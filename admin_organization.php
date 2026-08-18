<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security Check: Only logged-in admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/lang.php';

$lang = isset($lang) ? $lang : 'bm';
$success_msg = '';
$error_msg = '';

$upload_dir = 'uploads/';
$chart_image_path = 'uploads/org_chart.png';

// -------------------------------------------------------------
// 1. Dapatkan Nilai Semasa dari Pangkalan Data
// -------------------------------------------------------------
$settings = [];
if (isset($conn) && $conn) {
    $result = $conn->query("SELECT setting_key, setting_value FROM site_settings");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
}

// Nilai lalai sekiranya pangkalan data belum diisi
$btmk_vision_bm  = $settings['btmk_vision_bm']  ?? 'Menjadi pemangkin transformasi digital yang cemerlang, selamat, dan berdaya saing bagi Kumpulan Yayasan Sabah.';
$btmk_mission_bm = $settings['btmk_mission_bm'] ?? 'Menyediakan perkhidmatan ICT yang cekap, infrastruktur rangkaian moden, serta perlindungan keselamatan siber.';
$kkp_vision_bm   = $settings['kkp_vision_bm']   ?? 'Mewujudkan persekitaran kerja yang selamat, sihat, dan bebas daripada kemalangan.';
$kkp_mission_bm  = $settings['kkp_mission_bm']  ?? 'Memastikan pematuhan KKP, melaksanakan penilaian HIRARC, serta membudayakan amalan keselamatan.';


// -------------------------------------------------------------
// 2. Proses Kemaskini Borang (POST)
// -------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // A. Kemaskini Teks Visi & Misi
    if (isset($_POST['action']) && $_POST['action'] === 'update_profile') {
        $new_btmk_vision  = trim($_POST['btmk_vision_bm'] ?? '');
        $new_btmk_mission = trim($_POST['btmk_mission_bm'] ?? '');
        $new_kkp_vision   = trim($_POST['kkp_vision_bm'] ?? '');
        $new_kkp_mission  = trim($_POST['kkp_mission_bm'] ?? '');

        if (isset($conn) && $conn) {
            $stmt = $conn->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
            
            $data_to_save = [
                'btmk_vision_bm'  => $new_btmk_vision,
                'btmk_mission_bm' => $new_btmk_mission,
                'kkp_vision_bm'   => $new_kkp_vision,
                'kkp_mission_bm'  => $new_kkp_mission,
            ];

            foreach ($data_to_save as $key => $val) {
                $stmt->bind_param("sss", $key, $val, $val);
                $stmt->execute();
            }
            $stmt->close();

            // Kemaskini pembolehubah tempatan untuk paparan terus
            $btmk_vision_bm  = $new_btmk_vision;
            $btmk_mission_bm = $new_btmk_mission;
            $kkp_vision_bm   = $new_kkp_vision;
            $kkp_mission_bm  = $new_kkp_mission;

            $success_msg = ($lang === 'en') ? 'Organizational profile updated successfully!' : 'Profil organisasi berjaya dikemaskini!';
        } else {
            $error_msg = ($lang === 'en') ? 'Database connection error.' : 'Ralat sambungan pangkalan data.';
        }
    }

    // B. Muat Naik Gambar Carta Organisasi
    if (isset($_POST['action']) && $_POST['action'] === 'upload_chart') {
        if (isset($_FILES['org_chart_image']) && $_FILES['org_chart_image']['error'] === UPLOAD_ERR_OK) {
            $file_tmp  = $_FILES['org_chart_image']['tmp_name'];
            $file_name = $_FILES['org_chart_image']['name'];
            $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($file_ext, $allowed_exts)) {
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                $destination = $upload_dir . 'org_chart.png';

                if (move_uploaded_file($file_tmp, $destination)) {
                    $success_msg = ($lang === 'en') ? 'Organization chart image uploaded successfully!' : 'Gambar carta organisasi berjaya dimuat naik!';
                } else {
                    $error_msg = ($lang === 'en') ? 'Failed to move uploaded file.' : 'Gagal memindahkan fail yang dimuat naik.';
                }
            } else {
                $error_msg = ($lang === 'en') ? 'Invalid file format. Only JPG, PNG, and WEBP allowed.' : 'Format fail tidak sah. Hanya JPG, PNG, dan WEBP dibenarkan.';
            }
        }
    }
}

$display_image = file_exists($chart_image_path) ? $chart_image_path . '?v=' . time() : 'assets/images/org_chart_placeholder.png';
$hide_public_nav = true;
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($lang, ENT_QUOTES, 'UTF-8'); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ($lang === 'en') ? 'Manage Organization Profile' : 'Pengurusan Profil Organisasi'; ?> - Admin Panel</title>
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
            color: #1e293b;
        }
        .admin-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            background: #ffffff;
            padding: 1.25rem 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border-left: 5px solid var(--primary-blue);
        }
        .page-header h1 {
            margin: 0;
            font-size: 1.5rem;
            color: var(--primary-blue);
        }
        .alert {
            padding: 1rem 1.25rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
            font-weight: 500;
        }
        .alert-success { background-color: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; }
        .alert-danger { background-color: #f8d7da; color: #842029; border: 1px solid #f5c2c7; }

        .profile-section {
            background: #ffffff;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
        }
        .slide-content {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 1.5rem;
        }
        @media (max-width: 992px) {
            .slide-content { grid-template-columns: 1fr; }
        }
        
        .info-panel {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .info-box {
            padding: 1.2rem;
            border-radius: 8px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 4px solid var(--primary-blue);
        }
        .info-box.kkp-theme { border-left-color: var(--accent-gold); }
        .info-box-title {
            margin: 0 0 0.5rem 0;
            font-size: 1rem;
            font-weight: 700;
            color: var(--primary-blue);
        }
        .info-box p {
            margin: 0 0 0.5rem 0;
            font-size: 0.85rem;
            line-height: 1.4;
            color: #475569;
        }

        .chart-panel {
            background: #fafafa;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1.5rem;
            text-align: center;
        }
        .chart-title {
            margin: 0 0 1rem 0;
            font-size: 1.05rem;
            color: var(--primary-blue);
            font-weight: 700;
        }
        .org-chart-img-wrapper img {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
        }

        .admin-controls-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }
        @media (max-width: 768px) {
            .admin-controls-grid { grid-template-columns: 1fr; }
        }
        .admin-card {
            background: #ffffff;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
        }
        .admin-card h3 {
            margin-top: 0;
            color: var(--primary-blue);
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 0.5rem;
        }
        .btn-primary {
            background-color: var(--primary-blue);
            color: white;
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-primary:hover { background-color: #001a2d; }
        .file-input-box {
            border: 2px dashed #cbd5e1;
            padding: 1.5rem;
            text-align: center;
            border-radius: 6px;
            background: #f8fafc;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

    <?php include 'includes/navbar.php'; ?>

    <div class="admin-container">
        
        <div class="page-header">
            <div>
                <h1><?php echo ($lang === 'en') ? 'Manage Organization Profile' : 'Pengurusan Profil Organisasi'; ?></h1>
                <small style="color: #64748b;"><?php echo ($lang === 'en') ? 'Upload org chart image &amp; manage text' : 'Muat naik imej carta organisasi &amp; urus maklumat'; ?></small>
            </div>
            <a href="dashboard_admin.php" class="btn-primary" style="text-decoration: none; font-size: 0.85rem;">
                &larr; <?php echo ($lang === 'en') ? 'Back to Dashboard' : 'Kembali ke Dashboard'; ?>
            </a>
        </div>

        <?php if ($success_msg): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success_msg, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <?php if ($error_msg): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error_msg, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <!-- LIVE PREVIEW SECTION -->
        <div class="profile-section">
            <h2 style="margin:0 0 0.5rem 0; font-size:1.4rem; color:var(--primary-blue);">
                <?php echo ($lang === 'en') ? 'Organizational Profile (Live Preview)' : 'Profil Organisasi (Pratonton Langsung)'; ?>
            </h2>
            
            <div class="slide-content" style="margin-top: 1.5rem;">
                
                <!-- Left Column: Dynamic Info Boxes -->
                <div class="info-panel">
                    <div class="info-box">
                        <h5 class="info-box-title">Bahagian Teknologi Maklumat &amp; Komunikasi (BTMK)</h5>
                        <p><strong>Visi:</strong> <?php echo htmlspecialchars($btmk_vision_bm, ENT_QUOTES, 'UTF-8'); ?></p>
                        <p><strong>Misi:</strong> <?php echo htmlspecialchars($btmk_mission_bm, ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>

                    <div class="info-box kkp-theme">
                        <h5 class="info-box-title">Keselamatan &amp; Kesihatan Pekerjaan (KKP)</h5>
                        <p><strong>Visi:</strong> <?php echo htmlspecialchars($kkp_vision_bm, ENT_QUOTES, 'UTF-8'); ?></p>
                        <p><strong>Misi:</strong> <?php echo htmlspecialchars($kkp_mission_bm, ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                </div>

                <!-- Right Column: Chart Image -->
                <div class="chart-panel">
                    <h4 class="chart-title"><?php echo ($lang === 'en') ? 'Organizational Chart' : 'Carta Organisasi'; ?></h4>
                    <div class="org-chart-img-wrapper">
                        <img src="<?php echo htmlspecialchars($display_image, ENT_QUOTES, 'UTF-8'); ?>" alt="Carta Organisasi">
                    </div>
                </div>

            </div>
        </div>

        <!-- ADMIN CONTROLS GRID -->
        <div class="admin-controls-grid">
            
            <!-- 1. Upload Org Chart Image -->
            <div class="admin-card">
                <h3><?php echo ($lang === 'en') ? '1. Upload Org Chart Image' : '1. Muat Naik Imej Carta Organisasi'; ?></h3>
                <form action="admin_organization.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="upload_chart">
                    <div class="file-input-box">
                        <input type="file" name="org_chart_image" accept="image/png, image/jpeg, image/webp" required style="width: 100%;">
                    </div>
                    <button type="submit" class="btn-primary" style="width: 100%;">
                        <?php echo ($lang === 'en') ? 'Upload Image' : 'Muat Naik Gambar'; ?>
                    </button>
                </form>
            </div>

            <!-- 2. Edit Profile Text -->
            <div class="admin-card">
                <h3><?php echo ($lang === 'en') ? '2. Edit Profile Information' : '2. Kemaskini Maklumat Profil'; ?></h3>
                <form action="admin_organization.php" method="POST">
                    <input type="hidden" name="action" value="update_profile">

                    <div style="margin-bottom: 0.8rem;">
                        <label style="font-size:0.82rem; font-weight:700; color:var(--primary-blue); display:block; margin-bottom:4px;">BTMK - Visi (BM):</label>
                        <textarea name="btmk_vision_bm" style="width:100%; height:45px; padding:8px; border:1px solid #cbd5e1; border-radius:4px; box-sizing:border-box;"><?php echo htmlspecialchars($btmk_vision_bm, ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>

                    <div style="margin-bottom: 0.8rem;">
                        <label style="font-size:0.82rem; font-weight:700; color:var(--primary-blue); display:block; margin-bottom:4px;">BTMK - Misi (BM):</label>
                        <textarea name="btmk_mission_bm" style="width:100%; height:45px; padding:8px; border:1px solid #cbd5e1; border-radius:4px; box-sizing:border-box;"><?php echo htmlspecialchars($btmk_mission_bm, ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>

                    <div style="margin-bottom: 0.8rem;">
                        <label style="font-size:0.82rem; font-weight:700; color:#b8931d; display:block; margin-bottom:4px;">KKP - Visi (BM):</label>
                        <textarea name="kkp_vision_bm" style="width:100%; height:45px; padding:8px; border:1px solid #cbd5e1; border-radius:4px; box-sizing:border-box;"><?php echo htmlspecialchars($kkp_vision_bm, ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label style="font-size:0.82rem; font-weight:700; color:#b8931d; display:block; margin-bottom:4px;">KKP - Misi (BM):</label>
                        <textarea name="kkp_mission_bm" style="width:100%; height:45px; padding:8px; border:1px solid #cbd5e1; border-radius:4px; box-sizing:border-box;"><?php echo htmlspecialchars($kkp_mission_bm, ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>

                    <button type="submit" class="btn-primary" style="width: 100%;">
                        <?php echo ($lang === 'en') ? 'Save Text Changes' : 'Simpan Perubahan Teks'; ?>
                    </button>
                </form>
            </div>

        </div>

    </div>

    <div class="sabah-pattern-border" style="margin-top: 3rem;"></div>
    <div class="footer">&copy; <?php echo date('Y'); ?> Kumpulan Yayasan Sabah.</div>

</body>
</html>