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

namespace AudienceHero\Bundle\CoreBundle\Behavior\Publishable\Command;

use AudienceHero\Bundle\CoreBundle\Behavior\Publishable\Publisher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PublishCommand extends Command
{
    /** @var Publisher */
    private $publisher;

    public function __construct(Publisher $publisher)
    {
        $this->publisher = $publisher;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('audiencehero:core:publish')
            ->setDescription('Publish all entities that need to be published');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->publisher->publishScheduled();
    }
}
