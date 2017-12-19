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

namespace AudienceHero\Bundle\CoreBundle\Event;

/**
 * AppEvents.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
final class CoreEvents
{
    const IMPORT_POST_LOAD = 'audiencehero.core.import.post_load';
    const POST_FIXTURES_LOAD = 'audiencehero.core.post_fixtures_load';
}
