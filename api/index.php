<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

require __DIR__.'/../vendor/autoload.php';

$storagePath = sys_get_temp_dir().'/sewabus/storage';
$compiledViewPath = $storagePath.'/framework/views';

foreach ([
    $storagePath.'/app/private',
    $storagePath.'/app/public',
    $storagePath.'/framework/cache/data',
    $storagePath.'/framework/sessions',
    $compiledViewPath,
    $storagePath.'/logs',
] as $directory) {
    if (!is_dir($directory)) {
        mkdir($directory, 0777, true);
    }
}

putenv("LARAVEL_STORAGE_PATH={$storagePath}");
$_ENV['LARAVEL_STORAGE_PATH'] = $storagePath;
$_SERVER['LARAVEL_STORAGE_PATH'] = $storagePath;

putenv("VIEW_COMPILED_PATH={$compiledViewPath}");
$_ENV['VIEW_COMPILED_PATH'] = $compiledViewPath;
$_SERVER['VIEW_COMPILED_PATH'] = $compiledViewPath;

$forwardedProto = strtolower(trim(explode(',', $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')[0] ?? ''));
$forwardedHost = trim(explode(',', $_SERVER['HTTP_X_FORWARDED_HOST'] ?? '')[0] ?? '');

if ($forwardedHost !== '') {
    $_SERVER['HTTP_HOST'] = $forwardedHost;
    $_SERVER['SERVER_NAME'] = $forwardedHost;
}

if ($forwardedProto === 'https') {
    $_SERVER['HTTPS'] = 'on';
    $_SERVER['REQUEST_SCHEME'] = 'https';
    $_SERVER['SERVER_PORT'] = '443';
}

/** @var Application $app */
$app = require __DIR__.'/../bootstrap/app.php';

$app->useStoragePath($storagePath);

$_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__.'/../public') ?: __DIR__.'/../public';
$_SERVER['SCRIPT_FILENAME'] = realpath(__DIR__.'/../public/index.php') ?: __DIR__.'/../public/index.php';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';

$request = Request::create(
    $_SERVER['REQUEST_URI'] ?? '/',
    $_SERVER['REQUEST_METHOD'] ?? 'GET',
    $_POST,
    $_COOKIE,
    $_FILES,
    $_SERVER,
    file_get_contents('php://input')
);

$app->handleRequest($request);
