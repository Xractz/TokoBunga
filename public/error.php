<?php
$code = $_GET['code'] ?? http_response_code();
if (!in_array($code, [403, 404, 500])) {
    $code = 404;
}

$messages = [
    403 => [
        'title' => 'Access Denied',
        'desc' => 'Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.',
        'icon' => 'bi-shield-lock'
    ],
    404 => [
        'title' => 'Page Not Found',
        'desc' => 'Halaman yang Anda cari tidak ditemukan atau telah dipindahkan.',
        'icon' => 'bi-sign-dead-end'
    ],
    500 => [
        'title' => 'Server Error',
        'desc' => 'Terjadi kesalahan internal pada server. Silakan coba beberapa saat lagi.',
        'icon' => 'bi-hdd-network'
    ]
];

$msg = $messages[$code] ?? $messages[404];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/assets/images/favicon.png" type="image/png">
    <title><?php echo $code; ?> - <?php echo $msg['title']; ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display+SC:wght@400;700&family=Playfair:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="/assets/css/style.css" />
    
    <style>
        body {
            background-color: var(--bg-page);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            text-align: center;
            padding: 20px;
        }
        .error-card {
            background: white;
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 100%;
        }
        .error-icon {
            font-size: 5rem;
            color: var(--accent-dark);
            margin-bottom: 1rem;
        }
        .error-code {
            font-family: 'Playfair Display SC', serif;
            font-size: 3rem;
            color: var(--text-main);
            margin: 0;
            line-height: 1;
        }
        .error-title {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--text-muted);
        }
        .btn-home {
            background-color: var(--accent);
            border: none;
            padding: 10px 25px;
            border-radius: 50px;
            color: white;
            text-decoration: none;
            transition: 0.3s;
            margin-top: 20px;
            display: inline-block;
        }
        .btn-home:hover {
            background-color: var(--accent-dark);
            color: white;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <i class="bi <?php echo $msg['icon']; ?> error-icon"></i>
        <h1 class="error-code"><?php echo $code; ?></h1>
        <h2 class="error-title"><?php echo $msg['title']; ?></h2>
        <p><?php echo $msg['desc']; ?></p>
        <a href="/" class="btn-home">Kembali ke Beranda</a>
    </div>
</body>
</html>
