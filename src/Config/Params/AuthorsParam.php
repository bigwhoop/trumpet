<?php declare(strict_types=1);

namespace Bigwhoop\Trumpet\Config\Params;

use Bigwhoop\Trumpet\Config\Author;
use Bigwhoop\Trumpet\Config\ConfigException;
use Bigwhoop\Trumpet\Config\Presentation;

final class AuthorsParam implements Param
{
    /**
     * {@inheritdoc}
     */
    public function parse($value, Presentation $presentation)
    {
        if (!is_array($value)) {
            throw new ConfigException('Authors have to be an array.');
        }

        $authors = [];

        foreach (array_values($value) as $idx => $entry) {
            if (is_array($entry)) {
                $authors[] = $this->parseArrayEntry($idx, $entry);
            } elseif (is_string($entry)) {
                $authors[] = $this->parseStringEntry($idx, $entry);
            } else {
                throw new ConfigException("Author#$idx must either be an array or a string.");
            }
        }

        $presentation->authors = $authors;
    }

    /**
     * @param int    $idx
     * @param string $entry
     *
     * @return Author
     */
    private function parseStringEntry($idx, $entry)
    {
        $parts = array_map('trim', explode(',', $entry));
        $name = array_shift($parts);

        $author = new Author($name);

        if (!empty($parts)) {
            $author->company = array_shift($parts);
        }

        while (!empty($parts)) {
            if (filter_var($parts[0], FILTER_VALIDATE_EMAIL)) {
                $author->email = array_shift($parts);
            } elseif (substr($parts[0], 0, 1) === '@') {
                $author->twitter = array_shift($parts);
            } elseif (substr($parts[0], 0, 4) === 'http') {
                $author->website = array_shift($parts);
            } else {
                array_shift($parts);
            }
        }

        return $author;
    }

    /**
     * @param int   $idx
     * @param array $entry
     *
     * @return Author
     *
     * @throws ConfigException
     */
    private function parseArrayEntry($idx, array $entry)
    {
        $fnCreateIsStringValidator = function ($idx, $key) {
            return function ($value) use ($idx, $key) {
                if (!is_string($value)) {
                    throw new ConfigException("Author#$idx must have a '$key' property that is a string.");
                }
            };
        };

        $name = $this->pickFromArray($entry, 'name', '', $fnCreateIsStringValidator($idx, 'name'));

        $author = new Author($name);

        $author->company = $this->pickFromArray($entry, 'company', '', $fnCreateIsStringValidator($idx, 'company'));
        $author->email   = $this->pickFromArray($entry, 'email', '', $fnCreateIsStringValidator($idx, 'email'));
        $author->twitter = $this->formatTwitterHandle($this->pickFromArray($entry, 'twitter', '', $fnCreateIsStringValidator($idx, 'twitter')));
        $author->website = $this->formatWebsite($this->pickFromArray($entry, 'website', '', $fnCreateIsStringValidator($idx, 'website')));
        $author->email   = $this->pickFromArray($entry, 'email', '', $fnCreateIsStringValidator($idx, 'email'));
        $author->skype   = $this->pickFromArray($entry, 'skype', '', $fnCreateIsStringValidator($idx, 'skype'));

        return $author;
    }

    /**
     * @param array    $params
     * @param string   $key
     * @param string   $defaultValue
     * @param callable $validator
     *
     * @return mixed
     *
     * @throws ConfigException
     */
    private function pickFromArray(array $params, $key, $defaultValue, callable $validator)
    {
        if (!array_key_exists($key, $params)) {
            return $defaultValue;
        }

        $value = $params[$key];
        $validationResult = $validator($value);

        if (is_string($validationResult) && $validationResult !== '') {
            throw new ConfigException($validationResult);
        }

        return $value;
    }

    /**
     * @param string $handle
     *
     * @return string
     */
    private function formatTwitterHandle($handle)
    {
        if ($handle != '' && substr($handle, 0, 1) !== '@') {
            $handle = '@'.$handle;
        }

        return $handle;
    }

    /**
     * @param string $url
     *
     * @return string
     */
    private function formatWebsite($url)
    {
        if ($url != '' && substr($url, 0, 4) !== 'http') {
            $url = 'http://'.$url;
        }

        return $url;
    }
}
