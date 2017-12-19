<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\ContactBundle\Tests\Linkable;

use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroup;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroupForm;
use AudienceHero\Bundle\ContactBundle\Linkable\ContactsGroupFormPopulator;
use AudienceHero\Bundle\CoreBundle\Behavior\Linkable\LinkableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Linkable\LinkableInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ContactsGroupFormPopulatorTest extends TestCase
{
    /** @var ObjectProphecy */
    private $router;
    /** @var ContactsGroupFormPopulator */
    private $populator;

    public function setUp()
    {
        $this->router = $this->prophesize(UrlGeneratorInterface::class);
        $this->populator = new ContactsGroupFormPopulator($this->router->reveal());
    }

    public function testSupports()
    {
        $this->assertFalse($this->populator->supports(new class implements LinkableInterface{use LinkableEntity;}));
        $this->assertTrue($this->populator->supports(new ContactsGroupForm()));
    }

    public function testPopulate()
    {
        $this->router->generate(Argument::any(), Argument::any(), UrlGeneratorInterface::ABSOLUTE_URL)
             ->shouldBeCalled()
             ->willReturn('foobar');

        $cgf = new ContactsGroupForm();
        $this->populator->populate($cgf);
        $this->assertSame('foobar', $cgf->getURL('public'));
        $this->assertSame('foobar', $cgf->getURL('print'));
    }
}
