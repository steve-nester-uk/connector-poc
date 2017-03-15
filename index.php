<?php
$config = include(__DIR__ . "/config.php");

require_once(__DIR__."/util/PageChooser.php");
$requestUri = $_SERVER["REQUEST_URI"];
$p = new PageChooser();
$page = $p->set($requestUri);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">

    <title><?php echo $page["title"]; ?></title>
    <link href="<?php echo $config->rootDir; ?>/static/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo $config->rootDir; ?>/static/style.css" rel="stylesheet">
    <script src="<?php echo $config->rootDir; ?>/static/jquery.js"></script>
</head>
<body>
    <?php
        include_once(__DIR__ ."/views/header.php");
        
        echo "<div class='main'>";
        include_once($page["include"]);
        echo "</div>";

        include_once(__DIR__ ."/views/footer.php"); 
    ?>
    <script src="<?php echo $config->rootDir; ?>/static/script.js"></script>
</body>
</html>