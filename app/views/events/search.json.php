<?php

$eventsToJson = [];

foreach ($events as $event) {
    $eventsToJson[] = [
        'id' => $event->id,
        'name' => $event->name,
        'description' => $event->description,
        'start_date' => $event->start_date,
        'end_date' => $event->end_date,
        'event_location' => $event->event_location,
        'workload_hours' => $event->workload_hours,
        'event_type' => $event->event_type
    ];
}

$json['success'] = true;
$json['events'] = $eventsToJson;
