<!DOCTYPE html>
<html ng-app="ts3stats">
<head>
    <title>TeamSpeak3 - The Entropics</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-theme.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/tree-control-attribute.css" rel="stylesheet">
    <script src="js/jquery-2.1.4.min.js"></script>
    <script src="js/angular.min.js"></script>
    <script src="js/angular-route.min.js"></script>
    <script src="js/angular-sanitize.min.js"></script>
    <script src="js/angular-tree-control.js"></script>
    <script src="js/ng-google-chart.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/utils.js"></script>
    <script src="js/app.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body class="container-fluid">
    <div class="page-header">
        <h1 class="text-center">
            <a href=".#/">TeamSpeak3</a> <br>
            <small>The Entropics</small>
        </h1>
    </div>

    <div ng-view></div>
</body>
</html>
