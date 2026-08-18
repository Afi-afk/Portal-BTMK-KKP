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

// Flash messages
$message = '';
$message_type = '';

// Padam Pengguna (Delete Action)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_user') {
    $user_id = intval($_POST['user_id'] ?? 0);
    
    // Elak admin padam akaun sendiri
    if (isset($_SESSION['user_id']) && intval($_SESSION['user_id']) === $user_id) {
        $message = ($lang === 'en') ? 'You cannot delete your own account!' : 'Anda tidak boleh memadam akaun anda sendiri!';
        $message_type = 'danger';
    } else if ($user_id > 0 && isset($conn) && $conn) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $user_id);
            if ($stmt->execute()) {
                $message = ($lang === 'en') ? 'User deleted successfully.' : 'Pengguna berjaya dipadam.';
                $message_type = 'success';
            } else {
                $message = ($lang === 'en') ? 'Failed to delete user.' : 'Gagal memadam pengguna.';
                $message_type = 'danger';
            }
            $stmt->close();
        }
    }
}

// Kemaskini Peranan / Role Update Action (ENUM: admin, btmk, kkp, staff)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_role') {
    $user_id = intval($_POST['user_id'] ?? 0);
    $new_role = trim($_POST['role'] ?? '');

    $allowed_roles = ['admin', 'btmk', 'kkp', 'staff'];
    if (in_array($new_role, $allowed_roles) && $user_id > 0 && isset($conn) && $conn) {
        $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("si", $new_role, $user_id);
            if ($stmt->execute()) {
                $message = ($lang === 'en') ? 'User role updated successfully.' : 'Peranan pengguna berjaya dikemaskini.';
                $message_type = 'success';
            } else {
                $message = ($lang === 'en') ? 'Failed to update user role.' : 'Gagal mengemaskini peranan pengguna.';
                $message_type = 'danger';
            }
            $stmt->close();
        }
    }
}

// Dapatkan Senarai Pengguna & Pengiraan Stat
$users = [];
$total_users = 0;
$total_admins = 0;
$total_support = 0; // BTMK + KKP
$total_staff = 0;   // User biasa pembuat aduan

if (isset($conn) && $conn) {
    $result = $conn->query("SELECT id, username, full_name, email, role, created_at FROM users ORDER BY id DESC");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
            $total_users++;
            if ($row['role'] === 'admin') {
                $total_admins++;
            } elseif ($row['role'] === 'btmk' || $row['role'] === 'kkp') {
                $total_support++;
            } elseif ($row['role'] === 'staff') {
                $total_staff++;
            }
        }
    }
}

