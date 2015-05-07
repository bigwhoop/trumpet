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

use Intervention\Image\Constraint;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;

class ImageCommand implements Command
{
    const RETURN_TYPE_FILE     = 'file';
    const RETURN_TYPE_DATA_URL = 'data-url';
    const RETURN_TYPE_PNG      = 'png';

    /** @var ImageManager */
    private $imageManager;

    /** @var CommandExecutionContext */
    private $executionContext;

    /** @var string */
    private $returnType = self::RETURN_TYPE_FILE;

    /**
     * @param ImageManager            $manager
     * @param CommandExecutionContext $context
     */
    public function __construct(ImageManager $manager, CommandExecutionContext $context)
    {
        $this->imageManager     = $manager;
        $this->executionContext = $context;
    }

    /**
     * @param string $type
     */
    public function setReturnType($type)
    {
        $this->returnType = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getToken()
    {
        return 'image';
    }

    /**
     * {@inheritdoc}
     */
    public function execute(CommandParams $params, CommandExecutionContext $executionContext)
    {
        $fileName = $params->getFirstArgument();

        if (!$executionContext->hasFileInWorkingDirectory($fileName)) {
            throw new ExecutionFailedException("File '$fileName' does not exist.");
        }

        $img = $this->imageManager->make($executionContext->getPathOfFileInWorkingDirectory($fileName));

        if (!$params->hasSecondArgument()) {
            return $this->returnImage($img, $params);
        }

        $matches = [];
        if (!preg_match('#(\d+)x(\d+)#', $params->getSecondArgument(), $matches)) {
            throw new ExecutionFailedException("Second argument must be in format WIDTHxHEIGHT. For example 100x50, 0x50 or 100x0, but not 0x0.");
        }

        $width  = (int) $matches[1];
        $height = (int) $matches[2];

        if ($width === 0 && $height === 0) {
            throw new ExecutionFailedException("Either the width or the height must be greater than zero.");
        }

        if ($width === 0) {
            $width = null;
        }

        if ($height === 0) {
            $height = null;
        }

        switch ($params->getThirdArgument()) {
            case 'stretch':
                $img->resize($width, $height);
                break;

            case 'fit':
                $img->fit($width, $height);
                break;

            case 'crop':
                $x = (int) $params->getArgument(3);
                $y = (int) $params->getArgument(4);
                $img->crop($width, $height, $x === 0 ? null : $x, $y === 0 ? null : $y);
                break;

            default:
                $img->resize($width, $height, function (Constraint $constraint) {
                    $constraint->aspectRatio();
                });
                break;
        }

        return $this->returnImage($img, $params);
    }

    /**
     * @param Image         $img
     * @param CommandParams $params
     *
     * @return string
     *
     * @throws ExecutionFailedException
     */
    private function returnImage(Image $img, CommandParams $params)
    {
        switch ($this->returnType) {
            case self::RETURN_TYPE_FILE:
                $tmpDir = $this->executionContext->getWorkingDirectory().'/_tmp';
                if (!is_dir($tmpDir)) {
                    mkdir($tmpDir, 0755, true);
                }
                $tmpFile = $tmpDir.'/'.md5($params->getParams()).'.png';
                $img->encode('png')->save($tmpFile);

                return '<img src="/_tmp/'.basename($tmpFile).'">';

            case self::RETURN_TYPE_DATA_URL:
                return '![Image]('.$img->encode('data-url')->getEncoded().')';

            case self::RETURN_TYPE_PNG:
                return $img->encode('png')->getEncoded();

            default:
                throw new ExecutionFailedException("Invalid return type '{$this->returnType}' detected.");
        }
    }
}
