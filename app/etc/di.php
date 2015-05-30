<?php

namespace Trumpet;

use Bigwhoop\Trumpet\Commands\CommandHandler;
use Bigwhoop\Trumpet\Config\Params;
use Bigwhoop\Trumpet\Config\Config;
use Bigwhoop\Trumpet\Presentation\HandlebarsPresenter;
use DI;
use DI\Container as DIC;
use Handlebars\Handlebars;
use Handlebars\Loader\StringLoader as HbsStringLoader;
use Handlebars\Template as HbsTemplate;
use Handlebars\Context as HbsContext;

$definitions = [
    'ErrorHandler' => DI\factory(function () {
        return function ($severity, $message, $file, $line) {
            if (!(error_reporting() & $severity)) {
                return;
            }

            throw new \ErrorException($message, 0, $severity, $file, $line);
        };
    }),

    'WorkingDirectory' => getcwd(),

    'ThemeDirectory' => DI\factory(function (DIC $c) {
        return $c->get('WorkingDirectory').DIRECTORY_SEPARATOR.'theme';
    }),

    'Psr\Log\LoggerInterface' => DI\object('\Monolog\Logger')
        ->constructor('trumpet')
        ->method('pushHandler', DI\link('Monolog\Handler\HandlerInterface')),

    'Monolog\Handler\HandlerInterface' => DI\object('\Monolog\Handler\StreamHandler')
        ->constructor(fopen('php://stdout', 'w'))
        ->method('setFormatter', DI\link('Monolog\Formatter\FormatterInterface')),

    'Monolog\Formatter\FormatterInterface' => DI\object('\Monolog\Formatter\LineFormatter')
        ->constructor('%datetime% [%level_name%] %message%'),

    'Bigwhoop\Trumpet\HTTP\Server' => DI\object('Bigwhoop\Trumpet\HTTP\BuiltInPHPServer')
        ->constructor('localhost', 8075, __DIR__.'/../router.php', DI\link('WorkingDirectory'))
        ->method('setLog', DI\link('Psr\Log\LoggerInterface')),

    'Bigwhoop\Trumpet\Config\Config' => DI\factory(function () {
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

    'Bigwhoop\Trumpet\Presentation\Theming\Theme' => DI\object('Bigwhoop\Trumpet\Presentation\Theming\HandlebarsTheme')
        ->constructor(DI\link('ThemeDirectory')),

    'Bigwhoop\Trumpet\Presentation\Presenter' => DI\factory(function (DIC $c) {
        /** @var HandlebarsPresenter $presenter */
        $presenter = $c->get('Bigwhoop\Trumpet\Presentation\HandlebarsPresenter');
        $presenter->addSlideRenderer($c->get('Bigwhoop\Trumpet\Presentation\SlideRendering\CommandsRenderer'));
        $presenter->addSlideRenderer($c->get('Bigwhoop\Trumpet\Presentation\SlideRendering\MarkdownExtraSlideRenderer'));

        return $presenter;
    }),
    
    'Bigwhoop\SentenceBreaker\SentenceBreaker' => DI\object()
        ->method('addAbbreviations', DI\link('Bigwhoop\SentenceBreaker\Abbreviations\ValueProvider')),
    
    'Bigwhoop\SentenceBreaker\Abbreviations\ValueProvider' => DI\object('Bigwhoop\SentenceBreaker\Abbreviations\FlatFileProvider')
        ->constructor(__DIR__ . '/../../vendor/bigwhoop/sentence-breaker/data', ['all']),

    'Handlebars\Handlebars' => DI\factory(function () {
        $hbs = new Handlebars();
        $hbs->setLoader(new HbsStringLoader());

        $hbs->addHelper('urlencode', function (HbsTemplate $template, HbsContext $context, $args, $source) {
            return rawurlencode($context->get($args));
        });
        
        $hbs->addHelper('count', function (HbsTemplate $template, HbsContext $context, $args, $source) {
            return count($context->get($args));
        });

        $hbs->addHelper('join', function (HbsTemplate $template, HbsContext $context, $args, $source) {
            $matches = [];
            if (preg_match("#'([^']+)' (.+)#", $args, $matches)) {
                list(, $separator, $input) = $matches;

                $out = [];
                foreach ((array) $context->get($input) as $value) {
                    $context->push($value);
                    $out[] = $template->render($context);
                    $context->pop();
                }

                return implode($separator, $out);
            }

            return '';
        });

        return $hbs;
    }),

    'Bigwhoop\Trumpet\Commands\CommandExecutionContext' => DI\object(),

    'Bigwhoop\Trumpet\Commands\CommandHandler' => DI\factory(function (DIC $c) {
        $handler = new CommandHandler();
        $handler->registerCommand($c->get('Bigwhoop\Trumpet\Commands\CodeCommand'));
        $handler->registerCommand($c->get('Bigwhoop\Trumpet\Commands\ExecCommand'));
        $handler->registerCommand($c->get('Bigwhoop\Trumpet\Commands\IncludeCommand'));
        $handler->registerCommand($c->get('Bigwhoop\Trumpet\Commands\ImageCommand'));
        $handler->registerCommand($c->get('Bigwhoop\Trumpet\Commands\WikiCommand'));

        return $handler;
    }),

    'PhpParser\PrettyPrinterAbstract' => DI\object('\PhpParser\PrettyPrinter\Standard'),

    'PhpParser\Lexer' => DI\object('PhpParser\Lexer\Emulative'),

    'PhpParser\ParserAbstract' => DI\object('PhpParser\Parser')
        ->constructor(DI\link('PhpParser\Lexer')),

    'PhpParser\NodeTraverserInterface' => DI\object('PhpParser\NodeTraverser')
        ->method('addVisitor', DI\link('PhpParser\NodeVisitor\NameResolver')),
];

$builder = new DI\ContainerBuilder();
$builder->addDefinitions(new DI\Definition\Source\ArrayDefinitionSource($definitions));
//$builder->useAnnotations(false);

return $builder->build();
