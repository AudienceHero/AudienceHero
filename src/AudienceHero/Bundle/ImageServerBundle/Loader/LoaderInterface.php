<?php

namespace AudienceHero\Bundle\ImageServerBundle\Loader;

use Imagine\Imagick\Image;

/**
 * LoaderInterface.
 *
 * @author Marc Weistroff <marc@weistroff.net> 
 */
interface LoaderInterface
{
    public function load($url): Image;
}
