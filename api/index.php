<?php
/**
 * Vercel Entry Point for CakePHP Application
 * This file handles all HTTP requests in Vercel's serverless environment
 */

// Define paths relative to the api directory
define('ROOT', dirname(__DIR__));
define('APP_DIR', ROOT . '/app');
define('WEBROOT_DIR', APP_DIR . '/webroot');
define('WWW_ROOT', WEBROOT_DIR . '/');

// CakePHP constants
define('DS', DIRECTORY_SEPARATOR);
define('CAKE_CORE_INCLUDE_PATH', APP_DIR . '/vendor/cakephp/cakephp');
define('CORE_PATH', CAKE_CORE_INCLUDE_PATH . DS);
define('CAKE', CORE_PATH . 'src' . DS);
define('CONFIG', APP_DIR . '/config/');
define('TMP', APP_DIR . '/tmp/');
define('LOGS', APP_DIR . '/logs/');

// Ensure tmp directories exist (important for Vercel)
if (!is_dir(TMP)) {
    mkdir(TMP, 0755, true);
}
if (!is_dir(TMP . 'cache')) {
    mkdir(TMP . 'cache', 0755, true);
}
if (!is_dir(TMP . 'cache/models')) {
    mkdir(TMP . 'cache/models', 0755, true);
}
if (!is_dir(TMP . 'cache/persistent')) {
    mkdir(TMP . 'cache/persistent', 0755, true);
}
if (!is_dir(TMP . 'cache/views')) {
    mkdir(TMP . 'cache/views', 0755, true);
}
if (!is_dir(TMP . 'sessions')) {
    mkdir(TMP . 'sessions', 0755, true);
}
if (!is_dir(LOGS)) {
    mkdir(LOGS, 0755, true);
}

// Set include path
ini_set('include_path', ROOT . '/app' . PATH_SEPARATOR . ini_get('include_path'));

// Composer autoloader
require_once APP_DIR . '/vendor/autoload.php';

// Handle static files if they exist
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$parsedUrl = parse_url($requestUri);
$path = $parsedUrl['path'] ?? '/';

// Check if it's a static file request
if (preg_match('/\.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$/', $path)) {
    $filePath = WEBROOT_DIR . $path;
    if (file_exists($filePath)) {
        // Set appropriate content type
        $ext = pathinfo($filePath, PATHINFO_EXTENSION);
        $contentTypes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'ico' => 'image/x-icon',
            'svg' => 'image/svg+xml',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'eot' => 'application/vnd.ms-fontobject'
        ];
        
        if (isset($contentTypes[$ext])) {
            header('Content-Type: ' . $contentTypes[$ext]);
        }
        
        readfile($filePath);
        exit;
    }
}

try {
    // Bootstrap CakePHP
    require APP_DIR . '/config/bootstrap.php';

    use Cake\Http\Server;
    use Cake\Http\ServerRequestFactory;

    // Create server instance
    $server = new Server(new App\Application(CONFIG));

    // Create request from globals
    $request = ServerRequestFactory::fromGlobals();
    
    // Emit response
    $server->emit($server->run($request));

} catch (Exception $e) {
    // Error handling for production
    http_response_code(500);
    
    if (getenv('DEBUG') === 'true') {
        echo "Error: " . $e->getMessage() . "\n";
        echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
        echo "Trace:\n" . $e->getTraceAsString();
    } else {
        echo "Internal Server Error";
    }
}