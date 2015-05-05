<?php

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

    public function setUp()
    {
        parent::setUp();

        $this->cmd->setReturnType(ImageCommand::RETURN_TYPE_PNG);
    }

    /**
     * !image File.png.
     */
    public function testFile()
    {
        $file = $this->cmd->execute(new CommandParams('test.png'), $this->ctx);
        $this->assertImage($file, 768, 576);
    }

    /**
     * !image File.png [Width]x[Height].
     */
    public function testResizingStandard()
    {
        $file = $this->cmd->execute(new CommandParams('test.png 100x100'), $this->ctx);
        $this->assertImage($file, 100, 75);
    }

    /**
     * !image File.png [Width]x[Height] stretch.
     */
    public function testResizingStretch()
    {
        $file = $this->cmd->execute(new CommandParams('test.png 100x100 stretch'), $this->ctx);
        $this->assertImage($file, 100, 100);
    }

    /**
     * !image File.png [Width]x[Height] fit.
     */
    public function testResizingFit()
    {
        $file = $this->cmd->execute(new CommandParams('test.png 100x100 fit'), $this->ctx);
        $this->assertImage($file, 100, 100);
    }

    /**
     * !image File.png [Width]x[Height] crop [X] [Y].
     */
    public function testResizingCrop()
    {
        $file = $this->cmd->execute(new CommandParams('test.png 100x100 crop 50 50'), $this->ctx);
        $this->assertImage($file, 100, 100);
    }

    /**
     * @param string $imageData
     * @param int    $width
     * @param int    $height
     */
    private function assertImage($imageData, $width, $height)
    {
        $img = imagecreatefromstring($imageData);
        $this->assertEquals($width, imagesx($img));
        $this->assertEquals($height, imagesy($img));
    }
}
