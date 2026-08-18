<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check session or URL parameter for language (default: 'bm')
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = ($_GET['lang'] === 'en') ? 'en' : 'bm';
} elseif (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'bm';
}

$lang = $_SESSION['lang'];

$txt = [
    'bm' => [
        // Navbar & Utility Bar
        'general_line' => 'Talian Am',
        'email' => 'E-Mel',
        'language' => 'Bahasa',
        'home' => 'Utama',
        'notices' => 'Pemberitahuan',
        'education' => 'Pendidikan',
        'conservation' => 'Konservasi',
        'zones' => 'Zon YS',
        'dashboard' => 'Dashboard',
        'logout' => 'Log Keluar',
        'staff_portal' => 'Portal Warga YS',

        // Ticker & Hero Banner
        'announcement_label' => 'PENGUMUMAN RASMI',
        'announcement_text' => 'Permohonan Biasiswa Negeri Sabah & Tajaan Pengajian Kumpulan Yayasan Sabah Tahun 2026 Kini Dibuka Dalam Talian.',
        'hero_title' => 'KUMPULAN YAYASAN SABAH',
        'hero_subtitle' => 'Membangun Modal Insan, Memperkasakan Komuniti, dan Memelihara Khazanah Alam Negeri Sabah Sejak 1966.',
        'hero_cta' => 'Permohonan Biasiswa & Tajaan',

        // Quick Access Grid
        'e_scholarship' => 'E-Biasiswa & Tajaan',
        'dept_notices_short' => 'Notis Bahagian',
        'conservation_areas' => 'Kawasan Konservasi',
        'zone_offices' => 'Pejabat Zon YS',
        'ys_portal' => 'Portal Warga YS',

        // Department Notices Section
        'dept_notices_title' => 'Pemberitahuan Mengikut Bahagian',
        'dept_notices_sub' => 'Pengumuman rasmi dan maklumat terkini daripada bahagian-bahagian dalaman Kumpulan Yayasan Sabah',
        'dept_edu' => 'Bahagian Pendidikan & Tajaan',
        'dept_edu_notice_1' => 'Semakan Keputusan Biasiswa Kerajaan Negeri Sabah Sesi 1 2026/2027.',
        'dept_edu_notice_2' => 'Jadual Temuduga Ujian Kecenderungan Tajaan Luar Negara.',
        'dept_con' => 'Bahagian Konservasi (YSEMD)',
        'dept_con_notice_1' => 'Permohonan Permit Penyelidikan Kawasan Konservasi Lembah Danum.',
        'dept_con_notice_2' => 'Penutupan Sementara Laluan Trek Kanyon Imbak Bagi Kerja Penyelenggaraan.',
        'dept_soc' => 'Pembangunan Komuniti',
        'dept_soc_notice_1' => 'Program Keusahawanan Desa Kumpulan Yayasan Sabah Zon Pedalaman.',
        'dept_soc_notice_2' => 'Pendaftaran Bengkel Kraf & Kemahiran Wanita Sabah.',
        'dept_hr' => 'Pentadbiran & HR',
        'dept_hr_notice_1' => 'Iklan Jawatan Kosong Kumpulan Yayasan Sabah (Gred 19 - 41).',
        'dept_hr_notice_2' => 'Kenyataan Sebutharga & E-Tender Bekalan Komputer Ibu Pejabat.',

        // Biasiswa Section
        'funding_title' => 'Pembiayaan & Bantuan Pendidikan',
        'funding_sub' => 'Skim bantuan kewangan dan tajaan pelajaran bagi anak-anak negeri Sabah',
        'bkns_title' => 'Biasiswa Kerajaan Negeri Sabah (BKNS)',
        'bkns_desc' => 'Tajaan penuh bagi penuntut cemerlang anak Sabah di peringkat Diploma, Ijazah Sarjana Muda, dan Pascasiswazah.',
        'apply_online' => 'Mohon Dalam Talian →',
        'pbu_title' => 'Pinjaman Boleh Ubah (PBU)',
        'pbu_desc' => 'Kemudahan pembiayaan pendidikan yang boleh ditukar kepada biasiswa berdasarkan pencapaian akademik (PNGK).',
        'pbu_link' => 'Syarat & Panduan PBU →',
        'assistance_title' => 'Bantuan Persediaan IPT',
        'assistance_desc' => 'Bantuan kewangan khas pendaftaran awal (one-off) bagi pelajar golongan B40 yang melanjutkan pengajian ke IPTA/IPTS.',
        'assistance_link' => 'Semak Borang Bantuan →',

        // Zone Offices Section
        'zones_title' => 'Pejabat-Pejabat Zon Yayasan Sabah',
        'zones_sub' => 'Perkhidmatan mesra rakyat yang dibawakan terus ke seluruh daerah Negeri Sabah',
        'zone_west' => 'Zon Pantai Barat',
        'zone_west_desc' => 'Kota Kinabalu, Penampang, Tuaran, Papar, Kota Belud, Ranau.',
        'zone_sandakan' => 'Zon Sandakan',
        'zone_sandakan_desc' => 'Sandakan, Beluran, Telupid, Kinabatangan, Tongod.',
        'zone_tawau' => 'Zon Tawau',
        'zone_tawau_desc' => 'Tawau, Lahad Datu, Semporna, Kunak.',
        'zone_interior' => 'Zon Pedalaman',
        'zone_interior_desc' => 'Keningau, Tenom, Tambunan, Nabawan, Beaufort, Sipitang.',

        // Conservation Section
        'con_title' => 'Bahagian Pengurusan Konservasi & Alam Sekitar (YSEMD)',
        'con_desc' => 'Kumpulan Yayasan Sabah mengurus dan memelihara lebih 180,000 hektar kawasan hutan yang diwartakan sebagai kawasan perlindungan antarabangsa di Borneo:',
        'danum' => 'Lembah Danum',
        'danum_sub' => 'Danum Valley Conservation Area (DVCA)',
        'maliau' => 'Maliau Basin',
        'maliau_sub' => 'Maliau Basin Conservation Area (MBCA)',
        'imbak' => 'Kanyon Imbak',
        'imbak_sub' => 'Imbak Canyon Conservation Area (ICCA)',

        // Footer
        'footer_hq' => 'Ibu Pejabat Kumpulan Yayasan Sabah',
        'footer_address' => 'Menara Tun Mustapha, Teluk Likas,<br>P.O. Box 11623, 88817 Kota Kinabalu,<br>Sabah, Malaysia.',
        'footer_services' => 'Perkhidmatan Utama',
        'footer_hours' => 'Waktu Operasi',
        'hours_mon_thu' => 'Isnin - Khamis: 8:00 AM - 1:00 PM | 2:00 PM - 5:00 PM',
        'hours_fri' => 'Jumaat: 8:00 AM - 11:30 AM | 2:00 PM - 5:00 PM',
        'hours_weekend' => 'Sabtu, Ahad & Cuti Umum: Tutup',
        'copyright' => 'Hak Cipta Terpelihara.'
    ],
    'en' => [
        // Navbar & Utility Bar
        'general_line' => 'General Line',
        'email' => 'Email',
        'language' => 'Language',
        'home' => 'Home',
        'notices' => 'Notices',
        'education' => 'Education',
        'conservation' => 'Conservation',
        'zones' => 'Zone Offices',
        'dashboard' => 'Dashboard',
        'logout' => 'Logout',
        'staff_portal' => 'YS Staff Portal',

        // Ticker & Hero Banner
        'announcement_label' => 'OFFICIAL ANNOUNCEMENT',
        'announcement_text' => 'Applications for Sabah State Government Scholarships & Yayasan Sabah Sponsorships for 2026 are now open online.',
        'hero_title' => 'YAYASAN SABAH GROUP',
        'hero_subtitle' => 'Developing Human Capital, Empowering Communities, and Preserving Sabah\'s Natural Heritage Since 1966.',
        'hero_cta' => 'Apply for Scholarships & Sponsorships',

        // Quick Access Grid
        'e_scholarship' => 'E-Scholarships',
        'dept_notices_short' => 'Department Notices',
        'conservation_areas' => 'Conservation Areas',
        'zone_offices' => 'Zone Offices',
        'ys_portal' => 'Staff Portal',

        // Department Notices Section
        'dept_notices_title' => 'Departmental Announcements',
        'dept_notices_sub' => 'Official announcements and notices from internal divisions of Yayasan Sabah Group',
        'dept_edu' => 'Education & Sponsorship Division',
        'dept_edu_notice_1' => 'Results for Sabah State Government Scholarship Session 1 2026/2027.',
        'dept_edu_notice_2' => 'Interview Schedule for Overseas Sponsorship Assessment.',
        'dept_con' => 'Conservation Division (YSEMD)',
        'dept_con_notice_1' => 'Research Permit Application for Danum Valley Conservation Area.',
        'dept_con_notice_2' => 'Temporary Closure of Imbak Canyon Trail for Maintenance Work.',
        'dept_soc' => 'Community Development',
        'dept_soc_notice_1' => 'Rural Entrepreneurship Program for Interior Zone.',
        'dept_soc_notice_2' => 'Registration for Sabah Women\'s Craft & Skills Workshop.',
        'dept_hr' => 'Administration & HR',
        'dept_hr_notice_1' => 'Career Vacancies at Yayasan Sabah Group (Grade 19 - 41).',
        'dept_hr_notice_2' => 'Quotations & E-Tender Notice for HQ Computer Supplies.',

        // Biasiswa Section
        'funding_title' => 'Education Funding & Assistance',
        'funding_sub' => 'Financial assistance and study sponsorship schemes for Sabah students',
        'bkns_title' => 'Sabah State Government Scholarship (BKNS)',
        'bkns_desc' => 'Full sponsorship for outstanding Sabah students pursuing Diploma, Bachelor\'s, and Postgraduate degrees.',
        'apply_online' => 'Apply Online →',
        'pbu_title' => 'Convertible Loan Scheme (PBU)',
        'pbu_desc' => 'Education financing facility that can be converted into a full scholarship based on academic achievement (CGPA).',
        'pbu_link' => 'PBU Requirements & Guide →',
        'assistance_title' => 'IPT Preparation Assistance',
        'assistance_desc' => 'Special one-off early registration financial aid for B40 students entering higher education institutions.',
        'assistance_link' => 'Check Assistance Form →',

        // Zone Offices Section
        'zones_title' => 'Yayasan Sabah Zone Offices',
        'zones_sub' => 'People-friendly services delivered directly across all districts of Sabah',
        'zone_west' => 'West Coast Zone',
        'zone_west_desc' => 'Kota Kinabalu, Penampang, Tuaran, Papar, Kota Belud, Ranau.',
        'zone_sandakan' => 'Sandakan Zone',
        'zone_sandakan_desc' => 'Sandakan, Beluran, Telupid, Kinabatangan, Tongod.',
        'zone_tawau' => 'Tawau Zone',
        'zone_tawau_desc' => 'Tawau, Lahad Datu, Semporna, Kunak.',
        'zone_interior' => 'Interior Zone',
        'zone_interior_desc' => 'Keningau, Tenom, Tambunan, Nabawan, Beaufort, Sipitang.',

        // Conservation Section
        'con_title' => 'Conservation & Environmental Management Division (YSEMD)',
        'con_desc' => 'Yayasan Sabah Group manages and preserves over 180,000 hectares of forest gazetted as internationally protected areas in Borneo:',
        'danum' => 'Danum Valley',
        'danum_sub' => 'Danum Valley Conservation Area (DVCA)',
        'maliau' => 'Maliau Basin',
        'maliau_sub' => 'Maliau Basin Conservation Area (MBCA)',
        'imbak' => 'Imbak Canyon',
        'imbak_sub' => 'Imbak Canyon Conservation Area (ICCA)',

        // Footer
        'footer_hq' => 'Yayasan Sabah Group HQ',
        'footer_address' => 'Menara Tun Mustapha, Likas Bay,<br>P.O. Box 11623, 88817 Kota Kinabalu,<br>Sabah, Malaysia.',
        'footer_services' => 'Main Services',
        'footer_hours' => 'Operating Hours',
        'hours_mon_thu' => 'Monday - Thursday: 8:00 AM - 1:00 PM | 2:00 PM - 5:00 PM',
        'hours_fri' => 'Friday: 8:00 AM - 11:30 AM | 2:00 PM - 5:00 PM',
        'hours_weekend' => 'Saturday, Sunday & Public Holidays: Closed',
        'copyright' => 'All Rights Reserved.'
    ]
];

// Active translations array
$L = $txt[$lang];