<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kawalan Capaian: Hanya Admin dibenarkan
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/lang.php';

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil dan bersihkan data borang
    $full_name  = trim($_POST['full_name'] ?? '');
    $username   = trim(strtolower($_POST['username'] ?? ''));
    $email      = trim($_POST['email'] ?? '');
    $password   = $_POST['password'] ?? '';
    $department = trim($_POST['department'] ?? 'Pejabat Pengarah Yayasan Sabah');
    $phone      = trim($_POST['phone'] ?? '');
    $role       = $_POST['role'] ?? 'staff';

    // Pengesahan Format Username
    if (empty($full_name) || empty($username) || empty($email) || empty($password)) {
        $message = ($lang === 'en') ? 'Please fill in all required fields (*).' : 'Sila isi semua medan yang wajib (*).';
        $message_type = 'danger';
    } elseif (!preg_match('/^[a-z0-9._-]{3,30}$/', $username)) {
        $message = ($lang === 'en') 
            ? 'Username must be 3-30 characters long and contain only lowercase letters, numbers, dots, hyphens, or underscores (no spaces).' 
            : 'Nama pengguna mestilah 3-30 aksara dan hanya mengandungi huruf kecil, nombor, titik (.), sempang (-), atau underscore (_) tanpa ruang kosong.';
        $message_type = 'danger';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = ($lang === 'en') ? 'Invalid email format.' : 'Format e-mel tidak sah.';
        $message_type = 'danger';
    } else {
        try {
            // Semak jika username atau email sudah wujud
            $checkStmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $checkStmt->bind_param("ss", $username, $email);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();

            if ($checkResult->num_rows > 0) {
                $message = ($lang === 'en') ? 'Username or Email already exists.' : 'Nama pengguna atau E-mel telah wujud.';
                $message_type = 'danger';
            } else {
                // Enkripsi kata laluan
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                $phone_value = !empty($phone) ? $phone : NULL;

                // Masukkan pengguna baharu
                $insertStmt = $conn->prepare("INSERT INTO users (username, password, full_name, email, department, role, phone, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, 'active', NOW())");
                $insertStmt->bind_param("sssssss", $username, $hashedPassword, $full_name, $email, $department, $role, $phone_value);
                $success = $insertStmt->execute();

                if ($success) {
                    $message = ($lang === 'en') ? 'User registered successfully!' : 'Pengguna berjaya didaftarkan!';
                    $message_type = 'success';
                } else {
                    $message = ($lang === 'en') ? 'Failed to register user.' : 'Gagal mendaftarkan pengguna.';
                    $message_type = 'danger';
                }
                $insertStmt->close();
            }
            $checkStmt->close();
        } catch (Exception $e) {
            $message = ($lang === 'en') ? 'Database Error: ' . $e->getMessage() : 'Ralat Pangkalan Data: ' . $e->getMessage();
            $message_type = 'danger';
        }
    }
}

