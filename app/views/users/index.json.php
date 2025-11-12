<?php

$usersToJson = [];

foreach ($users as $user) {
    $usersToJson[] = [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'cpf' => $user->cpf,
        'phone' => $user->phone,
        'is_admin' => $user->is_admin,
        'is_active' => $user->is_active,
        'created_at' => $user->created_at,
        'updated_at' => $user->updated_at
    ];
}

$json['users'] = $usersToJson;
$json['pagination'] = [
    'page'                       => $paginator->getPage(),
    'per_page'                   => $paginator->perPage(),
    'total_of_pages'             => $paginator->totalOfPages(),
    'total_of_registers'         => $paginator->totalOfRegisters(),
    'total_of_registers_of_page' => $paginator->totalOfRegistersOfPage(),
];
