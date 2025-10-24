    <?php
    define('ROOT_PATH', dirname(__DIR__));
    require_once ROOT_PATH . '/includes/config.php';
    include '../includes/functions.php';
    require_once '../includes/header.php';

    require_role('company');
    if (!is_login())
        header('Location: /auth/login.php');
    ?>

    <!DOCTYPE html>
    <html lang="tr">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Company Admin Main Page</title>
        <style>
            body {
                margin: 0;
                font-family: Arial, sans-serif;
                background: #0d0d0d;
                color: #0ff;
                display: flex;
                flex-direction: column;
                min-height: 100vh;
                text-align: center;
            }

            main {
                flex: 1;
                padding: 60px 20px 80px 20px;
            }

            h1 {
                margin-bottom: 20px;
                color: #0ff;
                text-shadow: 0 0 10px #0ff;
            }

            ul.menu {
                list-style: none;
                padding: 0;
                margin-top: 30px;
                display: flex;
                flex-direction: column;
                /* dikey sıralama */
                align-items: center;
                /* ortalama */
            }

            ul.menu li {
                width: 200px;
                /* opsiyonel: tüm butonlar aynı genişlikte */
                margin: 12px 0;
            }

            ul.menu li a {
                display: block;
                padding: 12px;
                border-radius: 25px;
                text-decoration: none;
                color: #0ff;
                font-weight: bold;
                background-color: #272727ff;
                box-shadow: 0 0 6px #0ff;
                transition: 0.3s;
                text-align: center;
            }

            ul.menu li a:hover {
                color: #000;
                background-color: #0ff;
                box-shadow: 0 0 12px #0ff, 0 0 25px #0ff;
                transform: scale(1.1);
            }

            ul.menu li a.logout {
                color: rgba(179, 0, 0, 1);
                font-weight: bold;
                background-color: #272727ff;
                box-shadow: 0 0 6px rgba(255, 0, 0, 1);
            }

            ul.menu li a.logout:hover {
                color: #fff;
                background-color: rgba(179, 0, 0, 1);
                box-shadow: 0 0 12px rgba(255, 0, 0, 1);
                transform: scale(1.1);
            }
        </style>
    </head>

    <body>
        <main>
            <h1>Company Admin Panel</h1>

            <ul class="menu">
                <li><a href="/firm_admin/routes.php">Routes</a></li>
                <li><a href="/firm_admin/coupons.php">Coupons</a></li>
                <li><a href="/auth/logout.php" class="logout">Logout</a></li>
            </ul>
        </main>

        <?php require_once '../includes/footer.php'; ?>
    </body>

    </html>