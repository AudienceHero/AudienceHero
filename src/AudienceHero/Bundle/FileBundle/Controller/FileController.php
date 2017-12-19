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

namespace AudienceHero\Bundle\FileBundle\Controller;

use AudienceHero\Bundle\AudioClipMakerBundle\Domain\AudioClipMaker;
use AudienceHero\Bundle\AudioClipMakerBundle\Form\FileAudioClipMakerType;
use AudienceHero\Bundle\FileBundle\Entity\File;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class FileController extends Controller
{
    const FLASH_SUCCESS_CREATING_VIDEO = 'flash.success.creating_video_of_audio_file_in_background';
    const FLASH_SUCCESS_CREATING_AUDIO_CLIP = 'flash.success.creating_audio_clip_of_audio_file_in_background';
    const FLASH_SUCCESS_FILE_DELETED = 'flash.success.file_was_deleted';
    const FLASH_ERROR_NOT_ENOUGH_FUNDS_TO_CREATE_VIDEO = 'flash.error.not_enough_funds_to_create_video';

    /**
     * @Route("/files/{id}/audio-clip-maker", name="files_audio_clip_maker")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_USER') and is_granted('IS_OWNER', file) and file.isAudio()")
     */
    public function audioClipMakerAction(Request $request, File $file)
    {
        $audioClipMaker = new AudioClipMaker();
        $audioClipMaker->setFile($file);

        $form = $this->createForm(FileAudioClipMakerType::class, $audioClipMaker);

        if ($form->handleRequest($request)->isValid()) {
            $this->get('app.publisher')->filesAudioClipMaker($file, $audioClipMaker->getStart(), $audioClipMaker->getDuration());

            $this->addFlashSuccess(self::FLASH_SUCCESS_CREATING_AUDIO_CLIP);

            return $this->redirect($this->generateUrl('files_show', ['id' => $file->getId()]));
        }

        return $this->render('file/audio_clip_maker.html.twig', [
            'file' => $file,
            'form' => $form->createView(),
        ]);
    }
}
