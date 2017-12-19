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

namespace AudienceHero\Bundle\CoreBundle\Tests\Doctrine\ORM\Helper;

use AudienceHero\Bundle\CoreBundle\Doctrine\ORM\Helper\LikeExprHelper;
use PHPUnit\Framework\TestCase;

class LikeExprHelperTest extends TestCase
{
    public function testLikeHelper()
    {
        $this->assertEquals('gloves!_pink', LikeExprHelper::makeLikeParam('gloves_pink', '%s'));
        $this->assertEquals('gloves!%pink', LikeExprHelper::makeLikeParam('gloves%pink', '%s'));
        $this->assertEquals('glo!_ves!%pink', \AudienceHero\Bundle\CoreBundle\Doctrine\ORM\Helper\LikeExprHelper::makeLikeParam('glo_ves%pink', '%s'));

        $this->assertEquals('%gloves!_pink', LikeExprHelper::makeLikeParam('gloves_pink', '%%%s'));
        $this->assertEquals('%gloves!_pink%', LikeExprHelper::makeLikeParam('gloves_pink', '%%%s%%'));
    }
}
