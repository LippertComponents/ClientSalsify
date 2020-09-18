<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 9/26/18
 * Time: 4:15 PM
 */

namespace LCI\Salsify\Helpers;

use LCI\Salsify\Exceptions\ImageTransformationException;

class ImageTransformation
{
    /** @var array  */
    protected $parameters = [];

    /** @var string  */
    protected $base_url = '';

    /** @var string  */
    protected $extension = 'jpg';

    /** @var string  */
    protected $file_name = '';

    /** @var string  */
    protected $measurement_type = '';

    /** @var bool  */
    protected $valid = false;

    /**
     * ImageTransformation constructor.
     * @param string $url
     * @throws ImageTransformationException
     */
    public function __construct(string $url)
    {
        $this->makeBaseUrl($url);

        if (!$this->valid) {
            throw new ImageTransformationException('Image URL is invalid ' . $url);
        }
    }

    /**
     * @return string ~ The jpg URL
     */
    public function getUrl()
    {
        return $this->buildUrl();
    }

    /**
     * @return string ~ as ai
     */
    public function getURLasAI()
    {
        $this->extension = 'ai';
        return $this->getUrl();
    }

    /**
     * @return string ~ as jpg
     */
    public function getURLasJPG()
    {
        $this->extension = 'jpg';
        return $this->getUrl();
    }

    /**
     * @return string ~ the gif URL
     */
    public function getURLasGIF()
    {
        $this->extension = 'gif';
        return $this->getUrl();
    }

    /**
     * @return string ~ the png URL
     */
    public function getURLasPNG()
    {
        $this->extension = 'png';
        return $this->getUrl();
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        // if the url provided is a valid Salsify URL
        return $this->valid;
    }

    /**
     * a:b ratio (eg 16:9)  or decimal representing the ratio of the width divided by the height (e.g., 1.33 or 2.5)
     *
     * Used with a crop mode (scale, fill, lfill, pad, lpad, mpad or crop) to determine how the image is adjusted to the
     * new dimensions. Can also be used with either width or height to create a proportional transform.
     * The other dimension is then automatically updated to maintain the given aspect ratio.
     *
     * @param string $ar
     * @return $this
     */
    public function setAspectRatio(string $ar)
    {
        $this->parameters['ar'] = $ar;
        return $this;
    }

    /**
     * @param string $mode ~ Options:
     *   scale ~ (default) Change the size of the image to match the given width & height. All original image parts will be visible but might be stretched or shrunken.
     *   fill ~ Create an image with the exact given width and height while retaining original proportions. Uses only a portion of the original image that fills the given dimensions.
     *   lfill ~ Same as the 'fill' mode but doesn't expand the image if your requested dimensions are larger than the original image's.
     *   fit ~ Change image size to fit in the given width & height while retaining original proportions. All original image parts are visible. Both width and height dimensions of the transformed image must not exceed the specified width & height.
     *   mfit ~ Scale the image up to fit the given width & height while retaining original proportions.
     *   limit ~ Used for creating an image that does not exceed the given width or height.
     *   pad ~ Resize the image to fill the given width & height while retaining original proportions. Padding will be added if the original image proportions do not match the required ones.
     *   lpad ~ Same as the 'pad' mode but doesn't scale the image up if your requested dimensions are larger than the original image's.
     *   mpad ~ Same as the 'pad' mode but doesn't scale the original image.
     *   crop ~ Used to extract a given width & height out of the original image. The original proportions are retained and so is the size of the graphics.
     *   thumb ~ Generate a thumbnail using face detection in combination with the 'face' or 'faces' gravity.
     *
     * @return $this
     */
    public function setCropMode($mode='scale')
    {
        $this->parameters['c'] = $mode;
        return $this;
    }

    /**
     * @param float ~ $percentage Control how much of the original image surrounding the face to keep
     * @param string $mode ~ crop or thumb cropping modes with face detection (Default: 1.0).
     *
     * @return $this
     */
    public function cropAroundFace($percentage, $mode='crop')
    {
        $this->parameters['z'] = $percentage;
        return $this->setCropMode($mode);
    }

    /**
     * @param string $direction ~ Decides which part of the image to keep
     *   north_west ~ North west corner (top left).
     *   north ~ North center part (top center).
     *   north_east ~ North east corner (top right).
     *   west ~ Middle west part (left).
     *   center(default) ~ The center of the image.
     *   east ~ Middle east part (right).
     *   south_west ~ South west corner (bottom left).
     *   south ~ South center part (bottom center).
     *   south_east ~ South east corner (bottom right).
     *   xy_center ~ Set the crop's center of gravity to the given x & y coordinates"
     *   face ~ Automatically detects the largest face in an image and aim to make it the center of the cropped image.
     *   face (thumb) ~ Automatically detects the largest face in an image and use it to generate a face thumbnail.
     *   faces ~ Automatically detect multiple faces in an image and aim to make them the center of the cropped image.
     *   face:center ~ Same as the 'face' gravity, but with fallback to 'center' gravity instead of 'north' if no face is detected.
     *   faces:center ~ Same as the 'faces' gravity, but with fallback to 'center' gravity instead of 'north' if no face is detected.
     * @param string $mode ~  'crop', 'pad' and 'fill' For overlays, this decides where to place the overlay.
     * @return ImageTransformation
     */
    public function cropDirection($direction='center', $mode='crop')
    {
        $this->parameters['g'] = $direction;
        return $this->setCropMode($mode);
    }

