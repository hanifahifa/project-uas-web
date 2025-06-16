<?php
session_start();
include '../db.php'; // Assuming you have your connection file

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_nik'])) {
    header('Location: login.php');
    exit;
}

$user_nik = $_SESSION['user_nik'];

// Fetch user data from the database based on user_nik
$queryUser = mysqli_query($koneksi, "SELECT * FROM users WHERE nik='$user_nik'");
$user = mysqli_fetch_assoc($queryUser);

// Ensure the user exists in the database
if (!$user) {
    echo "User not found!";
    exit;
}

$nama = $user['name']; // Using 'name' from the users table

// Fetch all roles of the user from the 'user_roles' table
$queryRoles = mysqli_query($koneksi, "SELECT role FROM user_roles WHERE nik='$user_nik'");
$roles = [];
while ($role = mysqli_fetch_assoc($queryRoles)) {
    $roles[] = $role['role']; // Add each role to the roles array
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard - <?= htmlspecialchars($nama); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .sidebar {
            width: 250px;
            background-color: #2d6a4f;
            color: white;
            position: fixed;
            height: 100vh;
            padding-top: 20px;
        }

        .sidebar a {
            display: block;
            padding: 10px;
            color: white;
            text-decoration: none;
            font-size: 18px;
            margin: 10px 0;
            transition: background-color 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #1f4f37;
        }

        .sidebar .active {
            background-color: #38b2ac;
        }

        .main-content {
            margin-left: 260px;
            padding: 20px;
            background-color: #f0fdf4;
        }

        .menu-link {
            padding: 10px 15px;
            font-size: 16px;
            display: block;
            color: #fff;
        }

        .menu-link:hover {
            background-color: #388e3c;
        }

        .submenu {
            display: none;
            padding-left: 20px;
        }

        .submenu a {
            font-size: 14px;
            padding-left: 30px;
        }

        .submenu.active {
            display: block;
        }

        .fade-in {
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }

        .fade-in.show {
            opacity: 1;
        }

        #loading-screen {
            position: fixed;
            inset: 0;
            z-index: 50;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f5f7fa;
        }

        .greeting {
            font-size: 24px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 20px;
        }

        .role-list {
            margin-top: 10px;
            font-size: 18px;
            font-weight: 600;
            color: #3b82f6;
        }

        .role-item {
            margin: 5px 0;
        }
    </style>
</head>
<body>

    <!-- Loading Screen -->
    <div id="loading-screen" class="flex flex-col items-center justify-center">
        <div id="loading-logo" class="w-20 h-20 rounded-full bg-cyan-500 flex items-center justify-center text-white">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" width="70" height="70" aria-hidden="true" focusable="false">
                <path d="M32 2C19 2 10 12 10 24c0 12 10 28 22 28s22-16 22-28c0-12-9-22-22-22zm0 48c-11 0-19-15-19-26 0-9 7-18 19-18s19 9 19 18c0 11-8 26-19 26z"/>
                <circle cx="22" cy="26" r="4" />
                <circle cx="42" cy="26" r="4" />
                <path d="M32 38c-5 0-8 4-8 4h16s-3-4-8-4z"/>
            </svg>
        </div>
        <p class="mt-6 text-lg font-semibold animate-bounce" style="color:#0e7490;">Memuat dashboard...</p>
    </div>

    <!-- Sidebar -->
    <div class="sidebar fade-in">
        <h3 class="text-center text-white mb-6">Halo, <?= htmlspecialchars($nama); ?></h3>

        <!-- Menampilkan menu sesuai role -->
        <?php
        // Associative array for menu items based on role
        $menu_per_role = [
            'admin' => [
                'label' => 'Admin',
                'items' => [
                    ['text' => 'Dashboard Admin', 'page' => '../admin/dashboard_admin.php'],
                    ['text' => 'Manage Users', 'page' => '../admin/manage_user.php'],
                ]
            ],
            'warga' => [
                'label' => 'Warga',
                'items' => [
                    ['text' => 'Dashboard Warga', 'page' => '../warga/dashboard_warga.php'],
                ]
            ],
            'panitia' => [
                'label' => 'Panitia',
                'items' => [
                    ['text' => 'Dashboard Panitia', 'page' => '../panitia/dashboard_panitia.php'],
                ]
            ],
            'berqurban' => [
                'label' => 'Pekurban',
                'items' => [
                    ['text' => 'Dashboard Pekurban', 'page' => '../berqurban/dashboard_berqurban.php'],
                ]
            ],
        ];

        // Display the appropriate menu based on user roles
        foreach ($roles as $role) {
            if (isset($menu_per_role[$role])) :
                $menu = $menu_per_role[$role];
        ?>
            <a href="#" class="menu-link"><?= htmlspecialchars($menu['label']); ?> <i class="fa fa-chevron-down"></i></a>
            <div class="submenu" id="role-submenu">
                <?php foreach ($menu['items'] as $item) : ?>
                    <a href="#" class="submenu-item" data-page="<?= htmlspecialchars($item['page']); ?>">
                        <?= htmlspecialchars($item['text']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php
            endif;
        }
        ?>

        <a href="../logout.php">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main-content fade-in">
        <div class="greeting">
            <p>Selamat datang kembali, <?= htmlspecialchars($nama); ?>! Semoga hari Anda menyenankan.</p>
        </div>
        <div class="role-list">
            <p>Peran Anda:</p>
            <ul>
                <?php
                // Menampilkan semua role yang dimiliki oleh user
                foreach ($roles as $role) {
                    echo "<li class='role-item'>🔹 " . ucfirst($role) . "</li>";
                }
                ?>
            </ul>
        </div>
        <div id="content"> <!-- Konten yang dimuat akan tampil di sini --> </div>
    </div>

    <script>
        $(document).ready(function () {
            // Loading only for 0.5 seconds
            setTimeout(function () {
                $('#loading-screen').fadeOut(300, function () {
                    $('.fade-in').addClass('show');
                });
            }, 500);

            // Toggle submenu
            $('.menu-link').click(function (e) {
                e.preventDefault();
                $('#role-submenu').toggleClass('active');
                $(this).find('i').toggleClass('fa-chevron-down fa-chevron-up');
            });

            // Load dynamic content on menu link click
            $('.menu-link').click(function (e) {
                e.preventDefault();
                const page = $(this).data('page');
                // Highlight the active menu
                $('.menu-link').removeClass('active');
                $(this).addClass('active');
                // Load the page content dynamically
                $('#content').load(page);  // Load the page content inside #content div
            });

            // Load submenu item
            $('.submenu-item').click(function (e) {
                e.preventDefault();
                const page = $(this).data('page');
                $('#content').load(page);  // Load the page content inside #content div
            });
        });
    </script>
</body>
</html>
