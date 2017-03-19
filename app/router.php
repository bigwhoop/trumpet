<?php declare(strict_types=1);

namespace Trumpet;

use Bigwhoop\Trumpet\Config\Config;
use Bigwhoop\Trumpet\Presentation\Presenter;
use Bigwhoop\Trumpet\Presentation\Theming\Theme;
use Bigwhoop\Trumpet\Presentation\Theming\ThemeException;
use Interop\Container\ContainerInterface as Container;

if (PHP_SAPI !== 'cli-server') {
    exit("This file should only be run with PHP's built-in web server.");
}

/** @var Container $container */
$container = require __DIR__ . '/../bootstrap.php';

$twig = new \Twig_Environment(new \Twig_Loader_Filesystem(__DIR__ . '/templates'));

$renderTemplateAndSendResponse = function (int $httpStatusCode, string $view, array $data = []) use ($twig) {
    $response = $twig->render($view.'.html.twig', $data);

    http_response_code($httpStatusCode);
    echo $response;
    exit();
};

$cwd = getcwd();
$requestUriParts = array_filter(explode('/', trim(urldecode($_SERVER['REQUEST_URI']), '/')), function ($e) {
    return !empty($e);
});

if (!empty($requestUriParts) && $requestUriParts[0] === '?new') {
    $idx = 1;
    do {
        $newPresentationPath = $cwd.'/Presentation '.$idx++.'.trumpet';
    } while (file_exists($newPresentationPath));

    copy(__DIR__ . '/example/Presentation.trumpet', $newPresentationPath);

    header('location: /', true, 302);
    exit();
}

$trumpetFiles = array_map(function ($path) {
    return realpath($path);
}, glob("$cwd/*.trumpet"));

// Trumpet assets
if (!empty($requestUriParts) && $requestUriParts[0] === 'internal') {
    array_shift($requestUriParts);
    $internalPath = __DIR__.'/www/'.implode('/', $requestUriParts);
    if (file_exists($internalPath)) {
        $ext = pathinfo($internalPath, PATHINFO_EXTENSION);
        switch ($ext) {
            case 'css': $contentType = 'text/css'; break;
            case 'svg': $contentType = 'image/svg+xml'; break;
            default:
                $renderTemplateAndSendResponse(500, '500', [
                    'title' => 'File Not Found',
                    'message' => "Content type for extension of file '$internalPath' was not defined.",
                ]);
        }

        http_response_code(200);
        header('content-type: '.$contentType);
        readfile($internalPath);
        exit();
    }

    $renderTemplateAndSendResponse(404, '404', [
        'title' => 'File Not Found',
        'message' => 'Trumpet web asset not found.',
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
    $presentations = [];
    foreach ($trumpetFiles as $url => $trumpetFile) {
        $presentations[] = [
            'title' => pathinfo($trumpetFile, PATHINFO_FILENAME),
            'url'   => $url,
            'size'  => filesize($trumpetFile),
        ];
    }
    $renderTemplateAndSendResponse(200, 'presentations-index', [
        'title' => 'Presentations',
        'presentations' => $presentations,
        'cwd' => $cwd,
    ]);
}

// Presentation
if (array_key_exists('/'.$requestUriParts[0].'/', $trumpetFiles)) {
    $trumpetFile = $trumpetFiles['/'.$requestUriParts[0].'/'];

    try {
        /** @var Config $config */
        $config = $container->get(Config::class);

        $presentation = $config->readTrumpetFile($trumpetFile);

        /** @var Presenter $presenter */
        $presenter = $container->get(Presenter::class);

        http_response_code(200);
        echo $presenter->present($presentation);
        exit();
    } catch (\Exception $e) {
        $renderTemplateAndSendResponse(500, '500', [
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
$theme = $container->get(Theme::class);
try {
    $asset = $theme->getAsset($assetPath);

    http_response_code(200);
    header('content-type: '.$asset->contentType);
    echo $asset->content;
    exit();
} catch (ThemeException $e) {
    $renderTemplateAndSendResponse(404, '404', [
        'title' => 'File Not Found',
        'message' => $e->getMessage(),
    ]);
}
