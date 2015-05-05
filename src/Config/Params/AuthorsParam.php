<?php

/**
 * This file is part of trumpet.
 *
 * (c) Philippe Gerber
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bigwhoop\Trumpet\Config\Params;

use Bigwhoop\Trumpet\Config\Author;
use Bigwhoop\Trumpet\Config\ConfigException;
use Bigwhoop\Trumpet\Config\Presentation;

class AuthorsParam implements Param
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
            if (substr($parts[0], 0, 1) === '@') {
                $author->twitter = array_shift($parts);
            } elseif (substr($parts[0], 0, 4) === 'http') {
                $author->website = array_shift($parts);
            } else {
                break;
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
        if (!array_key_exists('name', $entry) || !is_string($entry['name'])) {
            throw new ConfigException("Author#$idx has to have a 'name' property that is a string.");
        }

        $author = new Author($entry['name']);

        if (array_key_exists('company', $entry)) {
            if (!is_string($entry['company'])) {
                throw new ConfigException("Author#$idx has to have a 'company' property that is a string.");
            }
            $author->company = $entry['company'];
        }

        if (array_key_exists('email', $entry)) {
            if (!is_string($entry['email'])) {
                throw new ConfigException("Author#$idx has to have a 'email' property that is a string.");
            }
            $author->email = $entry['email'];
        }

        if (array_key_exists('twitter', $entry)) {
            if (!is_string($entry['twitter'])) {
                throw new ConfigException("Author#$idx has to have a 'twitter' property that is a string.");
            }
            $handle = $entry['twitter'];
            if (substr($handle, 0, 1) !== '@') {
                $handle = '@'.$handle;
            }
            $author->twitter = $handle;
        }

        if (array_key_exists('website', $entry)) {
            if (!is_string($entry['website'])) {
                throw new ConfigException("Author#$idx has to have a 'website' property that is a string.");
            }
            $url = $entry['website'];
            if (substr($url, 0, 1) !== 'http') {
                $url = 'http://'.$url;
            }
            $author->website = $url;
        }

        if (array_key_exists('skype', $entry)) {
            if (!is_string($entry['skype'])) {
                throw new ConfigException("Author#$idx has to have a 'skype' property that is a string.");
            }
            $author->skype = $entry['skype'];
        }

        return $author;
    }
}
