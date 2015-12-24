<?php

if (!isset($_GET['client-id'])) {
    header('location: .');
    exit;
}

require_once __DIR__ . '/src/classes/Controller.php';
Controller::init(true);

$client_id = intval($_GET['client-id']);
$username = ProbableUsernameVisualizer::getProbableUsername($client_id);

if ($username == null) {
    header('location: .');
    exit;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>TeamSpeak3 - The Entropics</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-theme.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <script src="js/jquery-2.1.4.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</head>
<body class="container">
    <div class="page-header">
        <h1 class="text-center">
            <a href=".">TeamSpeak3</a> <br>
            <small>The Entropics</small>
        </h1>
    </div>
    <h2>Statistics of <small><?php echo $username ?></small></h2>
    <?php require __DIR__ . '/src/user/ranges.php' ?>
</body>
</html>
