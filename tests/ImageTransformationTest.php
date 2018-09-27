<?php
use LCI\Salsify\Helpers\ImageTransformation;
use LCI\Salsify\Exceptions\ImageTransformationException;

class ImageTransformationTest extends BaseTestCase
{
    /** @var string  */
    protected $test_asset_id = 'test-api-asset';

    protected $example_url = 'https://images.salsify.com/image/upload/s--0hc0ERZV--/u4lc6fg1w17qzturwna3.jpg';

    public function testInitImageTransformation()
    {
        $exception_message = '';

        try {
            /** @var ImageTransformation $asset */
            $asset = new ImageTransformation('https://my-web-server.com/some-image.jpg');

        } catch (ImageTransformationException $exception) {
            $exception_message = $exception->getMessage();
        }

        $this->assertEquals(
            'Image URL is invalid https://my-web-server.com/some-image.jpg',
            $exception_message,
            'Validation of the Image URL failed'
        );
    }

    public function testCanCropImage()
    {
        /** @var ImageTransformation $asset */
        $asset = new ImageTransformation($this->example_url);

        $this->assertEquals(
            true,
            $asset->isValid(),
            'Invalid URL '.$this->example_url
        );

        try {
            $crop_url = $asset
                ->setCropMode('crop')
                ->setHeightInPixels(500)
                ->setWidthInPixels(800)
                ->getUrl();

        } catch (ImageTransformationException $exception) {
            echo $exception->getMessage();
        }

        $this->assertEquals(
            'https://images.salsify.com/image/upload/c_crop,h_500,w_800/u4lc6fg1w17qzturwna3.jpg',
            $crop_url,
            'Crop URL creation failed'
        );

        /** @var ImageTransformation $asset */
        $asset = new ImageTransformation($this->example_url);

        try {
            $crop_url = $asset
                ->setCropMode('crop')
                ->setHeightInPercentage(0.50)
                ->setWidthInPercentage(0.80)
                ->getUrl();

        } catch (ImageTransformationException $exception) {
            echo $exception->getMessage();
        }

        $this->assertEquals(
            'https://images.salsify.com/image/upload/c_crop,h_0.5,w_0.8/u4lc6fg1w17qzturwna3.jpg',
            $crop_url,
            'Crop URL creation failed'
        );
    }

    public function testCannotUsePixelsAndPercentage()
    {
        /** @var ImageTransformation $asset */
        $asset = new ImageTransformation($this->example_url);

        $exception_message = '';

        try {
            $asset
                ->setCropMode('crop')
                ->setHeightInPercentage(0.50)
                ->setWidthInPixels(800)
                ->getUrl();

        } catch (ImageTransformationException $exception) {
            $exception_message = $exception->getMessage();
        }

        $this->assertEquals(
            'Cannot mix percentage and pixels for width and height',
            $exception_message
        );
    }

    /**
     * @depends testCanCropImage
     */
    public function testCanMakePNG()
    {
        /** @var ImageTransformation $asset */
        $asset = new ImageTransformation($this->example_url);

        $url = $asset->getURLasPNG();

        $this->assertEquals(
            'https://images.salsify.com/image/upload/u4lc6fg1w17qzturwna3.png',
            $url,
            'Image as PNG URL creation failed'
        );
    }

    /**
     * @depends testCanCropImage
     */
    public function testCanMakeGIF()
    {
        /** @var ImageTransformation $asset */
        $asset = new ImageTransformation($this->example_url);

        $url = $asset->getURLasGIF();

        $this->assertEquals(
            'https://images.salsify.com/image/upload/u4lc6fg1w17qzturwna3.gif',
            $url,
            'Image as Gif URL creation failed'
        );
    }

    /**
     * @depends testCanCropImage
     */
    public function testCanScale()
    {
        /** @var ImageTransformation $asset */
        $asset = new ImageTransformation($this->example_url);

        /**
         * Limit will set the max height/width and then keep image proportions
         */
        $crop_url = $asset
            ->setCropMode('limit')
            ->setHeightInPixels(500)
            ->setWidthInPixels(800)
            ->getUrl();

        $this->assertEquals(
            'https://images.salsify.com/image/upload/c_limit,h_500,w_800/u4lc6fg1w17qzturwna3.jpg',
            $crop_url,
            'Crop URL creation failed with mode limit'
        );

        // retain the height and width set above
        $url = $asset
            ->setCropMode('fill')
            ->getURLasJPG();

        $this->assertEquals(
            'https://images.salsify.com/image/upload/c_fill,h_500,w_800/u4lc6fg1w17qzturwna3.jpg',
            $url,
            'Image crop mode with fill URL creation failed'
        );

        // retain the height and width set above
        $url = $asset
            ->setCropMode('lfill')
            ->getURLasJPG();

        $this->assertEquals(
            'https://images.salsify.com/image/upload/c_lfill,h_500,w_800/u4lc6fg1w17qzturwna3.jpg',
            $url,
            'Image crop mode with lfill URL creation failed'
        );

        // retain the height and width set above
        $url = $asset
            ->setCropMode('fit')
            ->getURLasJPG();

        $this->assertEquals(
            'https://images.salsify.com/image/upload/c_fit,h_500,w_800/u4lc6fg1w17qzturwna3.jpg',
            $url,
            'Image crop mode with fit URL creation failed'
        );

        // retain the height and width set above
        $url = $asset
            ->setCropMode('mfit')
            ->getURLasJPG();

        $this->assertEquals(
            'https://images.salsify.com/image/upload/c_mfit,h_500,w_800/u4lc6fg1w17qzturwna3.jpg',
            $url,
            'Image crop mode with mfit URL creation failed'
        );
    }

    /**
     * @depends testCanCropImage
     */
    public function testCanPad()
    {
        /** @var ImageTransformation $asset */
        $asset = new ImageTransformation($this->example_url);

        $url = $asset
            ->setCropMode('pad')
            ->setHeightInPixels(500)
            ->setWidthInPixels(800)
            ->getURLasJPG();

        $this->assertEquals(
            'https://images.salsify.com/image/upload/c_pad,h_500,w_800/u4lc6fg1w17qzturwna3.jpg',
            $url,
            'Image crop mode with pad URL creation failed'
        );

        // retain the height and width set above
        $url = $asset
            ->setCropMode('lpad')
            ->getURLasJPG();

        $this->assertEquals(
            'https://images.salsify.com/image/upload/c_lpad,h_500,w_800/u4lc6fg1w17qzturwna3.jpg',
            $url,
            'Image crop mode with lpad URL creation failed'
        );

        // retain the height and width set above
        $url = $asset
            ->setCropMode('mpad')
            ->getURLasJPG();

        $this->assertEquals(
            'https://images.salsify.com/image/upload/c_mpad,h_500,w_800/u4lc6fg1w17qzturwna3.jpg',
            $url,
            'Image crop mode with mpad URL creation failed'
        );
    }
}