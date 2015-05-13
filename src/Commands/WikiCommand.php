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

class WikiCommand implements Command
{
    const ENDPOINT = 'https://en.wikipedia.org/w/api.php';
    const QUOTE_MAX_LENGTH = 400;

    /** @var CommandExecutionContext */
    private $executionContext;

    /**
     * @param CommandExecutionContext $context
     */
    public function __construct(CommandExecutionContext $context)
    {
        $this->executionContext = $context;
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
        $maxLength = (int) $params->getSecondArgument(self::QUOTE_MAX_LENGTH);

        $cacheFile = $this->getCacheFilePath($article);
        if (is_readable($cacheFile)) {
            $summary = file_get_contents($cacheFile);

            return $this->quote($summary, $maxLength);
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

            return $this->quote($page->extract, $maxLength);
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
     * @param int    $maxLength
     * @param string $suffix
     *
     * @return string
     */
    private function quote($text, $maxLength, $suffix = ' ...')
    {
        if (mb_strlen($text) - mb_strlen($suffix) > $maxLength) {
            $text = mb_substr($text, 0, $maxLength).$suffix;
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
