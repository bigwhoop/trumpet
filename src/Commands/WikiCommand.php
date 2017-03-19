<?php declare(strict_types=1);

namespace Bigwhoop\Trumpet\Commands;

use Bigwhoop\SentenceBreaker\SentenceBreaker;

final class WikiCommand implements Command
{
    const ENDPOINT = 'https://en.wikipedia.org/w/api.php';

    /** @var CommandExecutionContext */
    private $executionContext;
    
    /** @var SentenceBreaker */
    private $sentenceBreaker;

    public function __construct(CommandExecutionContext $context, SentenceBreaker $sentenceBreaker)
    {
        $this->executionContext = $context;
        $this->sentenceBreaker  = $sentenceBreaker;
    }

    public function getToken(): string
    {
        return 'wiki';
    }
    
    public function execute(CommandParams $params, CommandExecutionContext $executionContext): string
    {
        $article = $params->getFirstArgument();
        $numSentences = (int) $params->getSecondArgument(0);

        $cacheFile = $this->getCacheFilePath($article);
        if (is_readable($cacheFile)) {
            $summary = file_get_contents($cacheFile);

            return $this->quote($summary, $numSentences);
        }

        $url = $this->buildURL($article);
        $response = file_get_contents($url);

        $data = json_decode($response);
        if (!$data) {
            throw new ExecutionFailedException('Wikipedia API response could not be decoded. Request URL: '.$url.'. Response: '.var_export($response, true));
        }

        foreach ($data->query->pages as $page) {
            if (isset($page->missing)) {
                continue;
            }

            file_put_contents($cacheFile, $page->extract);

            return $this->quote($page->extract, $numSentences);
        }

        throw new ExecutionFailedException("Failed to query for Wikipedia article '$article'. Request URL: $url");
    }
    
    private function getCacheFilePath(string $article): string
    {
        $tmpDir = $this->executionContext->ensureTempDirectory();
        
        return $tmpDir.'/summary-'.md5($article).'.txt';
    }
    
    private function quote(string $text, int $numSentences): string
    {
        if ($numSentences > 0) {
            $sentences = $this->sentenceBreaker->split($text);
            $text = join(' ', array_slice($sentences, 0, $numSentences));
        }

        return "> $text";
    }
    
    private function buildURL(string $article): string
    {
        $params = [
            'format'      => 'json',
            'action'      => 'query',
            'prop'        => 'extracts',
            'exintro'     => '',
            'explaintext' => '',
            'titles'      => $article,
        ];

        return self::ENDPOINT.'?'.http_build_query($params, null, '&', PHP_QUERY_RFC3986);
    }
}
