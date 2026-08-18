<?php
session_start();

// Redirect logged-in users straight to their dashboard
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = ($lang === 'en') 
            ? "Please fill in all fields." 
            : "Sila isi semua ruang.";
    } else {
        // Prepare statement to prevent SQL Injection
        $stmt = $conn->prepare("SELECT id, username, password, role, full_name FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            // Verify hashed password
            if (password_verify($password, $user['password'])) {
                // Secure session creation
                session_regenerate_id(true);
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['username']  = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role']      = $user['role'];

                // Role-based redirection
                if ($user['role'] === 'admin') {
                    header("Location: dashboard_admin.php");
                } else {
                    header("Location: dashboard_staf.php");
                }
                exit();
            } else {
                $error = ($lang === 'en') ? "Invalid password." : "Kata laluan tidak sah.";
            }
        } else {
            $error = ($lang === 'en') ? "User not found." : "Pengguna tidak wujud.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($lang, ENT_QUOTES, 'UTF-8'); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ($lang === 'en') ? 'Log In' : 'Log Masuk'; ?> - Portal BTMK & KKP</title>
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
            margin-bottom: 1.25rem;
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

        .forgot-link-wrapper {
            text-align: right;
            margin-top: 0.4rem;
        }

        .forgot-link {
            font-size: 0.8rem;
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }

        .forgot-link:hover {
            color: var(--primary-blue);
            text-decoration: underline;
        }

        /* BAHAGIAN PETA DAFTAR AKAUN (SIGN UP) */
        .signup-link-wrapper {
            text-align: center;
            margin-top: 1.2rem;
            font-size: 0.88rem;
            color: #64748b;
        }

        .signup-link {
            color: #2563eb;
            text-decoration: none;
            font-weight: 700;
            transition: color 0.2s;
        }

        .signup-link:hover {
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

            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="username"><?php echo ($lang === 'en') ? 'Username' : 'Nama Pengguna'; ?></label>
                    <input type="text" id="username" name="username" required placeholder="<?php echo ($lang === 'en') ? 'Enter your username' : 'Masukkan nama pengguna'; ?>" autocomplete="username">
                </div>

                <div class="form-group">
                    <label for="password"><?php echo ($lang === 'en') ? 'Password' : 'Kata Laluan'; ?></label>
                    <input type="password" id="password" name="password" required placeholder="<?php echo ($lang === 'en') ? 'Enter your password' : 'Masukkan kata laluan'; ?>" autocomplete="current-password">
                    
                    <div class="forgot-link-wrapper">
                        <a href="forgot_password.php" class="forgot-link"><?php echo ($lang === 'en') ? 'Forgot password?' : 'Lupa kata laluan?'; ?></a>
                    </div>
                </div>

                <button type="submit" class="btn-submit"><?php echo ($lang === 'en') ? 'Log In' : 'Log Masuk'; ?></button>
            </form>

            <!-- BAHAGIAN DAFTAR AKAUN BARU -->
            <div class="signup-link-wrapper">
                <?php echo ($lang === 'en') ? "Don't have an account?" : "Belum mempunyai akaun?"; ?> 
                <a href="signup.php" class="signup-link"><?php echo ($lang === 'en') ? 'Sign Up' : 'Daftar Akaun'; ?></a>
            </div>

            <a href="index.php" class="back-link">&larr; <?php echo ($lang === 'en') ? 'Back to Main Portal' : 'Kembali ke Halaman Utama'; ?></a>
        </div>

        <!-- 3. Bottom Border Pattern -->
        <div class="sabah-pattern-border"></div>
    </div>

</body>
</html>