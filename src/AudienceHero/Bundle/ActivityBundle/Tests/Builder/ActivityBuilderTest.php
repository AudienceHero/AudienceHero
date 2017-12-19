<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\ActivityBundle\Tests\Builder;

use AudienceHero\Bundle\ActivityBundle\Builder\ActivityBuilder;
use AudienceHero\Bundle\ActivityBundle\Entity\Activity;
use AudienceHero\Bundle\ActivityBundle\Repository\ActivityRepository;
use AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableInterface;
use AudienceHero\Bundle\CoreBundle\Entity\Person;
use AudienceHero\Bundle\CoreBundle\Entity\User;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class ActivityBuilderTest extends TestCase
{
    /** @var ObjectProphecy */
    private $requestStack;
    /** @var ObjectProphecy */
    private $repository;
    /** @var \DateTime */
    private $date;
    /** @var IdentifiableInterface */
    private $subject;
    /** @var Person */
    private $owner;

    public function setUp()
    {
        $this->requestStack = $this->prophesize(RequestStack::class);
        $this->repository = $this->prophesize(ActivityRepository::class);
        $this->subject = new Activity();
        $this->owner = new User();
        $this->date = new \DateTime();
    }

    private function getInstance(): ActivityBuilder
    {
        return new ActivityBuilder(
            $this->requestStack->reveal(),
            $this->repository->reveal()
        );
    }

    public function testBuildWithoutARequest()
    {
        $this->requestStack->getMasterRequest()->shouldBeCalled()->willReturn(null);
        $this->repository->persist(Argument::type(Activity::class))->shouldBeCalled();

        $activity = $this->getInstance()->build($this->date, $this->owner, 'my_type', $this->subject);
        $this->assertInstanceOf(Activity::class, $activity);
        $this->assertSame($this->owner, $activity->getOwner());
        $this->assertSame('my_type', $activity->getType());
        $this->assertContains($this->subject, $activity->getSubjects());
        $this->assertSame($this->date, $activity->getCreatedAt());
        $this->assertEmpty($activity->getRequest());
        $this->assertNull($activity->getIp());
        $this->assertNull($activity->getReferer());
        $this->assertNull($activity->getUserAgent());
    }

    public function testBuildEnrichesActivityWithRequest()
    {
        $request = new Request([], [], [], [], [], [
            'HTTP_USER_AGENT' => 'Foo/Bar',
            'HTTP_REFERER' => 'my_referer',
            'REMOTE_ADDR' => '127.0.0.1',
        ], null);
        $this->requestStack->getMasterRequest()->shouldBeCalled()->willReturn($request);
        $this->repository->persist(Argument::type(Activity::class))->shouldBeCalled();

        $activity = $this->getInstance()->build($this->date, $this->owner, 'my_type', $this->subject);
        $this->assertInstanceOf(Activity::class, $activity);
        $this->assertSame($this->owner, $activity->getOwner());
        $this->assertSame('my_type', $activity->getType());
        $this->assertContains($this->subject, $activity->getSubjects());
        $this->assertSame($this->date, $activity->getCreatedAt());
        $this->assertSame([
            'USER_AGENT' => 'Foo/Bar',
            'REFERER' => 'my_referer',
        ], $activity->getRequest());
        $this->assertSame('127.0.0.1', $activity->getIp());
        $this->assertSame('my_referer', $activity->getReferer());
        $this->assertSame('Foo/Bar', $activity->getUserAgent());
    }
}
