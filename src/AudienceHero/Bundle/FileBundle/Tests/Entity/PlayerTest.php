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

namespace AudienceHero\Bundle\FileBundle\Tests\Entity;

use AudienceHero\Bundle\CoreBundle\Entity\User;
use AudienceHero\Bundle\FileBundle\Entity\Player;
use AudienceHero\Bundle\FileBundle\Entity\PlayerTrack;
use PHPUnit\Framework\TestCase;

class PlayerTest extends TestCase
{
    public function testSetOwnerSetTracksOwnerAsWell()
    {
        $track = new PlayerTrack();
        $player = new Player();
        $player->setTracks([$track]);
        $owner = new User();
        $this->assertNull($player->getOwner());
        $this->assertNull($track->getOwner());
        $player->setOwner($owner);
        $this->assertSame($owner, $player->getOwner());
        $this->assertSame($owner, $track->getOwner());
    }
}
