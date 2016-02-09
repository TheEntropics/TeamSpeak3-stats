<!DOCTYPE html>
<html ng-app="ts3stats">
<head>
    <title>TeamSpeak3 - The Entropics</title>
    <link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="bower_components/bootstrap/dist/css/bootstrap-theme.min.css" rel="stylesheet">
    <link href="bower_components/angularjs-slider/dist/rzslider.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="bower_components/angular-tree-control/css/tree-control-attribute.css" rel="stylesheet">
    <script src="bower_components/jquery/dist/jquery.min.js"></script>
    <script src="bower_components/angular/angular.min.js"></script>
    <script src="bower_components/angular-route/angular-route.min.js"></script>
    <script src="bower_components/angular-sanitize/angular-sanitize.min.js"></script>
    <script src="bower_components/angular-tree-control/angular-tree-control.js"></script>
    <script src="bower_components/angular-google-chart/ng-google-chart.min.js"></script>
    <script src="bower_components/angularjs-slider/dist/rzslider.min.js"></script>
    <script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
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
