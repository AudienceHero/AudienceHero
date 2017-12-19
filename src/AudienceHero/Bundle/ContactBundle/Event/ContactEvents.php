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

namespace AudienceHero\Bundle\ContactBundle\Event;

/**
 * ContactEvents.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class ContactEvents
{
    const OPT_IN_CONFIRMED = 'audiencehero.contact.opt_in_confirmed';
    const OPT_IN_REQUEST = 'audiencehero.contact.opt_in_request';
    const OPT_OUT = 'audiencehero.contact.opt_out';
}
