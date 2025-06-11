<?php
session_start();

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        html, body {
        height: 100%;      
        margin: 0; 
        
        }
        body{
            display: flex;
            flex-direction: column;
        }

        main{
            flex-grow: 1;
        }
        header{
            display: flex;
            align-items: center;
            background-color:rgb(92, 92, 92);
            height: 100px;
            flex-shrink: 0;
            padding: 20px;
            color: white;
        }
        div.space{
            flex-grow: 1;
        }
        div.navs{
            margin-right: 10px;
        }
        a.noclass{
            text-decoration: none;
            font-size: 20px;
        }
        a.noclass:hover{
            text-decoration: none;
            font-size: 20px;
            color: lightgray;
        }
    </style>
    <link rel="stylesheet" href="css/admin.css">
    <meta charset="UTF-8">
    <title><?php echo $title ?? '';?></title>
</head>
    <body>
        <header>
            <h4><?php echo $title ?></h4>
            <div class="space"> </div>
            <div class="navs"><a class="noclass" href="adminhome.php">Home</a></div>
            <div class="navs"><a class="noclass" href="admin_view_flights.php">Flights</a></div>
            <div class="navs"><a class="noclass" href="admin_add_flights.php">Add Flights</a></div>
            <div class="navs"><a class="noclass" href="<?php echo $_SERVER['PHP_SELF']; ?>?logout=1">Logout</a></div>
        </header>
        <main>
            <?php echo $content ?? ''; ?>
        </main>
        <footer>

        </footer>
    </body>
</html>