    /**
     * @param int $dpi ~ Control the DPI/density of an image or when converting PDF documents to images. (Range 50-300, default 150)
     * @return $this
     */
    public function setDPI(int $dpi)
    {
        return $this->setDensity($dpi);
    }

    /**
     * @param int $dpi ~ Control the DPI/density of an image or when converting PDF documents to images. (Range 50-300, default 150)
     * @return $this
     */
    public function setDensity(int $dpi)
    {
        $this->parameters['dn'] = $dpi;
        return $this;
    }

    /**
     * @param string $dpr ~ Deliver the image in the specified device pixel ratio, or set to auto. For auto,
     *  deliver the image in a resolution that automatically matches the DPR (Device Pixel Ratio) setting of
     *  the user's device, rounded up to the nearest integer.
     * @return $this
     */
    public function setDevicePixelRatio($dpr='auto')
    {
        $this->parameters['dpr'] = $dpr;
        return $this;
    }

    /**
     * @param float $height ~ 0.2
     * @return $this
     * @throws ImageTransformationException
     */
    public function setHeightInPercentage($height)
    {
        if (empty($this->measurement_type)) {
            $this->measurement_type = 'percentage';

        } elseif ($this->measurement_type !== 'percentage') {
            throw new ImageTransformationException('Cannot mix percentage and pixels for width and height');
        }

        $this->parameters['h'] = $height;
        return $this;
    }

    /**
     * @param int $height
     * @return $this
     * @throws ImageTransformationException
     */
    public function setHeightInPixels(int $height)
    {
        if (empty($this->measurement_type)) {
            $this->measurement_type = 'pixels';

        } elseif ($this->measurement_type !== 'pixels') {
            throw new ImageTransformationException('Cannot mix percentage and pixels for width and height');
        }

        $this->parameters['h'] = $height;
        return $this;
    }

    /**
     * @param float $width ~ 0.2
     * @return $this
     * @throws ImageTransformationException
     */
    public function setWidthInPercentage($width)
    {
        if (empty($this->measurement_type)) {
            $this->measurement_type = 'percentage';

        } elseif ($this->measurement_type !== 'percentage') {
            throw new ImageTransformationException('Cannot mix percentage and pixels for width and height');
        }

        $this->parameters['w'] = $width;
        return $this;
    }

    /**
     * @param int $width
     * @return $this
     * @throws ImageTransformationException
     */
    public function setWidthInPixels(int $width)
    {
        if (empty($this->measurement_type)) {
            $this->measurement_type = 'pixels';

        } elseif ($this->measurement_type !== 'pixels') {
            throw new ImageTransformationException('Cannot mix percentage and pixels for width and height');
        }

        $this->parameters['w'] = $width;
        return $this;
    }

    /**
     * @param int $x ~ Horizontal position for custom-coordinates based cropping, overlay placement and certain region related effects.
     * @return $this
     */
    public function setXHorizontal(int $x)
    {
        $this->parameters['x'] = $x;
        return $this;
    }

    /**
     * @param int $y ~ Vertical position for custom-coordinates based cropping and overlay placement.
     * @return $this
     */
    public function setYVertical(int $y)
    {
        $this->parameters['y'] = $y;
        return $this;
    }

    /**
     * This method allows additional params as needed that have not bee covered in the existing methods
     * @see https://help.salsify.com/help/transforming-image-files for full list
     *
     * @param string $param
     * @param string $value
     * @return $this
     */
    public function setDirectParameterValue($param, $value)
    {
        $this->parameters[$param] = $value;
        return $this;
    }

    /**
     * @param int $q ~ Control the JPG compression quality. 1 is the lowest quality and 100 is the highest.
     *  The default is the original image's quality or 90% if not available. Reducing quality generates JPG
     *  images much smaller in file size.
     * @return $this
     */
    public function setQuality(int $q=90)
    {
        $this->parameters['q'] = $q;
        return $this;
    }

    /**
     * @return string
     */
    protected function buildUrl()
    {
        $config = '';
        foreach ($this->parameters as $key => $value) {
            if (!empty($config)) {
                $config .= ',';
            }

            $config .= $key . '_' . $value;
        }

        if (!empty($config)) {
            $config .= '/';
        }

        return $this->base_url . '/' . $config . $this->file_name . '.' . $this->extension;
    }

    /**
     * @param string $url
     */
    protected function makeBaseUrl($url)
    {
        //https://images.salsify.com/image/upload/s--0hc0ERZV--/u4lc6fg1w17qzturwna3.jpg
        $this->base_url ='https://' . parse_url($url, PHP_URL_HOST);

        if ($this->base_url === 'https://images.salsify.com') {
            $this->valid = true;
        } else {
            return;
        }

        $path = parse_url($url, PHP_URL_PATH);

        $directories = explode('/', trim($path, '/'));

        $stop = 2;
        if (count($directories) < 3 ) {
            // invalid URL?
            $this->valid = false;
            return;
        }

        for ($x = 0; $x < $stop; $x++) {
            $this->base_url .= '/' . $directories[$x];
        }

        $this->file_name = pathinfo($path, PATHINFO_FILENAME);
    }

}