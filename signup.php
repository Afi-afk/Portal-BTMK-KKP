<?php
session_start();

// Jika pengguna sudah log masuk, lencongkan terus ke papan pemuka
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: dashboard_admin.php");
    } else {
        header("Location: dashboard_staf.php");
    }
    exit();
}

require_once 'config/db.php';
require_once 'lang.php'; // Include language handler

$lang = isset($lang) ? $lang : 'bm';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name        = trim($_POST['full_name']);
    $username         = trim($_POST['username']);
    $password         = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Semakan ruang kosong
    if (empty($full_name) || empty($username) || empty($password) || empty($confirm_password)) {
        $error = ($lang === 'en') 
            ? "Please fill in all fields." 
            : "Sila isi semua ruang.";
    } elseif ($password !== $confirm_password) {
        $error = ($lang === 'en') 
            ? "Passwords do not match." 
            : "Kata laluan tidak padan.";
    } elseif (strlen($password) < 6) {
        $error = ($lang === 'en') 
            ? "Password must be at least 6 characters long." 
            : "Kata laluan mestilah sekurang-kurangnya 6 aksara.";
    } else {
        // Semak sama ada nama pengguna sudah wujud
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt_check->bind_param("s", $username);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $error = ($lang === 'en') 
                ? "Username already exists. Please choose another." 
                : "Nama pengguna sudah wujud. Sila pilih nama lain.";
        } else {
            // Hash kata laluan untuk keselamatan
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $default_role = 'staf'; // Peranan lalai untuk pendaftaran baru

            // Masukkan pengguna baru ke dalam pangkalan data
            $stmt_insert = $conn->prepare("INSERT INTO users (full_name, username, password, role) VALUES (?, ?, ?, ?)");
            $stmt_insert->bind_param("ssss", $full_name, $username, $hashed_password, $default_role);

            if ($stmt_insert->execute()) {
                $success = ($lang === 'en') 
                    ? "Registration successful! You can now log in." 
                    : "Pendaftaran berjaya! Anda kini boleh log masuk.";
            } else {
                $error = ($lang === 'en') 
                    ? "Registration failed. Please try again later." 
                    : "Pendaftaran gagal. Sila cuba lagi kemudian.";
            }
            $stmt_insert->close();
        }
        $stmt_check->close();
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($lang, ENT_QUOTES, 'UTF-8'); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ($lang === 'en') ? 'Sign Up' : 'Daftar Akaun'; ?> - Portal BTMK & KKP</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        :root {
            --primary-blue: #002B49;
            --accent-gold: #D4AF37;
            --hover-blue: #001f35;
            --bg-light: #f4f6f9;
            --border-color: #cbd5e1;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--primary-blue);
            background-image: 
                linear-gradient(135deg, rgba(0, 43, 73, 0.85) 0%, rgba(0, 31, 53, 0.92) 100%),
                url('assets/images/menara_tun_mustapha.jpg'),
                url('https://upload.wikimedia.org/wikipedia/commons/thumb/8/87/Menara_Tun_Mustapha_01.jpg/1200px-Menara_Tun_Mustapha_01.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 1.5rem;
            box-sizing: border-box;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(8px);
            border-radius: 16px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 460px;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .sabah-pattern-border {
            height: 18px;
            width: 100%;
            background-color: var(--primary-blue);
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="32" height="18" viewBox="0 0 32 18"><path d="M16 0 L32 9 L16 18 L0 9 Z" fill="%23C8102E"/><path d="M16 3 L27 9 L16 15 L5 9 Z" fill="%23D4AF37"/><path d="M16 6 L22 9 L16 12 L10 9 Z" fill="%23002B49"/><circle cx="16" cy="9" r="1.5" fill="%23ffffff"/></svg>');
            background-repeat: repeat-x;
            background-size: 32px 18px;
            border-top: 1px solid var(--accent-gold);
            border-bottom: 1px solid var(--accent-gold);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 2;
        }

        .login-header-banner {
            background: #ffffff;
            padding: 2rem 1.5rem 1.5rem;
            text-align: center;
            position: relative;
        }

        .crests-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 14px;
            margin: 0 auto 1.2rem auto;
            padding: 0;
            width: 100%;
            box-sizing: border-box;
        }

        .crests-wrapper img {
            height: 50px;
            width: 50px;
            object-fit: contain;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.12));
            transition: transform 0.2s ease;
        }

        .crests-wrapper img:hover {
            transform: scale(1.08);
        }

        .logo-ys-center {
            height: 54px !important;
            width: 54px !important;
        }

        .brand-header h2 {
            margin: 0;
            color: var(--primary-blue);
            font-size: 1.35rem;
            font-weight: 800;
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }

        .brand-header p {
            margin: 6px 0 0;
            color: #b45309;
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .login-body {
            padding: 2rem 2rem 2.2rem;
        }

        .form-group {
            margin-bottom: 1.1rem;
        }

        .form-group label {
            display: block;
            color: #334155;
            font-size: 0.88rem;
            font-weight: 600;
            margin-bottom: 0.4rem;
        }

        .form-group input {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 0.95rem;
            background-color: #f8fafc;
            transition: all 0.2s ease-in-out;
        }

        .form-group input:focus {
            border-color: var(--primary-blue);
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(0, 43, 73, 0.15);
            outline: none;
        }

        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, var(--primary-blue) 0%, #001f35 100%);
            color: #ffffff;
            border: 1px solid var(--accent-gold);
            padding: 0.85rem;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            font-weight: 700;
            letter-spacing: 0.5px;
            transition: all 0.2s ease-in-out;
            margin-top: 0.6rem;
            box-shadow: 0 4px 12px rgba(0, 43, 73, 0.25);
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #003a63 0%, var(--primary-blue) 100%);
            border-color: #f59e0b;
            transform: translateY(-1px);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .alert-error {
            background-color: #fef2f2;
            color: #991b1b;
            padding: 0.8rem 1rem;
            border-radius: 8px;
            margin-bottom: 1.2rem;
            font-size: 0.85rem;
            border: 1px solid #fecaca;
            text-align: center;
            font-weight: 600;
        }

        .alert-success {
            background-color: #f0fdf4;
            color: #166534;
            padding: 0.8rem 1rem;
            border-radius: 8px;
            margin-bottom: 1.2rem;
            font-size: 0.85rem;
            border: 1px solid #bbf7d0;
            text-align: center;
            font-weight: 600;
        }

        .login-link-wrapper {
            text-align: center;
            margin-top: 1.2rem;
            font-size: 0.88rem;
            color: #64748b;
        }

        .login-link {
            color: #2563eb;
            text-decoration: none;
            font-weight: 700;
            transition: color 0.2s;
        }

        .login-link:hover {
            color: var(--primary-blue);
            text-decoration: underline;
        }

        .back-link {
            text-align: center;
            margin-top: 1.2rem;
            display: block;
            color: #64748b;
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 600;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: var(--primary-blue);
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <!-- 1. Top Border Pattern -->
        <div class="sabah-pattern-border"></div>

        <!-- Portal Header Banner -->
        <div class="login-header-banner">
            <div class="crests-wrapper">
                <!-- 1. Jata Negara Malaysia -->
                <img src="https://upload.wikimedia.org/wikipedia/commons/2/26/Coat_of_arms_of_Malaysia.svg" alt="Jata Negara Malaysia" title="Jata Negara Malaysia">
                
                <!-- 2. Logo Yayasan Sabah -->
                <img src="assets/images/yayasan_sabah.png" alt="Logo Kumpulan Yayasan Sabah" class="logo-ys-center" title="Kumpulan Yayasan Sabah" onerror="this.onerror=null; this.src='assets/images/logo-ys.png';">
                
                <!-- 3. Jata Negeri Sabah / Sabah Maju Jaya -->
                <img src="https://upload.wikimedia.org/wikipedia/commons/6/68/Coat_of_arms_of_Sabah.svg" alt="Jata Negeri Sabah (Sabah Maju Jaya)" title="Jata Negeri Sabah - Sabah Maju Jaya">
            </div>

            <div class="brand-header">
                <h2>PORTAL BTMK & KKP</h2>
                <p>Kumpulan Yayasan Sabah</p>
            </div>
        </div>

        <!-- 2. Pattern Below Kumpulan Yayasan Sabah -->
        <div class="sabah-pattern-border"></div>

        <div class="login-body">
            <?php if (!empty($error)): ?>
                <div class="alert-error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert-success">
                    <?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?><br>
                    <a href="login.php" class="login-link" style="display:inline-block; margin-top:8px;">
                        <?php echo ($lang === 'en') ? 'Click here to Log In' : 'Klik di sini untuk Log Masuk'; ?>
                    </a>
                </div>
            <?php else: ?>

                <form action="signup.php" method="POST">
                    <div class="form-group">
                        <label for="full_name"><?php echo ($lang === 'en') ? 'Full Name' : 'Nama Penuh'; ?></label>
                        <input type="text" id="full_name" name="full_name" required placeholder="<?php echo ($lang === 'en') ? 'Enter full name' : 'Masukkan nama penuh'; ?>" value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="username"><?php echo ($lang === 'en') ? 'Username' : 'Nama Pengguna'; ?></label>
                        <input type="text" id="username" name="username" required placeholder="<?php echo ($lang === 'en') ? 'Enter username' : 'Masukkan nama pengguna'; ?>" autocomplete="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="password"><?php echo ($lang === 'en') ? 'Password' : 'Kata Laluan'; ?></label>
                        <input type="password" id="password" name="password" required placeholder="<?php echo ($lang === 'en') ? 'Enter password (min. 6 characters)' : 'Masukkan kata laluan (min. 6 aksara)'; ?>" autocomplete="new-password">
                    </div>

                    <div class="form-group">
                        <label for="confirm_password"><?php echo ($lang === 'en') ? 'Confirm Password' : 'Sahkan Kata Laluan'; ?></label>
                        <input type="password" id="confirm_password" name="confirm_password" required placeholder="<?php echo ($lang === 'en') ? 'Re-enter password' : 'Masukkan semula kata laluan'; ?>" autocomplete="new-password">
                    </div>

                    <button type="submit" class="btn-submit"><?php echo ($lang === 'en') ? 'Sign Up' : 'Daftar Akaun'; ?></button>
                </form>

                <div class="login-link-wrapper">
                    <?php echo ($lang === 'en') ? 'Already have an account?' : 'Sudah mempunyai akaun?'; ?> 
                    <a href="login.php" class="login-link"><?php echo ($lang === 'en') ? 'Log In' : 'Log Masuk'; ?></a>
                </div>

            <?php endif; ?>

            <a href="index.php" class="back-link">&larr; <?php echo ($lang === 'en') ? 'Back to Main Portal' : 'Kembali ke Halaman Utama'; ?></a>
        </div>

        <!-- 3. Bottom Border Pattern -->
        <div class="sabah-pattern-border"></div>
    </div>

</body>
</html>