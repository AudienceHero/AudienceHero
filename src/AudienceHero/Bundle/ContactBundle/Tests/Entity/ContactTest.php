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

namespace AudienceHero\Bundle\ContactBundle\Tests\Entity;

use AudienceHero\Bundle\ContactBundle\Entity\Contact;
use PHPUnit\Framework\TestCase;

class ContactTest extends TestCase
{
    public function testSalutationName()
    {
        $c = new Contact();
        $c->setName('Marc Weistroff');
        $this->assertEquals('Marc Weistroff', $c->getSalutationName());

        $c->setSalutationName('Marc');
        $this->assertEquals('Marc', $c->getSalutationName());

        $c->setSalutationName('');
        $this->assertEquals('Marc Weistroff', $c->getSalutationName());

        $c->setSalutationName(null);
        $this->assertEquals('Marc Weistroff', $c->getSalutationName());
    }

    public function testSetCountry()
    {
        $c = new Contact();
        $c->setCountry(null);
        $this->assertEquals(null, $c->getCountry());

        $c->setCountry('France');
        $this->assertEquals('FR', $c->getCountry());

        $c->setCountry('france');
        $this->assertEquals('FR', $c->getCountry());
    }

    public function testSetEmail()
    {
        $c = new Contact();
        $c->setEmail('foobar@example.com');
        $this->assertEquals('foobar@example.com', $c->getEmail());

        $c = new \AudienceHero\Bundle\ContactBundle\Entity\Contact();
        $c->setEmail('       FOOBAR@example.com');
        $this->assertEquals('foobar@example.com', $c->getEmail());

        $c = new Contact();
        $c->setEmail("\t\t FOOBAR@example.com\t    \t     ");
        $this->assertEquals('foobar@example.com', $c->getEmail());

        $c = new \AudienceHero\Bundle\ContactBundle\Entity\Contact();
        $c->setEmail("\t\t <FOOBAR@example.com>>\t    \t     ");
        $this->assertEquals('foobar@example.com', $c->getEmail());

        $c = new Contact();
        $c->setEmail('     FOOBAR@example.com>>\\');
        $this->assertEquals('foobar@example.com', $c->getEmail());
    }
}
