<?php
/**
 * Created by PhpStorm.
 * User: marc
 * Date: 07/06/2017
 * Time: 09:40
 */

namespace AudienceHero\Bundle\ImageServerBundle\Tests\Loader;

use Imagine\Imagick\Image;
use AudienceHero\Bundle\ImageServerBundle\Loader\StreamLoader;
use PHPUnit\Framework\TestCase;

class StreamLoaderTest extends TestCase
{
    public function testLoad()
    {
        $loader = new StreamLoader();
        $image = $loader->load(__DIR__.'/../../Resources/fixtures/300.png');

        $this->assertInstanceOf('Imagine\Imagick\Image', $image);
        $box = $image->getSize();
        $this->assertSame(300, $box->getHeight());
        $this->assertSame(300, $box->getWidth());
    }
}
