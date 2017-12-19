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

namespace AudienceHero\Bundle\CoreBundle\Tests\Serializer\Normalizer;

use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableEntity;
use AudienceHero\Bundle\CoreBundle\Entity\Person;
use AudienceHero\Bundle\CoreBundle\Entity\User;
use AudienceHero\Bundle\CoreBundle\Queue\Message;
use AudienceHero\Bundle\CoreBundle\Serializer\Normalizer\MessageNormalizer;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Serializer\Serializer;

class MessageNormalizerTest extends TestCase
{
    /** @var ObjectProphecy */
    private $registry;
    /** @var ObjectProphecy */
    private $propertyAccessor;
    /** @var ObjectProphecy */
    private $em;

    public function setUp()
    {
        $this->em = $this->prophesize(EntityManagerInterface::class);
        $this->registry = $this->prophesize(RegistryInterface::class);
        $this->propertyAccessor = new PropertyAccessor();
    }

    public function testSupportsNormalization()
    {
        $normalizer = new MessageNormalizer($this->registry->reveal(), $this->propertyAccessor);

        $message = new class() extends Message {
        };
        $this->assertTrue($normalizer->supportsNormalization($message));
        $this->assertFalse($normalizer->supportsNormalization($normalizer));
    }

    public function testSupportsDenormalization()
    {
        $normalizer = new MessageNormalizer($this->registry->reveal(), $this->propertyAccessor);

        $message = new class() extends Message {
        };
        $this->assertTrue($normalizer->supportsDenormalization('{}', Message::class));
        $this->assertTrue($normalizer->supportsDenormalization('{}', $message));
        $this->assertFalse($normalizer->supportsDenormalization('{}', MessageNormalizer::class));
    }

    private function getMessageInstance()
    {
        return new class() extends Message {
            use OwnableEntity;

            private $scalar = 'my_scalar';

            public function getScalar(): string
            {
                return $this->scalar;
            }

            public function setScalar(string $scalar)
            {
                $this->scalar = $scalar;
            }
        };
    }

    public function testNormalize()
    {
        $message = $this->getMessageInstance();
        $ownerMock = $this->prophesize(Person::class);
        $ownerMock->getId()->shouldBeCalled()->willReturn('my_id');
        $owner = $ownerMock->reveal();
        $message->setOwner($owner);

        $this->registry->getManagerForClass(get_class($owner))
                       ->shouldBeCalled()
                       ->willReturn($this->em->reveal());

        $normalizer = new MessageNormalizer($this->registry->reveal(), $this->propertyAccessor);
        $serializer = new Serializer([$normalizer]);
        $normalizer->setSerializer($serializer);

        $data = $normalizer->normalize($message);
        $this->assertSame([
            'scalar' => 'my_scalar',
            'owner' => [
                'type' => get_class($owner),
                'id' => 'my_id',
            ],
        ], $data);
    }

    public function testDenormalize()
    {
        $data = [
            'scalar' => 'my_scalar',
            'owner' => [
                'type' => User::class,
                'id' => 'my_id',
            ],
        ];

        $owner = new User();

        $this->em->find(User::class, 'my_id')
                 ->shouldBeCalled()
                 ->willReturn($owner);

        $this->registry->getManagerForClass(get_class($owner))
            ->shouldBeCalled()
            ->willReturn($this->em->reveal());

        $normalizer = new MessageNormalizer($this->registry->reveal(), $this->propertyAccessor);
        $serializer = new Serializer([$normalizer]);
        $normalizer->setSerializer($serializer);

        $result = $normalizer->denormalize($data, get_class($this->getMessageInstance()));
        $this->assertInstanceOf(get_class($this->getMessageInstance()), $result);
        $this->assertSame('my_scalar', $result->getScalar());
        $this->assertSame($owner, $result->getOwner());
    }
}
