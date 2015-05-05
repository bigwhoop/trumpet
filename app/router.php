<?php

namespace Trumpet;

use Bigwhoop\Trumpet\Config\Config;
use Bigwhoop\Trumpet\Presentation\Presenter;
use Bigwhoop\Trumpet\Presentation\Theme;
use Bigwhoop\Trumpet\Presentation\ThemeException;
use DI\Container as DIC;
use Handlebars\Handlebars;

if (php_sapi_name() !== 'cli-server') {
    exit("This file should only be run with PHP's built-in web server.");
}

error_reporting(E_ALL);
ini_set('display_errors', true);
ini_set('display_startup_errors', true);

require __DIR__.'/../vendor/autoload.php';

/** @var DIC $dic */
$dic = require __DIR__.'/etc/di.php';

set_error_handler($dic->get('ErrorHandler'));

/** @var Handlebars $handlebars */
$handlebars = $dic->get('Handlebars\Handlebars');

/**
 * @param int $httpStatusCode
 * @param string $view
 * @param array $data
 */
function renderInternalViewAndSendResponse($httpStatusCode, $view, array $data = [])
{
    global $handlebars;

    $data['content'] = $handlebars->render(file_get_contents(__DIR__.'/templates/'.$view.'.hbs'), $data);
    $response = $handlebars->render(file_get_contents(__DIR__.'/templates/layout.hbs'), $data);

    http_response_code($httpStatusCode);
    echo $response;
    exit();
}

$cwd = getcwd();
$requestUriParts = array_filter(explode('/', trim(urldecode($_SERVER['REQUEST_URI']), '/')), function ($e) {
    return !empty($e);
});

$trumpetFiles = array_map(function ($path) {
    return realpath($path);
}, glob("$cwd/*.trumpet"));

if (empty($trumpetFiles)) {
    renderInternalViewAndSendResponse(404, '404', [
        'title' => 'No *.trumpet Files Found',
        'message' => "No *.trumpet files were found in $cwd. You should create some and reload the page.",
    ]);
}

$trumpetFiles = array_combine(
    array_map(function ($path) {
        return '/'.pathinfo($path, PATHINFO_FILENAME).'/';
    }, $trumpetFiles),
    $trumpetFiles
);

// Index
if (empty($requestUriParts)) {
    renderInternalViewAndSendResponse(200, 'presentations-index', [
        'title' => 'Presentations',
        'trumpetFiles' => $trumpetFiles,
    ]);
}

if ($requestUriParts[0] === 'internal') {
    array_shift($requestUriParts);
    $internalPath = __DIR__ . '/www/' . implode('/', $requestUriParts);
    if (file_exists($internalPath)) {
        $ext = pathinfo($internalPath, PATHINFO_EXTENSION);
        switch ($ext) {
            case 'css': $contentType = 'text/css'; break;
            case 'svg': $contentType = 'image/svg+xml'; break;
            default:
                renderInternalViewAndSendResponse(500, '500', [
                    'title' => 'File Not Found',
                    'message' => "Content type for extension of file '$internalPath' was not defined.",
                ]);
        }

        http_response_code(200);
        header('content-type: ' . $contentType);
        readfile($internalPath);
        exit();
    }

    renderInternalViewAndSendResponse(404, '404', [
        'title' => 'File Not Found',
        'message' => 'Trumpet web asset not found.',
    ]);
}

// Presentation
if (array_key_exists('/'.$requestUriParts[0].'/', $trumpetFiles)) {
    $trumpetFile = $trumpetFiles['/'.$requestUriParts[0].'/'];

    try {
        /** @var Config $config */
        $config = $dic->get('Bigwhoop\Trumpet\Config\Config');

        $presentation = $config->readTrumpetFile($trumpetFile);

        /** @var Presenter $presenter */
        $presenter = $dic->get('Bigwhoop\Trumpet\Presentation\Presenter');

        http_response_code(200);
        echo $presenter->present($presentation);
        exit();
    } catch (\Exception $e) {
        renderInternalViewAndSendResponse(500, '500', [
            'title' => 'Internal Server Error',
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);
    }
}

// Theme customization
$assetPath = implode('/', $requestUriParts);
if (file_exists($cwd.'/'.$assetPath)) {
    return false;
}

// Theme asset
/** @var Theme $theme */
$theme = $dic->get('Bigwhoop\Trumpet\Presentation\Theme');
try {
    $asset = $theme->getAsset($assetPath);

    http_response_code(200);
    header('content-type: '.$asset->contentType);
    echo $asset->content;
    exit();
} catch (ThemeException $e) {
    renderInternalViewAndSendResponse(404, '404', [
        'title' => 'File Not Found',
        'message' => $e->getMessage(),
    ]);
}
