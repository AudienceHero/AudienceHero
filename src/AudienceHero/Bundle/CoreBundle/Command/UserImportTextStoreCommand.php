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

namespace AudienceHero\Bundle\CoreBundle\Command;

use AudienceHero\Bundle\CoreBundle\Entity\TextStore;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UserImportTextStoreCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:user:import-text-store')
            ->setDescription('Import a file into a text store')
            ->addArgument('user', InputArgument::REQUIRED, 'user email')
            ->addArgument('filepath', InputArgument::REQUIRED, 'filepath')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = $this->getContainer()->get('logger');

        $path = $input->getArgument('filepath');
        $em = $this->getContainer()->get('doctrine')->getManager();
        $user = $em->getRepository('UserBundle:User')->findOneByEmail($input->getArgument('user'));
        if (!$user) {
            throw new \InvalidArgumentException('User not found');
        }

        if (!file_exists($path)) {
            throw new \InvalidArgumentException('File not found');
        }

        $ts = new TextStore();
        $ts->setOwner($user);
        $ts->setText(file_get_contents($path));
        $ts->setContentType(mime_content_type($path));
        $em->persist($ts);
        $em->flush();

        $output->writeln($ts->getId());
    }
}
