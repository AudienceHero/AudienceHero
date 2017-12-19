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

namespace AudienceHero\Bundle\FileBundle\Action;

use AudienceHero\Bundle\FileBundle\Entity\File;
use AudienceHero\Bundle\FileBundle\Filesystem\FileSystemInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * DownloadAction.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class DownloadAction
{
    /**
     * @var FileSystemInterface
     */
    private $fileSystem;

    public function __construct(FileSystemInterface $fileSystem)
    {
        $this->fileSystem = $fileSystem;
    }

    /**
     * @Route("/files/{id}/download", name="files_download")
     * @Method({"GET"})
     * @Security("has_role('ROLE_USER') and is_granted('IS_OWNER', file)")
     */
    public function __invoke(File $file): RedirectResponse
    {
        return new RedirectResponse(
            $this->fileSystem->resolveUrl($file)
        );
    }
}
