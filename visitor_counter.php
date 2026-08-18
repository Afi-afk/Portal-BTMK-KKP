<?php
// 1. Pastikan sambungan database wujud
if (!isset($conn) || ($conn instanceof mysqli && $conn->connect_error)) {
    $db_host = 'localhost';
    $db_user = 'root';
    $db_pass = '';
    $db_name = 'ys_portal_db';

    $conn = @new mysqli($db_host, $db_user, $db_pass, $db_name);
}

// Set nilai lalai
$totalVisits = 0;
$todayVisits = 0;

if (isset($conn) && !$conn->connect_error) {
    // 2. Dapatkan IP Pelawat & Tarikh
    $user_ip    = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $ip_address = hash('sha256', $user_ip); // Hash IP untuk privasi (64 aksara)
    $today      = date('Y-m-d');

    // 3. Masukkan atau kemaskini rekod pelawat
    $log_stmt = $conn->prepare("
        INSERT INTO visitor_logs (ip_address, visit_date, last_visit) 
        VALUES (?, ?, NOW()) 
        ON DUPLICATE KEY UPDATE last_visit = NOW()
    ");

    if ($log_stmt) {
        // DIBAIKI: Tambah koma antara $ip_address dan $today
        $log_stmt->bind_param("ss", $ip_address, $today);
        
        // Tangkap jika berlaku ralat semasa eksekusi SQL
        if (!$log_stmt->execute()) {
            error_log("Visitor Counter Execute Error: " . $log_stmt->error);
        }
        $log_stmt->close();
    } else {
        error_log("Visitor Counter Prepare Error: " . $conn->error);
    }

    // 4. Dapatkan Jumlah Keseluruhan Pelawat (Unique Daily Visits)
    $total_res = $conn->query("SELECT COUNT(*) AS total FROM visitor_logs");
    if ($total_res) {
        $row = $total_res->fetch_assoc();
        $totalVisits = (int)($row['total'] ?? 0);
    }

    // 5. Dapatkan Jumlah Pelawat Hari Ini
    $today_stmt = $conn->prepare("SELECT COUNT(*) AS today_count FROM visitor_logs WHERE visit_date = ?");
    if ($today_stmt) {
        $today_stmt->bind_param("s", $today);
        $today_stmt->execute();
        $result = $today_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $todayVisits = (int)($row['today_count'] ?? 0);
        }
        $today_stmt->close();
    }
}
?>