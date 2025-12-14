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
        'event_type' => $event->event_type,
        'owner_id' => $event->owner_id
    ];
}

$json['events'] = $eventsToJson;
$json['pagination'] = [
    'page'                       => $paginator->getPage(),
    'per_page'                   => $paginator->perPage(),
    'total_of_pages'             => $paginator->totalOfPages(),
    'total_of_registers'         => $paginator->totalOfRegisters(),
    'total_of_registers_of_page' => $paginator->totalOfRegistersOfPage(),
];
