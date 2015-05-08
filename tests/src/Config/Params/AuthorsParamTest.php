<?php

namespace Bigwhoop\Trumpet\Tests\Config\Params;

use Bigwhoop\Trumpet\Config\Author;
use Bigwhoop\Trumpet\Config\Params\AuthorsParam;
use Bigwhoop\Trumpet\Config\Presentation;
use Bigwhoop\Trumpet\Tests\TestCase;

class AuthorsParamTest extends TestCase
{
    /**
     * @Inject
     *
     * @var AuthorsParam
     */
    private $param;

    public function testSingleLineNameOnly()
    {
        $expected = new Author('My Name');

        $this->assertAuthor($expected, 'My Name');
    }

    public function testSingeLineNameAndCompany()
    {
        $expected = new Author('My Name');
        $expected->company = 'My Company';

        $this->assertAuthor($expected, 'My Name, My Company');
    }

    public function testSingeLineNameAndCompanyAndEmail()
    {
        $expected = new Author('My Name');
        $expected->company = 'My Company';
        $expected->email = 'bla@example.org';

        $this->assertAuthor($expected, 'My Name, My Company, bla@example.org');
    }

    public function testSingeLineNameAndCompanyAndTwitter()
    {
        $expected = new Author('My Name');
        $expected->company = 'My Company';
        $expected->twitter = '@foo';

        $this->assertAuthor($expected, 'My Name, My Company, @foo');
    }

    public function testSingeLineNameAndCompanyAndWebsiteHTTP()
    {
        $expected = new Author('My Name');
        $expected->company = 'My Company';
        $expected->website = 'http://example.org';

        $this->assertAuthor($expected, 'My Name, My Company, http://example.org');
    }

    public function testSingeLineNameAndCompanyAndWebsiteHTTPS()
    {
        $expected = new Author('My Name');
        $expected->company = 'My Company';
        $expected->website = 'https://example.org';

        $this->assertAuthor($expected, 'My Name, My Company, https://example.org');
    }

    public function testSingeLineDynamicAdditionalParams()
    {
        $expected = new Author('My Name');
        $expected->company = 'My Company';
        $expected->email   = 'name@example.org';
        $expected->twitter = '@example';
        $expected->website = 'https://example.org';

        $this->assertAuthor($expected, 'My Name, My Company, @example, name@example.org, ignored part, https://example.org');
        $this->assertAuthor($expected, 'My Name, My Company, name@example.org, ignored part, @example, https://example.org');
        $this->assertAuthor($expected, 'My Name, My Company, https://example.org, @example, ignored part, name@example.org');
        $this->assertAuthor($expected, 'My Name, My Company, name@example.org, https://example.org, @example, ignored part');
    }

    public function testArray()
    {
        $expected = new Author('My Name');
        $expected->company = 'My Company';
        $expected->email   = 'name@example.org';
        $expected->twitter = '@example';
        $expected->website = 'https://example.org';
        $expected->skype   = 'example';

        $this->assertAuthor($expected, [
            'name' => 'My Name',
            'company' => 'My Company',
            'email' => 'name@example.org',
            'twitter' => '@example',
            'website' => 'https://example.org',
            'skype' => 'example',
        ]);
    }

    public function testArrayTwitterCompletion()
    {
        $expected = new Author('My Name');
        $expected->twitter = '@example';

        $this->assertAuthor($expected, [
            'name' => 'My Name',
            'twitter' => 'example',
        ]);
    }

    public function testArrayWebsiteCompletion()
    {
        $expected = new Author('My Name');
        $expected->website = 'http://example.org';

        $this->assertAuthor($expected, [
            'name' => 'My Name',
            'website' => 'example.org',
        ]);
    }

    /**
     * @param Author       $expected
     * @param string|array $paramValue
     *
     * @throws \Bigwhoop\Trumpet\Config\ConfigException
     */
    private function assertAuthor(Author $expected, $paramValue)
    {
        $presentation = new Presentation();
        $this->param->parse([$paramValue], $presentation);
        $actual = $presentation->authors;

        $this->assertEquals([$expected], $actual);
    }
}
