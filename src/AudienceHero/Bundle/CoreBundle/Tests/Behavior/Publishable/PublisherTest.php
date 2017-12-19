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

namespace AudienceHero\Bundle\CoreBundle\Tests\Behavior\Publishable;

use AudienceHero\Bundle\CoreBundle\Behavior\Publishable\PublishableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Publishable\Publisher;
use AudienceHero\Bundle\CoreBundle\Behavior\Publishable\Repository\Specification\PublishableScheduledAndReady;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory;
use Doctrine\ORM\EntityManagerInterface;
use Happyr\DoctrineSpecification\EntitySpecificationRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class PublisherTest extends TestCase
{
    /** @var ObjectProphecy */
    private $em;

    public function setUp()
    {
        $this->em = $this->prophesize(EntityManagerInterface::class);
    }

    public function testPublishDoesNotPublishEntityThatShouldNotBePublished()
    {
        $manager = new Publisher($this->em->reveal());

        $entity = $this->prophesize(PublishableInterface::class);
        $entity->isTimeForPublication()->willReturn(false)->shouldBeCalled();
        $entity->publish()->shouldNotBeCalled();
        $manager->publish($entity->reveal());
    }

    public function testPublishPublishesEntityThatShouldBePublished()
    {
        $manager = new Publisher($this->em->reveal());

        $entity = $this->prophesize(PublishableInterface::class);
        $entity->isTimeForPublication()->willReturn(true)->shouldBeCalled();
        $entity->publish()->shouldBeCalled();
        $manager->publish($entity->reveal());
    }

    public function testPublishScheduled()
    {
        $factory = $this->prophesize(ClassMetadataFactory::class);

        $object = $this->prophesize(PublishableInterface::class);
        $object->isTimeForPublication()->willReturn(true)->shouldBeCalled();
        $object->publish()->shouldBeCalled();
        $rc = new \ReflectionClass($object->reveal());

        $metadata = $this->prophesize(ClassMetadata::class);
        $metadata->getReflectionClass()->willReturn($rc)->shouldBeCalled();
        $metadata->getName()->willReturn('Anonymous')->shouldBeCalled();
        $metas = [
            $metadata->reveal(),
        ];
        $factory->getAllMetadata()->willReturn($metas)->shouldBeCalled();

        $repository = $this->prophesize(EntitySpecificationRepository::class);
        $repository->match(Argument::type(PublishableScheduledAndReady::class))
            ->willReturn([$object->reveal()])->shouldBeCalled();

        $this->em->getMetadataFactory()->willReturn($factory->reveal())->shouldBeCalled();
        $this->em->getRepository('Anonymous')->willReturn($repository->reveal())->shouldBeCalled($repository->reveal());
        $this->em->flush()->shouldBeCalled();

        $manager = new Publisher($this->em->reveal());
        $manager->publishScheduled();
    }
}
