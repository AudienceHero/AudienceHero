<?php

namespace AudienceHero\Bundle\ImageServerBundle\Loader;

use Imagine\Imagick\Image;
use Imagine\Imagick\Imagine;

/**
 * StreamLoader.
 *
 * @author Marc Weistroff <marc@weistroff.net> 
 */
class StreamLoader implements LoaderInterface
{
    public function load($url): Image
    {
        $imagine = new Imagine();
        $image = $imagine->open($url);

        return $image;
    }
}
