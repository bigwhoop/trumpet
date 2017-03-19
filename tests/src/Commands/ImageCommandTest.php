<?php declare(strict_types=1);

namespace Bigwhoop\Trumpet\Tests\Commands;

use Bigwhoop\Trumpet\Commands\CommandExecutionContext;
use Bigwhoop\Trumpet\Commands\CommandParams;
use Bigwhoop\Trumpet\Commands\ImageCommand;
use Bigwhoop\Trumpet\Tests\TestCase;

class ImageCommandTest extends TestCase
{
    /**
     * @Inject
     *
     * @var ImageCommand
     */
    private $cmd;

    /**
     * @Inject
     *
     * @var CommandExecutionContext
     */
    private $ctx;

    public function testToken()
    {
        $this->assertEquals('image', $this->cmd->getToken());
    }

    /**
     * !image File.png.
     */
    public function testFile()
    {
        $this->assertImage('test.png', 768, 576);
    }

    /**
     * !image File.png [Width]x[Height].
     */
    public function testResizingStandard()
    {
        $this->assertImage('test.png 100x100', 100, 75);
    }

    /**
     * !image File.png [Width]x[Height] stretch.
     */
    public function testResizingStretch()
    {
        $this->assertImage('test.png 100x100 stretch', 100, 100);
    }

    /**
     * !image File.png [Width]x[Height] fit.
     */
    public function testResizingFit()
    {
        $this->assertImage('test.png 100x100 fit', 100, 100);
    }

    /**
     * !image File.png [Width]x[Height] crop [X] [Y].
     */
    public function testResizingCrop()
    {
        $this->assertImage('test.png 100x100 crop 50 50', 100, 100);
    }
    
    private function assertImage(string $paramValue, int $width, int $height)
    {
        $this->cmd->setReturnType(ImageCommand::RETURN_TYPE_DATA);
        $imageData = $this->cmd->execute(new CommandParams($paramValue), $this->ctx);

        $img = imagecreatefromstring($imageData);
        $this->assertEquals($width, imagesx($img));
        $this->assertEquals($height, imagesy($img));

        $this->cmd->setReturnType(ImageCommand::RETURN_TYPE_PATH);
        $imagePath = $this->cmd->execute(new CommandParams($paramValue), $this->ctx);

        list($actualWidth, $actualHeight) = getimagesize($imagePath, $info);
        $this->assertEquals($width, $actualWidth);
        $this->assertEquals($height, $actualHeight);
    }
}