$hide_public_nav = true;
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($lang, ENT_QUOTES, 'UTF-8'); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ($lang === 'en') ? 'User Management' : 'Pengurusan Pengguna'; ?> - BTMK & KKP</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        :root {
            --primary-blue: #002B49;
            --accent-gold: #D4AF37;
            --admin-red: #a91e2c;
            --bg-light: #f4f6f9;
            --text-dark: #2c3e50;
            --success-green: #28a745;
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
        
        /* Banner Header Admin & Butang Kembali */
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
            gap: 1rem;
        }
        .hero-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
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
            color: #000;
            padding: 0.35rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Stat Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: #ffffff;
            padding: 1.25rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border-top: 4px solid var(--primary-blue);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .stat-card.gold { border-top-color: var(--accent-gold); }
        .stat-card.blue { border-top-color: var(--info-blue); }
        .stat-card.green { border-top-color: var(--success-green); }
        .stat-info h3 {
            margin: 0;
            font-size: 2rem;
            color: #222;
            line-height: 1;
        }
        .stat-info p {
            margin: 8px 0 0 0;
            color: #666;
            font-size: 0.85rem;
            font-weight: 600;
        }

        /* Alert Notifications */
        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            font-weight: 600;
            font-size: 0.9rem;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Content & Table Styling */
        .content-card {
            background: #ffffff;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            margin-bottom: 2rem;
        }
        .card-header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #eee;
            padding-bottom: 1rem;
            margin-bottom: 1.25rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .card-header-actions h2 {
            margin: 0;
            color: var(--primary-blue);
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-add {
            background: var(--primary-blue);
            color: #ffffff;
            padding: 0.55rem 1rem;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 600;
            transition: background 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }
        .btn-add:hover {
            background: #001a2d;
        }

        .table-responsive {
            overflow-x: auto;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.9rem;
        }
        .data-table th, .data-table td {
            padding: 0.85rem 1rem;
            border-bottom: 1px solid #e9ecef;
        }
        .data-table th {
            background-color: #f8f9fa;
            color: var(--primary-blue);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.78rem;
            letter-spacing: 0.5px;
        }
        .data-table tr:hover {
            background-color: #f1f5f9;
        }

        /* Role Badges Styling */
        .role-badge {
            padding: 0.25rem 0.6rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: bold;
            text-transform: uppercase;
            display: inline-block;
        }
        .role-admin { background: #fbe7e8; color: var(--admin-red); }
        .role-btmk  { background: #e2f0fd; color: #0056b3; }
        .role-kkp   { background: #fff3cd; color: #856404; }
        .role-staff { background: #e2e8f0; color: #475569; }

        /* Form Controls */
        .role-select {
            padding: 0.3rem 0.5rem;
            font-size: 0.82rem;
            border-radius: 4px;
            border: 1px solid #ced4da;
            background-color: #fff;
        }
        .btn-action-save {
            background: var(--primary-blue);
            color: #fff;
            border: none;
            padding: 0.3rem 0.6rem;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8rem;
        }
        .btn-action-delete {
            background: var(--admin-red);
            color: #fff;
            border: none;
            padding: 0.35rem 0.65rem;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .btn-action-delete:hover {
            background: #841722;
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

    <?php include 'includes/navbar.php'; ?>

    <div class="dashboard-container">
        
        <!-- Header Banner -->
        <div class="admin-hero">
            <div>
                <h1 style="margin: 0 0 0.4rem 0;">
                    <?php echo ($lang === 'en') ? 'User Management' : 'Pengurusan Akaun Pengguna'; ?>
                </h1>
                <p style="margin:0; opacity:0.9;">
                    <?php echo ($lang === 'en') ? 'Manage user accounts, BTMK/KKP support staff, and system admins' : 'Urus akaun pengadu (staff), staf sokongan (BTMK/KKP) dan pentadbir sistem'; ?>
                </p>
            </div>
            <div class="hero-actions">
                <a href="dashboard_admin.php" class="btn-back">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                    <span><?php echo ($lang === 'en') ? 'Back' : 'Kembali'; ?></span>
                </a>
                <span class="admin-badge"><?php echo ($lang === 'en') ? 'ADMIN MODE' : 'MOD PENTADBIR'; ?></span>
            </div>
        </div>

        <!-- System Overview Metrics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-info">
                    <h3><?php echo $total_users; ?></h3>
                    <p><?php echo ($lang === 'en') ? 'Total Accounts' : 'Jumlah Keseluruhan'; ?></p>
                </div>
            </div>

            <div class="stat-card gold">
                <div class="stat-info">
                    <h3><?php echo $total_admins; ?></h3>
                    <p><?php echo ($lang === 'en') ? 'Administrators' : 'Pentadbir (Admin)'; ?></p>
                </div>
            </div>

            <div class="stat-card blue">
                <div class="stat-info">
                    <h3><?php echo $total_support; ?></h3>
                    <p><?php echo ($lang === 'en') ? 'Support Staff (BTMK & KKP)' : 'Staf Sokongan (BTMK & KKP)'; ?></p>
                </div>
            </div>

            <div class="stat-card green">
                <div class="stat-info">
                    <h3><?php echo $total_staff; ?></h3>
                    <p><?php echo ($lang === 'en') ? 'Users / Complainants' : 'Pengguna'; ?></p>
                </div>
            </div>
        </div>

        <!-- Flash Message Notification -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <!-- Table Card -->
        <div class="content-card">
            <div class="card-header-actions">
                <h2>
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <span><?php echo ($lang === 'en') ? 'Registered System Users' : 'Senarai Pengguna Berdaftar'; ?></span>
                </h2>
                <a href="admin_register_user.php" class="btn-add">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    <span><?php echo ($lang === 'en') ? 'Register New User' : 'Daftar Pengguna Baru'; ?></span>
                </a>
            </div>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th><?php echo ($lang === 'en') ? 'Full Name / Username' : 'Nama Penuh / Nama Pengguna'; ?></th>
                            <th><?php echo ($lang === 'en') ? 'Email Address' : 'E-mel'; ?></th>
                            <th><?php echo ($lang === 'en') ? 'Role Description' : 'Kategori Peranan'; ?></th>
                            <th><?php echo ($lang === 'en') ? 'Change Role' : 'Tukar Peranan'; ?></th>
                            <th><?php echo ($lang === 'en') ? 'Actions' : 'Tindakan'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $index => $u): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($u['full_name'] ?: $u['username'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                        <br>
                                        <small style="color: #6c757d;">@<?php echo htmlspecialchars($u['username'], ENT_QUOTES, 'UTF-8'); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($u['email'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>
                                        <?php 
                                            $role_class = 'role-staff';
                                            $role_label = 'STAFF (Pengguna)';

                                            if ($u['role'] === 'admin') {
                                                $role_class = 'role-admin';
                                                $role_label = 'ADMIN (Pentadbir)';
                                            } elseif ($u['role'] === 'btmk') {
                                                $role_class = 'role-btmk';
                                                $role_label = 'STAF BTMK (ICT)';
                                            } elseif ($u['role'] === 'kkp') {
                                                $role_class = 'role-kkp';
                                                $role_label = 'PEGAWAI KKP';
                                            }
                                        ?>
                                        <span class="role-badge <?php echo $role_class; ?>">
                                            <?php echo $role_label; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST" action="" style="display: flex; gap: 0.4rem; align-items: center;">
                                            <input type="hidden" name="action" value="update_role">
                                            <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                            <select name="role" class="role-select">
                                                <option value="staff" <?php echo ($u['role'] === 'staff') ? 'selected' : ''; ?>>Staff (Pengguna Biasa)</option>
                                                <option value="btmk" <?php echo ($u['role'] === 'btmk') ? 'selected' : ''; ?>>Staf BTMK (Sokongan ICT)</option>
                                                <option value="kkp" <?php echo ($u['role'] === 'kkp') ? 'selected' : ''; ?>>Pegawai KKP (Sokongan KKP)</option>
                                                <option value="admin" <?php echo ($u['role'] === 'admin') ? 'selected' : ''; ?>>Admin (Pentadbir)</option>
                                            </select>
                                            <button type="submit" class="btn-action-save" title="Simpan Peranan">
                                                <?php echo ($lang === 'en') ? 'Save' : 'Simpan'; ?>
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <?php if (isset($_SESSION['user_id']) && intval($_SESSION['user_id']) !== intval($u['id'])): ?>
                                            <form method="POST" action="" onsubmit="return confirm('<?php echo ($lang === 'en') ? 'Are you sure you want to delete this user?' : 'Adakah anda pasti ingin memadam pengguna ini?'; ?>');" style="display:inline;">
                                                <input type="hidden" name="action" value="delete_user">
                                                <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                                <button type="submit" class="btn-action-delete">
                                                    <?php echo ($lang === 'en') ? 'Delete' : 'Padam'; ?>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <small style="color: #999; font-style: italic;"><?php echo ($lang === 'en') ? 'Current User' : 'Akaun Semasa'; ?></small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: #777; padding: 2rem;">
                                    <?php echo ($lang === 'en') ? 'No users found.' : 'Tiada pengguna ditemui.'; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Sabah Pattern Divider Bottom -->
    <div class="sabah-pattern-border"></div>

    <!-- Footer -->
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