<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace AppBundle\Tests\File;

use AudienceHero\Bundle\FileBundle\MimeType\MP3MimeTypeGuesser;
use PHPUnit\Framework\TestCase;

class MP3MimeTypeGuesserTest extends TestCase
{
    public function testGuess()
    {
        $guesser = new MP3MimeTypeGuesser();
        $this->assertSame('audio/mpeg', $guesser->guess(__DIR__.'/../../Resources/fixtures/assets/Groovin.mp3'));
    }
}
