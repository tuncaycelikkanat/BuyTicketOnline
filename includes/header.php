<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo $page_title ?? "Buy Ticket"; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        header {
            background-color: #1a1a1a;
            text-align: center;
            padding: 20px 0;
            border-bottom: 2px solid #0ff;
        }

        header a {
            display: inline-block;
            margin: 5px 10px;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            color: #0ff;
            font-weight: bold;
            background-color: #222;
            box-shadow: 0 0 5px #0ff;
            transition: all 0.3s ease;
        }

        header a:hover {
            color: #fff;
            background-color: #333;
            box-shadow: 0 0 10px #0ff, 0 0 20px #0ff;
            transform: scale(1.05);
        }
    </style>
</head>

<body>
    <header>
        <a href="../index.php" class="btn-rounded btn-primary">Main Page</a>
        <a href="/auth/profile.php" class="btn-rounded btn-primary">My Profile</a>
    </header>
</body>

</html>