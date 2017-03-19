<?php declare(strict_types=1);

namespace Trumpet;

use Bigwhoop\SentenceBreaker;
use Bigwhoop\Trumpet\Commands;
use Bigwhoop\Trumpet\Config\Config;
use Bigwhoop\Trumpet\Config\Params;
use Bigwhoop\Trumpet\Exceptions\RuntimeException;
use Bigwhoop\Trumpet\Http\BuiltInPhpHttpServer;
use Bigwhoop\Trumpet\Http\HttpServer;
use Bigwhoop\Trumpet\Presentation\ThemePresenter;
use Bigwhoop\Trumpet\Presentation\Presenter;
use Bigwhoop\Trumpet\Presentation\SlideRendering;
use Bigwhoop\Trumpet\Presentation\Theming\TwigTheme;
use Bigwhoop\Trumpet\Presentation\Theming\Theme;
use DI\Container;
use DI\ContainerBuilder;
use Psr\Log\LoggerInterface;
use function DI\factory;
use function DI\get;
use function DI\object;

$definitions = [
    'ErrorHandler' => factory(function () {
        return function (int $severity, string $message, string $file, int $line) {
            if (!(error_reporting() & $severity)) {
                return;
            }

            throw new \ErrorException($message, 0, $severity, $file, $line);
        };
    }),

    'WorkingDirectory' => getcwd(),

    'ThemeDirectories' => factory(function (Container $c) {
        return [
            $c->get('WorkingDirectory').'/.theme',
            __DIR__ . '/../app/theme'
        ];
    }),
    
    'ThemeDirectory' => factory(function (Container $c) {
        $paths = $c->get('ThemeDirectories');
        foreach ($paths as $path) {
            if (is_dir($path) && is_readable($path)) {
                return $path;
            }
        }
        
        throw new RuntimeException('Non of the theme paths were readable: ' . join(', ', $paths));
    }),

    LoggerInterface::class => factory(function () {
        $stdOutHandler = new \Monolog\Handler\StreamHandler(fopen('php://stdout', 'wb'));
        $stdOutHandler->setFormatter(new \Monolog\Formatter\LineFormatter('%datetime% [%level_name%] %message%'));
        
        return new \Monolog\Logger('trumpet', [$stdOutHandler]);
    }),
    
    HttpServer::class => object(BuiltInPhpHttpServer::class)
        ->constructor('localhost', 8075, __DIR__.'/../app/router.php', get('WorkingDirectory'))
        ->method('setLogger', get(LoggerInterface::class)),

    Config::class => factory(function () {
        $config = new Config();
        $config->setParam('title', new Params\TitleParam());
        $config->setParam('subtitle', new Params\SubTitleParam());
        $config->setParam('date', new Params\DateParam());
        $config->setParam('authors', new Params\AuthorsParam());
        $config->setParam('slides', new Params\SlidesParam());
        $config->setParam('license', new Params\LicenseParam());
        $config->setParam('theme', new Params\ThemeParam());

        return $config;
    }),

    Theme::class => object(TwigTheme::class)
        ->constructor(get('ThemeDirectory')),

    Presenter::class => factory(function (Container $c) {
        /** @var ThemePresenter $presenter */
        $presenter = $c->get(ThemePresenter::class);
        $presenter->addSlideRenderer($c->get(SlideRendering\CommandsRenderer::class));
        $presenter->addSlideRenderer($c->get(SlideRendering\MarkdownExtraSlideRenderer::class));

        return $presenter;
    }),
    
    SentenceBreaker\SentenceBreaker::class => object()
        ->method('addAbbreviations', get(SentenceBreaker\Abbreviations\ValueProvider::class)),
    
    SentenceBreaker\Abbreviations\ValueProvider::class => object(SentenceBreaker\Abbreviations\FlatFileProvider::class)
        ->constructor(__DIR__ . '/../../vendor/bigwhoop/sentence-breaker/data', ['all']),

    Commands\CommandExecutionContext::class => object()
        ->constructor(get(Theme::class), get('WorkingDirectory')),

    Commands\CommandHandler::class => factory(function (Container $c) {
        $handler = new Commands\CommandHandler();
        $handler->registerCommand($c->get(Commands\CodeCommand::class));
        $handler->registerCommand($c->get(Commands\ExecCommand::class));
        $handler->registerCommand($c->get(Commands\IncludeCommand::class));
        $handler->registerCommand($c->get(Commands\ImageCommand::class));
        $handler->registerCommand($c->get(Commands\WikiCommand::class));

        return $handler;
    }),

    \PhpParser\PrettyPrinterAbstract::class => object(\PhpParser\PrettyPrinter\Standard::class),

    \PhpParser\Lexer::class => object(\PhpParser\Lexer\Emulative::class),

    \PhpParser\ParserAbstract::class => object(\PhpParser\Parser\Php7::class)
        ->constructor(get(\PhpParser\Lexer::class)),

    \PhpParser\NodeTraverserInterface::class => object(\PhpParser\NodeTraverser::class)
        ->method('addVisitor', get(\PhpParser\NodeVisitor\NameResolver::class)),
];

$builder = new ContainerBuilder();
$builder->addDefinitions($definitions);
$builder->useAnnotations(true);

return $builder->build();
