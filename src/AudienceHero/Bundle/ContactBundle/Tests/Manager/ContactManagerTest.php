<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\ContactBundle\Tests\Manager;

use AudienceHero\Bundle\ContactBundle\Entity\Contact;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroup;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroupContact;
use AudienceHero\Bundle\ContactBundle\Manager\ContactManager;
use AudienceHero\Bundle\ContactBundle\Repository\ContactRepository;
use AudienceHero\Bundle\ContactBundle\Repository\ContactsGroupContactRepository;
use AudienceHero\Bundle\ContactBundle\Repository\Specification\ContactAndContactsGroup;
use AudienceHero\Bundle\CoreBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Happyr\DoctrineSpecification\Specification\Specification;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ContactManagerTest extends TestCase
{
    /** @var ObjectProphecy */
    private $em;
    /** @var ObjectProphecy */
    private $registry;
    /** @var ObjectProphecy */
    private $contactRepository;
    /** @var ObjectProphecy */
    private $contactsGroupContactRepository;
    /** @var Contact */
    private $contact;
    /** @var User */
    private $owner;
    /** @var ContactsGroup */
    private $contactsGroup;

    public function setUp()
    {
        $this->registry = $this->prophesize(RegistryInterface::class);
        $this->contactRepository = $this->prophesize(ContactRepository::class);
        $this->contactsGroupContactRepository = $this->prophesize(ContactsGroupContactRepository::class);
        $this->em = $this->prophesize(EntityManager::class);

        $this->owner = new User();
        $this->contact = new Contact();
        $this->contactsGroup = new ContactsGroup();
    }

    private function getInstance(): ContactManager
    {
        return new ContactManager($this->registry->reveal(), $this->contactRepository->reveal(), $this->contactsGroupContactRepository->reveal());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Contact must have an owner
     */
    public function testAddThrowsExceptionIfContactHasNoOwner()
    {
        $this->getInstance()->add($this->contact);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Contact must have either an email or a phone number attached
     */
    public function testAddThrowsAnExceptionIfContactHasNoPhoneOrEmail()
    {
        $this->contact->setOwner($this->owner);
        $this->getInstance()->add($this->contact);
    }

    public function testAddNonExistingContact()
    {
        $this->contactRepository->matchOneOrNullResult(Argument::type(Specification::class))
            ->shouldBeCalled()
            ->willReturn(null);

        $this->em->persist($this->contact)->shouldBeCalled();
        $this->registry->getManager()->willReturn($this->em->reveal());

        $this->contact->setOwner($this->owner);
        $this->contact->setEmail('foo@example.com');
        $this->getInstance()->add($this->contact);
    }

    public function testAddExistingContact()
    {
        $existing = new Contact();
        $existing->setName('Jane Doe');

        $this->contactRepository->matchOneOrNullResult(Argument::type(Specification::class))
            ->shouldBeCalled()
            ->willReturn($existing);

        $this->em->persist($this->contact)->shouldNotBeCalled();
        $this->registry->getManager()->willReturn($this->em->reveal());

        $this->contact->setOwner($this->owner);
        $this->contact->setEmail('foo@example.com');
        $this->contact->setPhone('112');
        $returned = $this->getInstance()->add($this->contact);
        $this->assertSame($existing, $returned);
        $this->assertSame('112', $existing->getPhone());
    }

    public function testAddToGroup()
    {
        $this->contact->setOwner($this->owner);
        $this->contactsGroup->setOwner($this->owner);

        $this->em->persist(Argument::that(function(ContactsGroupContact $cgc) {
            return $cgc->getContact() === $this->contact && $cgc->getGroup() === $this->contactsGroup;
        }))->shouldBeCalled();
        $this->registry->getManager()->willReturn($this->em->reveal());
        $cgc = $this->getInstance()->addToGroup($this->contact, $this->contactsGroup);
        $this->assertInstanceOf(ContactsGroupContact::class, $cgc);
        $this->assertSame($this->contact, $cgc->getContact());
        $this->assertSame($this->contactsGroup, $cgc->getGroup());
    }

    public function testAddToGroupWithExistingContactsAndGroupsReturnsExistingRelation()
    {
        $contact = $this->prophesize(Contact::class);
        $contact->getId()->shouldBeCalled()->willReturn('id1');
        $group = $this->prophesize(ContactsGroup::class);
        $group->getId()->shouldBeCalled()->willReturn('id2');

        $cgc = new ContactsGroupContact();
        $this->contactsGroupContactRepository->matchOneOrNullResult(
            Argument::type(ContactAndContactsGroup::class)
        )->shouldBeCalled()->willReturn($cgc);

        $this->assertSame($cgc, $this->getInstance()->addToGroup($contact->reveal(), $group->reveal()));
    }

    public function testAddToGroupWithExistingContactsAndGroupsReturnsNewRelation()
    {
        $contact = $this->prophesize(Contact::class);
        $contact->getId()->shouldBeCalled()->willReturn('id1');
        $contact->getOwner()->willReturn($this->owner);

        $group = $this->prophesize(ContactsGroup::class);
        $group->getId()->shouldBeCalled()->willReturn('id2');
        $group->getOwner()->willReturn($this->owner);

        $this->contactsGroupContactRepository->matchOneOrNullResult(Argument::type(ContactAndContactsGroup::class))
             ->shouldBeCalled()
             ->willReturn(null);

        $this->em->persist(Argument::that(function(ContactsGroupContact $cgc) use ($contact, $group) {
            return $cgc->getContact() === $contact->reveal() && $cgc->getGroup() === $group->reveal();
        }))->shouldBeCalled();
        $this->registry->getManager()->willReturn($this->em->reveal());

        $cgc = $this->getInstance()->addToGroup($contact->reveal(), $group->reveal());
        $this->assertInstanceOf(ContactsGroupContact::class, $cgc);
        $this->assertSame($contact->reveal(), $cgc->getContact());
        $this->assertSame($group->reveal(), $cgc->getGroup());
    }
}
