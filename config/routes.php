<?php

return [
    'GET' => [
        '/api/auth/me'            => ['controller' => 'AuthController',    'method' => 'me'],
        '/api/profile'            => ['controller' => 'ProfileController', 'method' => 'show'],
        '/api/reports'            => ['controller' => 'ReportController',  'method' => 'index'],
        '/api/reports/latest'     => ['controller' => 'ReportController',  'method' => 'latest'],
        '/api/reports/{id}'       => ['controller' => 'ReportController',  'method' => 'show'],
        '/api/dashboard/reports'  => ['controller' => 'ReportController',  'method' => 'myReports'],
        '/api/admin/dashboard'    => ['controller' => 'AdminController',   'method' => 'dashboard'],
    ],
    'POST' => [
        '/api/auth/register'      => ['controller' => 'AuthController',    'method' => 'register'],
        '/api/auth/login'         => ['controller' => 'AuthController',    'method' => 'login'],
        '/api/auth/logout'        => ['controller' => 'AuthController',    'method' => 'logout'],
        '/api/reports/lost'       => ['controller' => 'ReportController',  'method' => 'storeLost'],
        '/api/reports/found'      => ['controller' => 'ReportController',  'method' => 'storeFound'],
        '/api/claims'             => ['controller' => 'ClaimController',   'method' => 'store'],
        '/api/claims/{id}/approve'=> ['controller' => 'ClaimController',   'method' => 'approve'],
        '/api/claims/{id}/reject' => ['controller' => 'ClaimController',   'method' => 'reject'],
        '/api/admin/reports/found'=> ['controller' => 'AdminController',   'method' => 'createFound'],
    ],
    'PUT' => [
        '/api/profile'            => ['controller' => 'ProfileController', 'method' => 'update'],
    ],
    'DELETE' => [
        '/api/reports/{id}'       => ['controller' => 'ReportController',  'method' => 'destroy'],
    ],
];
