<?php
session_start();

if (!$_SESSION['id_user']) {
    header("location: login.php");
    exit;
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Vintage</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts & Font Awesome -->
    <link href="https://fonts.googleapis.com/css2?family=Cinzel&family=EB+Garamond&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background-color: #fef8e7;
            font-family: 'EB Garamond', serif;
            color: #4b3e2b;
        }

        .vintage-card {
            background-color: #fffaf3;
            border: 1px solid #ccbfa3;
            box-shadow: 2px 4px 12px rgba(0, 0, 0, 0.1);
        }

        .list-group-item.active {
            background-color: #5b4636;
            border-color: #5b4636;
            color: #fff;
            font-family: 'Cinzel', serif;
            font-weight: bold;
        }

        .list-group-item {
            background-color: #e8dbc4;
            border-color: #c6b49c;
            color: #3f2f1b;
        }

        .list-group-item:hover {
            background-color: #d6c3a5;
            color: #2e261a;
        }

        .vintage-header {
            font-family: 'Cinzel', serif;
            font-size: 1.5rem;
            color: #3f2f1b;
            border-bottom: 2px solid #b89b7d;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>

    <div class="container py-5">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 mb-4">
                <ul class="list-group">
                    <li class="list-group-item active">MAIN MENU</li>
                    <a href="dashboard.php" class="list-group-item text-decoration-none">Dashboard</a>
                    <a href="input-barang.php" class="list-group-item text-decoration-none">Inventory</a>
                    <a href="logout.php" class="list-group-item text-decoration-none">Logout</a>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-9">
                <div class="card vintage-card">
                    <div class="card-body">
                        <div class="vintage-header">Dashboard</div>
                        <p class="mb-0">
                            Selamat Datang <strong><?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?></strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
