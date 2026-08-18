<?php
session_start();

// Semak jika borang dihantar melalui POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

// 1. Sambungan Pangkalan Data
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'yayasan_sabah_db';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Sambungan pangkalan data gagal: " . $conn->connect_error);
}

// 2. Bersihkan dan Sahkan Data Input
$raw_incident_type = filter_input(INPUT_POST, 'incident_type', FILTER_SANITIZE_SPECIAL_CHARS);
$location          = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_SPECIAL_CHARS);
$description       = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS);

if (empty($raw_incident_type) || empty($location) || empty($description)) {
    $_SESSION['flash_error'] = "Sila isi semua ruangan yang wajib.";
    header("Location: index.php?status=error");
    exit();
}

// Tukar pemetaan jenis insiden kepada Bahasa Melayu 100%
$incident_labels = [
    'near_miss'        => 'Hampir Kemalangan',
    'unsafe_condition' => 'Keadaan Tidak Selamat',
    'unsafe_act'       => 'Tingkah Laku Tidak Selamat',
    'accident'         => 'Kemalangan Tempat Kerja'
];

// Jika borang menghantar kunci 'near_miss', tukar ke BM. Jika borang sudah hantar 'Hampir Kemalangan', guna terus nilai tersebut.
$incident_type = isset($incident_labels[$raw_incident_type]) ? $incident_labels[$raw_incident_type] : $raw_incident_type;

// 3. Memproses Muat Naik Fail/Gambar
$attachment_path = null;

if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['attachment']['tmp_name'];
    $fileName    = $_FILES['attachment']['name'];
    $fileSize    = $_FILES['attachment']['size'];

    $fileNameCmps  = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));

    // Format yang dibenarkan & had saiz (Maksimum 5MB)
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
    $maxFileSize       = 5 * 1024 * 1024; // 5MB

    if (in_array($fileExtension, $allowedExtensions) && $fileSize <= $maxFileSize) {
        $uploadFileDir = 'uploads/hazards/';
        
        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0755, true);
        }

        $newFileName = 'hazard_' . date('Ymd_His') . '_' . uniqid() . '.' . $fileExtension;
        $dest_path   = $uploadFileDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $attachment_path = $dest_path;
        }
    }
}

// 4. Simpan Rekod ke Dalam Pangkalan Data (Menyimpan $incident_type dalam Bahasa Melayu)
$sql  = "INSERT INTO hazard_reports (incident_type, location, description, attachment_path, created_at) VALUES (?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);

if ($stmt) {
    // $incident_type di sini kini dipastikan dalam Bahasa Melayu
    $stmt->bind_param("ssss", $incident_type, $location, $description, $attachment_path);
    $stmt->execute();
    $report_id = $stmt->insert_id;
    $stmt->close();
} else {
    $report_id = rand(1000, 9999);
}

$conn->close();

// 5. Hantar Emel Peringatan kepada Pegawai KKP
$to_email = "kkp@ys.sabah.gov.my";
$subject  = "[PORTAL KKP] Laporan Hazard / Insiden Baru #HZ-" . $report_id;

$message = "
<html>
<head>
    <title>Laporan Hazard Baru</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8fafc; padding: 20px; }
        .card { background: #ffffff; padding: 20px; border-radius: 8px; border-left: 5px solid #002B49; max-width: 600px; }
        h2 { color: #002B49; margin-top: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        td { padding: 8px; border-bottom: 1px solid #e2e8f0; }
        .label { font-weight: bold; color: #475569; width: 35%; }
    </style>
</head>
<body>
    <div class='card'>
        <h2>Laporan Hazard / Insiden Baru</h2>
        <p>Laporan baharu telah dihantar melalui Portal KKP Yayasan Sabah:</p>
        <table>
            <tr><td class='label'>ID Laporan:</td><td>#HZ-" . $report_id . "</td></tr>
            <tr><td class='label'>Jenis Insiden:</td><td><strong>" . htmlspecialchars($incident_type) . "</strong></td></tr>
            <tr><td class='label'>Lokasi Kejadian:</td><td>" . htmlspecialchars($location) . "</td></tr>
            <tr><td class='label'>Keterangan:</td><td>" . nl2br(htmlspecialchars($description)) . "</td></tr>
            <tr><td class='label'>Tarikh Laporan:</td><td>" . date('d M Y, h:i A') . "</td></tr>
            <tr><td class='label'>Lampiran Gambar:</td><td>" . ($attachment_path ? "<a href='http://localhost/yayasan_sabah/" . $attachment_path . "'>Lihat Gambar</a>" : "Tiada lampiran") . "</td></tr>
        </table>
    </div>
</body>
</html>
";

$headers  = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From: Portal KKP Yayasan Sabah <noreply@ys.sabah.gov.my>" . "\r\n";

@mail($to_email, $subject, $message, $headers);

// 6. Lencongkan Semula dengan Mesej Kejayaan
$_SESSION['flash_success'] = "Laporan Insiden anda telah berjaya dihantar kepada Unit KKP. Terima kasih!";
header("Location: index.php?status=success");
exit();
?>