// Senarai Bahagian Rasmi Menara Tun Mustapha & Pejabat Zon Kumpulan Yayasan Sabah
$departments_list = [
    'Pejabat Pengarah Yayasan Sabah',
    'Bahagian Teknologi Maklumat & Komunikasi (BTMK)',
    'Unit Keselamatan & Kesihatan Pekerjaan (KKP)',
    'Bahagian Pengurusan Sumber Manusia',
    'Bahagian Kewangan, Akaun & Pelaburan',
    'Bahagian Pentadbiran & Fasiliti',
    'Bahagian Undang-Undang & Urus Setia',
    'Bahagian Komunikasi Korporat',
    'Bahagian Pembangunan Pendidikan',
    'Bahagian Konservasi & Alam Sekitar',
    'Bahagian Audit Dalaman',
    'Pejabat Zon Pantai Barat (Kota Kinabalu)',
    'Pejabat Zon Pantai Timur Utara (Sandakan)',
    'Pejabat Zon Pantai Timur Selatan (Tawau)',
    'Pejabat Zon Pedalaman (Keningau)'
];
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($lang); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ($lang === 'en') ? 'Register New User - Admin' : 'Daftar Pengguna Baharu - Admin'; ?></title>
    
    <style>
        :root {
            --primary-blue: #002B49;
            --dark-blue: #001A2D;
            --accent-gold: #D4AF37;
            --light-gold: #f4e8c1;
            --bg-light: #f4f6f9;
            --text-dark: #333333;
            --border-color: #cccccc;
            --shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            --radius: 8px;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .container {
            max-width: 620px;
            width: 100%;
            margin: 40px auto;
            padding: 0 20px;
            box-sizing: border-box;
            flex: 1;
        }

        .card {
            background-color: #ffffff;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border-top: 5px solid var(--accent-gold);
            padding: 35px 30px;
        }

        .card-title {
            color: var(--primary-blue);
            margin-top: 0;
            margin-bottom: 24px;
            font-size: 1.6rem;
            font-weight: 700;
            text-align: center;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 22px;
            font-size: 0.9rem;
            line-height: 1.4;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: var(--primary-blue);
            font-size: 0.9rem;
        }

        .form-control {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 0.95rem;
            font-family: inherit;
            box-sizing: border-box;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            background-color: #fff;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(0, 43, 73, 0.12);
        }

        select.form-control {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml;utf8,<svg fill='%23002B49' height='24' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg'><path d='M7 10l5 5 5-5z'/></svg>");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 18px;
            padding-right: 35px;
            cursor: pointer;
        }

        .field-help {
            font-size: 0.8rem;
            color: #666;
            margin-top: 5px;
            display: block;
        }

        .btn-submit {
            width: 100%;
            background-color: var(--primary-blue);
            color: #ffffff;
            padding: 13px;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease, transform 0.1s ease;
            margin-top: 15px;
        }

        .btn-submit:hover {
            background-color: var(--dark-blue);
        }

        .action-links {
            margin-top: 24px;
            text-align: center;
        }

        .action-links a {
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .sabah-pattern-border {
            height: 6px;
            background: linear-gradient(90deg, var(--primary-blue) 0%, var(--accent-gold) 50%, var(--primary-blue) 100%);
            width: 100%;
        }

        .footer {
            background-color: var(--primary-blue);
            color: #ffffff;
            text-align: center;
            padding: 1.8rem 1.5rem;
            font-size: 0.85rem;
            margin-top: auto;
        }
    </style>
</head>
<body>

    <?php include 'includes/navbar.php'; ?>

    <div class="container">
        <div class="card">
            <h2 class="card-title">
                <?php echo ($lang === 'en') ? 'Register New Staff / User' : 'Pendaftaran Pengguna / Staf Baharu'; ?>
            </h2>

            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form action="admin_register_user.php" method="POST">
                
                <!-- Nama Penuh (full_name) -->
                <div class="form-group">
                    <label for="full_name">
                        <?php echo ($lang === 'en') ? 'Full Name' : 'Nama Penuh'; ?> *
                    </label>
                    <input type="text" id="full_name" name="full_name" class="form-control" required 
                           onkeyup="suggestUsername()"
                           value="<?php echo isset($_POST['full_name']) && $message_type !== 'success' ? htmlspecialchars($_POST['full_name']) : ''; ?>" 
                           placeholder="<?php echo ($lang === 'en') ? 'e.g. Ahmad bin Abdullah' : 'cth: Ahmad bin Abdullah'; ?>">
                </div>

                <!-- Nama Pengguna (username) -->
                <div class="form-group">
                    <label for="username">
                        <?php echo ($lang === 'en') ? 'Username' : 'Nama Pengguna'; ?> *
                    </label>
                    <input type="text" id="username" name="username" class="form-control" required 
                           pattern="[a-z0-9._-]{3,30}"
                           title="Hanya huruf kecil, nombor, titik (.), sempang (-), atau underscore (_) dibenarkan (3-30 aksara)."
                           value="<?php echo isset($_POST['username']) && $message_type !== 'success' ? htmlspecialchars($_POST['username']) : ''; ?>" 
                           placeholder="<?php echo ($lang === 'en') ? 'e.g. ahmad.abdullah' : 'cth: ahmad.abdullah'; ?>">
                    <span class="field-help">
                        <strong>Syarat Username:</strong> Gunakan 3–30 aksara (huruf kecil, nombor, <code>.</code>, <code>_</code>, atau <code>-</code> tanpa ruang kosong).
                    </span>
                </div>

                <!-- E-mel (email) -->
                <div class="form-group">
                    <label for="email">
                        <?php echo ($lang === 'en') ? 'Email Address' : 'Alamat E-mel'; ?> *
                    </label>
                    <input type="email" id="email" name="email" class="form-control" required 
                           value="<?php echo isset($_POST['email']) && $message_type !== 'success' ? htmlspecialchars($_POST['email']) : ''; ?>" 
                           placeholder="cth: pengguna@yayasansabah.org.my">
                </div>

                <!-- Kata Laluan (password) -->
                <div class="form-group">
                    <label for="password">
                        <?php echo ($lang === 'en') ? 'Password' : 'Kata Laluan'; ?> *
                    </label>
                    <input type="password" id="password" name="password" class="form-control" required placeholder="••••••••">
                </div>

                <!-- Jabatan / Bahagian (Pilihan Dropdown) -->
                <div class="form-group">
                    <label for="department">
                        <?php echo ($lang === 'en') ? 'Department / Division' : 'Jabatan / Bahagian'; ?>
                    </label>
                    <select id="department" name="department" class="form-control">
                        <?php foreach ($departments_list as $dept): ?>
                            <option value="<?php echo htmlspecialchars($dept); ?>" 
                                <?php echo (isset($_POST['department']) && $_POST['department'] === $dept) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($dept); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Nombor Telefon (phone) -->
                <div class="form-group">
                    <label for="phone">
                        <?php echo ($lang === 'en') ? 'Phone Number' : 'Nombor Telefon'; ?>
                    </label>
                    <input type="text" id="phone" name="phone" class="form-control" 
                           value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" 
                           placeholder="012-3456789">
                </div>

                <!-- Peranan / Tahap Capaian (role ENUM: admin, btmk, kkp, staff) -->
                <div class="form-group">
                    <label for="role">
                        <?php echo ($lang === 'en') ? 'Role / Access Level' : 'Peranan / Tahap Capaian'; ?> *
                    </label>
                    <select id="role" name="role" class="form-control" required>
                        <option value="staff" <?php echo (isset($_POST['role']) && $_POST['role'] === 'staff') ? 'selected' : ''; ?>>
                            <?php echo ($lang === 'en') ? 'Staff' : 'Staf (Pengguna Biasa)'; ?>
                        </option>
                        <option value="kkp" <?php echo (isset($_POST['role']) && $_POST['role'] === 'kkp') ? 'selected' : ''; ?>>
                            <?php echo ($lang === 'en') ? 'KKP Staff' : 'Staf KKP'; ?>
                        </option>
                        <option value="btmk" <?php echo (isset($_POST['role']) && $_POST['role'] === 'btmk') ? 'selected' : ''; ?>>
                            <?php echo ($lang === 'en') ? 'BTMK Staff' : 'Staf BTMK'; ?>
                        </option>
                        <option value="admin" <?php echo (isset($_POST['role']) && $_POST['role'] === 'admin') ? 'selected' : ''; ?>>
                            <?php echo ($lang === 'en') ? 'Administrator' : 'Pentadbir (Admin)'; ?>
                        </option>
                    </select>
                </div>

                <button type="submit" class="btn-submit">
                    <?php echo ($lang === 'en') ? 'Register User' : 'Daftar Pengguna'; ?>
                </button>
            </form>

            <div class="action-links">
                <a href="dashboard_admin.php">
                    &larr; <?php echo ($lang === 'en') ? 'Back to Admin Dashboard' : 'Kembali ke Dashboard Admin'; ?>
                </a>
            </div>
        </div>
    </div>

    <div class="sabah-pattern-border"></div>

    <footer class="footer">
        &copy; <?php echo date('Y'); ?> 
        <?php echo ($lang === 'en') ? 'Yayasan Sabah Group. All Rights Reserved.' : 'Kumpulan Yayasan Sabah. Hak Cipta Terpelihara.'; ?>
    </footer>

    <script>
        function suggestUsername() {
            const fullNameInput = document.getElementById('full_name').value.trim();
            const usernameInput = document.getElementById('username');
            
            if (fullNameInput.length > 0 && usernameInput.dataset.manual !== 'true') {
                let formatted = fullNameInput.toLowerCase()
                    .replace(/(bin|binti|a\/l|a\/p)\b/g, '')
                    .replace(/[^a-z0-9\s]/g, '')
                    .trim()
                    .replace(/\s+/g, '.');
                
                usernameInput.value = formatted;
            }
        }

        document.getElementById('username').addEventListener('input', function() {
            this.dataset.manual = 'true';
        });
    </script>
</body>
</html>