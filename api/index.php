<?php
/**
 * Vercel Entry Point for CakePHP Application
 */

use Cake\Http\Server;
use Cake\Http\ServerRequestFactory;

// Define paths
define('ROOT', dirname(__DIR__));
define('APP_DIR', ROOT . '/app');
define('WEBROOT_DIR', APP_DIR . '/webroot');

// CakePHP constants
define('DS', DIRECTORY_SEPARATOR);
define('CONFIG', APP_DIR . '/config/');
define('TMP', '/tmp/');  // Use Vercel's temp directory
define('LOGS', '/tmp/');

// Create necessary directories
@mkdir('/tmp/cache', 0755, true);
@mkdir('/tmp/cache/models', 0755, true);
@mkdir('/tmp/cache/persistent', 0755, true);
@mkdir('/tmp/cache/views', 0755, true);
@mkdir('/tmp/sessions', 0755, true);

// Composer autoloader (must be before use statements)
require_once APP_DIR . '/vendor/autoload.php';

// Handle static files
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($requestUri, PHP_URL_PATH);

if (preg_match('/\.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf)$/', $path)) {
    $filePath = WEBROOT_DIR . $path;
    if (file_exists($filePath)) {
        $ext = pathinfo($filePath, PATHINFO_EXTENSION);
        $mimeTypes = [
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
            'ttf' => 'font/ttf'
        ];
        
        if (isset($mimeTypes[$ext])) {
            header('Content-Type: ' . $mimeTypes[$ext]);
        }
        readfile($filePath);
        exit;
    }
}

try {
    // Bootstrap CakePHP
    require APP_DIR . '/config/bootstrap.php';

    // Create and run server
    $server = new Server(new App\Application(CONFIG));
    $request = ServerRequestFactory::fromGlobals();
    $server->emit($server->run($request));

} catch (Exception $e) {
    http_response_code(500);
    if ($_ENV['DEBUG'] ?? false) {
        echo "Error: " . $e->getMessage();
    } else {
        echo "Internal Server Error";
    }
}