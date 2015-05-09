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
    const RETURN_TYPE_URL      = 'url';
    const RETURN_TYPE_PATH     = 'path';
    const RETURN_TYPE_DATA_URL = 'data-url';
    const RETURN_TYPE_DATA     = 'data';

    /** @var ImageManager */
    private $imageManager;

    /** @var CommandExecutionContext */
    private $executionContext;

    /** @var string */
    private $returnType = self::RETURN_TYPE_URL;

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

        list($width, $height) = $this->parseDimension($params->getSecondArgument());

        switch ($params->getThirdArgument()) {
            case 'stretch':
                $img->resize($width, $height);
                break;

            case 'fit':
                $img->fit($width, $height);
                break;

            case 'crop':
                $x = $this->filterIntOrNullArgument($params->getArgument(3));
                $y = $this->filterIntOrNullArgument($params->getArgument(4));
                $img->crop($width, $height, $x, $y);
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
     * @param string $value
     *
     * @return int|null
     */
    private function filterIntOrNullArgument($value)
    {
        $value = (int) $value;
        if ($value < 1) {
            $value = null;
        }

        return $value;
    }

    /**
     * @param string $dimension
     *
     * @return array
     */
    private function parseDimension($dimension)
    {
        $matches = [];
        if (!preg_match('#(\d+)x(\d+)#', $dimension, $matches)) {
            throw new ExecutionFailedException("Dimension argument be in format WIDTHxHEIGHT. For example 100x50, 0x50 or 100x0, but not 0x0.");
        }

        $width  = $this->filterIntOrNullArgument($matches[1]);
        $height = $this->filterIntOrNullArgument($matches[2]);

        if ($width === null && $height === null) {
            throw new ExecutionFailedException("Either the width or the height must be greater than zero.");
        }

        return [$width, $height];
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
            case self::RETURN_TYPE_URL:
                case self::RETURN_TYPE_PATH:
                $tmpDir = $this->ensureTempDirectory();
                $tmpFile = $tmpDir.'/'.md5($params->getParams()).'.png';
                $img->encode('png')->save($tmpFile);

                if ($this->returnType === self::RETURN_TYPE_PATH) {
                    return $tmpFile;
                }

                return '<img src="/_tmp/'.basename($tmpFile).'">';

            case self::RETURN_TYPE_DATA_URL:
                return '![Image]('.$img->encode('data-url')->getEncoded().')';

            case self::RETURN_TYPE_DATA:
                return $img->encode('png')->getEncoded();

            default:
                throw new ExecutionFailedException("Invalid return type '{$this->returnType}' detected.");
        }
    }

    /**
     * @return string
     */
    private function ensureTempDirectory()
    {
        $tmpDir = $this->executionContext->getWorkingDirectory().'/_tmp';
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }

        return $tmpDir;
    }
}
