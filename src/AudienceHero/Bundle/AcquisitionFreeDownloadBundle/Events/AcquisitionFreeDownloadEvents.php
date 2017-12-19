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

namespace AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Events;

/**
 * AcquisitionFreeDownloadEvents.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
final class AcquisitionFreeDownloadEvents
{
    const HIT = 'audiencehero/acquisition_free_download.hit';
    const UNLOCK = 'audiencehero/acquisition_free_download.unlock';
}
