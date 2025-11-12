<?php

$logosToJson = [];

foreach ($logos as $logo) {
    $logosToJson[] = [
        'id' => $logo->id,
        'name' => $logo->name,
        'original_name' => $logo->original_name,
        'file_path' => $logo->file_path,
        'file_size' => $logo->file_size,
        'file_extension' => $logo->file_extension,
        'width' => $logo->width,
        'height' => $logo->height,
        'uploaded_at' => $logo->uploaded_at,
        'user_id' => $logo->user_id,
        'is_active' => $logo->is_active
    ];
}

$json['logos'] = $logosToJson;
$json['pagination'] = [
    'page'                       => $paginator->getPage(),
    'per_page'                   => $paginator->perPage(),
    'total_of_pages'             => $paginator->totalOfPages(),
    'total_of_registers'         => $paginator->totalOfRegisters(),
    'total_of_registers_of_page' => $paginator->totalOfRegistersOfPage(),
];
