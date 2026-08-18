<?php
session_start();
require_once 'config/db.php';
require_once 'lang.php'; // Pengendali bahasa

$lang = isset($lang) ? $lang : 'bm';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contact_type = trim($_POST['contact_type'] ?? 'email');
    $identifier   = trim($_POST['identifier'] ?? '');

    if (empty($identifier)) {
        $error = ($lang === 'en') 
            ? "Please enter your email address or phone number." 
            : "Sila masukkan alamat e-mel atau nombor telefon anda.";
    } else {
        // Semak rekod pengguna mengikut jenis pilihan
        $query = ($contact_type === 'phone') 
            ? "SELECT id, email, phone FROM users WHERE phone = ?" 
            : "SELECT id, email, phone FROM users WHERE email = ?";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $identifier);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            // Jana token selamat & tetapkan masa luput (1 jam)
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Simpan token ke dalam pangkalan data
            $update_stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?");
            $update_stmt->bind_param("ssi", $token, $expires, $user['id']);
            $update_stmt->execute();
            $update_stmt->close();

            // Pautan reset password
            $reset_link = "https://" . $_SERVER['HTTP_HOST'] . "/reset_password.php?token=" . $token;

            if ($contact_type === 'email') {
                // Logik penghantaran e-mel (Gunakan PHPMailer atau mail())
                // mail($user['email'], "Tetapan Semula Kata Laluan", "Klik di sini: " . $reset_link);
            } else {
                // Logik penghantaran SMS / WhatsApp API
            }

            $success = ($lang === 'en')
                ? "Password reset instructions have been sent to your " . ($contact_type === 'phone' ? 'phone' : 'email') . "."
                : "Arahan menetap semula kata laluan telah dihantar ke " . ($contact_type === 'phone' ? 'telefon' : 'e-mel') . " anda.";
        } else {
            // Mesej umum untuk keselamatan (elak peniruan akaun)
            $success = ($lang === 'en')
                ? "If an account matches that information, reset instructions will be sent."
                : "Jika akaun wujud dengan maklumat tersebut, arahan menetap semula kata laluan akan dihantar.";
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
    <title><?php echo ($lang === 'en') ? 'Forgot Password' : 'Lupa Kata Laluan'; ?> - Portal BTMK & KKP</title>
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

        .radio-label {
            display: block;
            font-size: 0.88rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.5rem;
        }

        .radio-group {
            display: flex;
            gap: 20px;
            margin-bottom: 1.2rem;
        }

        .radio-option {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.88rem;
            color: #334155;
            cursor: pointer;
            font-weight: 500;
        }

        .radio-option input[type="radio"] {
            accent-color: var(--primary-blue);
            cursor: pointer;
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

        .back-link {
            text-align: center;
            margin-top: 1.5rem;
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
                <img src="https://upload.wikimedia.org/wikipedia/commons/2/26/Coat_of_arms_of_Malaysia.svg" alt="Jata Negara Malaysia" title="Jata Negara Malaysia">
                <img src="assets/images/yayasan_sabah.png" alt="Logo Kumpulan Yayasan Sabah" class="logo-ys-center" title="Kumpulan Yayasan Sabah" onerror="this.onerror=null; this.src='assets/images/logo-ys.png';">
                <img src="https://upload.wikimedia.org/wikipedia/commons/6/68/Coat_of_arms_of_Sabah.svg" alt="Jata Negeri Sabah (Sabah Maju Jaya)" title="Jata Negeri Sabah - Sabah Maju Jaya">
            </div>

            <div class="brand-header">
                <h2><?php echo ($lang === 'en') ? 'RESET PASSWORD' : 'LUPA KATA LALUAN'; ?></h2>
                <p>Portal BTMK & KKP</p>
            </div>
        </div>

        <!-- 2. Middle Border Pattern -->
        <div class="sabah-pattern-border"></div>

        <div class="login-body">
            <?php if (!empty($error)): ?>
                <div class="alert-error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert-success"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <form action="forgot_password.php" method="POST">
                <span class="radio-label">
                    <?php echo ($lang === 'en') ? 'Reset method:' : 'Kaedah tetapan semula:'; ?>
                </span>
                
                <div class="radio-group">
                    <label class="radio-option">
                        <input type="radio" name="contact_type" value="email" checked onclick="toggleInput('email')">
                        <?php echo ($lang === 'en') ? 'Email' : 'E-mel'; ?>
                    </label>
                    <label class="radio-option">
                        <input type="radio" name="contact_type" value="phone" onclick="toggleInput('phone')">
                        <?php echo ($lang === 'en') ? 'Phone Number' : 'Nombor Telefon'; ?>
                    </label>
                </div>

                <div class="form-group">
                    <label id="identifier-label" for="identifier">
                        <?php echo ($lang === 'en') ? 'Email Address' : 'Alamat E-mel'; ?>
                    </label>
                    <input type="text" id="identifier" name="identifier" required placeholder="e.g. user@sabah.gov.my">
                </div>

                <button type="submit" class="btn-submit">
                    <?php echo ($lang === 'en') ? 'Send Reset Link' : 'Hantar Pautan Tetapan'; ?>
                </button>
            </form>

            <a href="login.php" class="back-link">&larr; <?php echo ($lang === 'en') ? 'Back to Login' : 'Kembali ke Log Masuk'; ?></a>
        </div>

        <!-- 3. Bottom Border Pattern -->
        <div class="sabah-pattern-border"></div>
    </div>

    <script>
        function toggleInput(type) {
            const label = document.getElementById('identifier-label');
            const input = document.getElementById('identifier');
            if (type === 'phone') {
                label.textContent = "<?php echo ($lang === 'en') ? 'Phone Number' : 'Nombor Telefon'; ?>";
                input.placeholder = "e.g. 0123456789";
            } else {
                label.textContent = "<?php echo ($lang === 'en') ? 'Email Address' : 'Alamat E-mel'; ?>";
                input.placeholder = "e.g. user@sabah.gov.my";
            }
        }
    </script>
</body>
</html>