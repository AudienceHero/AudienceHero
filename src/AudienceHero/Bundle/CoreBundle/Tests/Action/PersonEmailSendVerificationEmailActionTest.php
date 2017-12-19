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

namespace AudienceHero\Bundle\CoreBundle\Tests\Action;

use AudienceHero\Bundle\CoreBundle\Action\PersonEmailSendVerificationEmailAction;
use AudienceHero\Bundle\CoreBundle\Bridge\Symfony\HttpFoundation\EmptyJsonLdResponse;
use AudienceHero\Bundle\CoreBundle\Entity\Person;
use AudienceHero\Bundle\CoreBundle\Entity\PersonEmail;
use AudienceHero\Bundle\CoreBundle\Mailer\Model\PersonEmailVerificationEmail;
use AudienceHero\Bundle\CoreBundle\Mailer\TransactionalMailer;
use PHPUnit\Framework\TestCase;

class PersonEmailSendVerificationEmailActionTest extends TestCase
{
    public function testAction()
    {
        $person = $this->prophesize(Person::class);
        $owner = $person->reveal();
        $pe = $this->prophesize(PersonEmail::class);
        $pe->getEmail()->willReturn('my@email.com')->shouldBeCalled();
        $pe->getOwner()->willReturn($owner)->shouldBeCalled();

        $mailer = $this->prophesize(TransactionalMailer::class);
        $data = $pe->reveal();
        $mailer->send(
            PersonEmailVerificationEmail::class,
            $owner,
            ['person_email' => $data],
            'my@email.com'
        )->shouldBeCalled();

        $action = new PersonEmailSendVerificationEmailAction($mailer->reveal());
        $response = $action($pe->reveal());
        $this->assertInstanceOf(EmptyJsonLdResponse::class, $response);
    }
}
