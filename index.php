<?php
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
global $conn;
include "app/config/database.php";
include "app/Controllers/MemberController.php";
include "app/Controllers/DashboardController.php";

//Get the uri
$requestUri = $_SERVER['REQUEST_URI'];
//redirect to dashboard for root url
if ($requestUri === '/') {
    header("Location: /dashboard");
    exit();
}

//Define the routes
$routes = [
    '/dashboard' => ['DashboardController', 'showDashboard'],
    '/get-all-members' => ['MemberController', 'getAllMembers'],
    '/get-member/{id}' => ['MemberController', 'getMember'],
    '/member' => ['MemberController','showMemberForm'],
    '/member/{id}' => ['MemberController','showMemberForm'],
    '/send-member' => ['MemberController','addOrEditMember'],
    '/send-member/{id}' => ['MemberController','addOrEditMember'],
    '/delete-member/{id}' => ['MemberController', 'deleteMember'],
    '/create-members-table/{credentials}' => ['MemberController', 'createMemberTable'],
];

//for getting parameters in the uri like {id} - replace and configure preg that can
//modify the url for sending the parameter(if exist) and loading the relevant method
foreach ($routes as $route => $controllerInfo) {
    $pattern = str_replace('/', '\/', $route);
    $pattern = preg_replace('/\{[a-zA-Z_][a-zA-Z0-9_:]*\}/', '([a-zA-Z0-9_:]+)?', $pattern);

    if (preg_match('/^' . $pattern . '$/', $requestUri, $matches)) {
        list($controllerClass, $method) = $controllerInfo;
        $controller = new $controllerClass($conn);
        array_shift($matches);
        $controller->$method(...$matches);
        exit();
    }
}
//if no route found. send to 404
header("HTTP/1.0 404 Not Found");
echo "404 Not Found";