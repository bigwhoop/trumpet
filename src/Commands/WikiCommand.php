<?php

/**
 * This file is part of trumpet.
 *
 * (c) Philippe Gerber
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bigwhoop\Trumpet\Commands;

use Bigwhoop\SentenceBreaker\SentenceBreaker;

class WikiCommand implements Command
{
    const ENDPOINT = 'https://en.wikipedia.org/w/api.php';

    /** @var CommandExecutionContext */
    private $executionContext;
    
    /** @var SentenceBreaker */
    private $sentenceBreaker;

    /**
     * @param CommandExecutionContext $context
     * @param SentenceBreaker $sentenceBreaker
     */
    public function __construct(CommandExecutionContext $context, SentenceBreaker $sentenceBreaker)
    {
        $this->executionContext = $context;
        $this->sentenceBreaker  = $sentenceBreaker;
    }

    /**
     * {@inheritdoc}
     */
    public function getToken()
    {
        return 'wiki';
    }

    /**
     * {@inheritdoc}
     */
    public function execute(CommandParams $params, CommandExecutionContext $executionContext)
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

    /**
     * @param string $article
     *
     * @return string
     */
    private function getCacheFilePath($article)
    {
        $tmpDir = $this->executionContext->ensureTempDirectory();
        $tmpFile = $tmpDir.'/summary-'.md5($article).'.txt';

        return $tmpFile;
    }

    /**
     * @param string $text
     * @param int    $numSentences
     *
     * @return string
     */
    private function quote($text, $numSentences)
    {
        if ($numSentences > 0) {
            $sentences = $this->sentenceBreaker->split($text);
            $text = join(' ', array_slice($sentences, 0, $numSentences));
        }

        return "> $text";
    }

    /**
     * @param string $article
     *
     * @return string
     */
    private function buildURL($article)
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
