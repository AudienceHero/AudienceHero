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

namespace AudienceHero\Bundle\FileBundle\EventListener;

use AudienceHero\Bundle\FileBundle\Entity\File;
use AudienceHero\Bundle\FileBundle\Filesystem\FileSystemInterface;
use AudienceHero\Bundle\FileBundle\Queue\FileProducer;
use Doctrine\ORM\Event\LifecycleEventArgs;

final class DoctrineEventListener
{
    /** @var FileProducer */
    private $producer;
    /**
     * @var FileSystemInterface
     */
    private $fileSystem;

    public function __construct(FileSystemInterface $fileSystem, FileProducer $producer)
    {
        $this->producer = $producer;
        $this->fileSystem = $fileSystem;
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof File) {
            $this->setRemoteUrl($entity);
        }
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof File) {
            $this->producer->filesUpload($entity);
            $this->setRemoteUrl($entity);
        }
    }

    private function setRemoteUrl(File $file)
    {
        $file->setRemoteUrl($this->fileSystem->resolveUrl($file));
    }
}
