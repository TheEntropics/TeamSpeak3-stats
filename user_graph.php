<?php

require_once __DIR__ . '/src/classes/Controller.php';
Controller::init(true);

setlocale(LC_ALL, "it_IT.utf8");

$client_id = $_GET['client-id'];

$data = DailyUserUptimeVisualizer::getDailyUserUptime($client_id);
foreach ($data as $i => $row) {
    $date = strftime('%d %B %Y', (new DateTime($row['date']))->getTimestamp());
    $time = UptimeVisualizer::formatTime($row['day_time']);
    $data[$i]['tooltip'] = "<div><h4>$date</h4><p>$time</p></div>";
}

header('Content-Type: application/json');
echo json_encode($data);
