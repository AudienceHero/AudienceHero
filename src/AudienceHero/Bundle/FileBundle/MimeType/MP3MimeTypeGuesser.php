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

namespace AudienceHero\Bundle\FileBundle\MimeType;

use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesserInterface;

/**
 * MP3MimeTypeGuesser.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class MP3MimeTypeGuesser implements MimeTypeGuesserInterface
{
    private $cmd;

    /**
     * Constructor.
     *
     * The $cmd pattern must contain a "%s" string that will be replaced
     * with the file name to guess.
     *
     * The command output must start with the mime type of the file.
     *
     * @param string $cmd The command to run to get the mime type of a file
     */
    public function __construct($cmd = 'file %s 2>/dev/null')
    {
        $this->cmd = $cmd;
    }

    /**
     * Returns whether this guesser is supported on the current OS.
     *
     * @return bool
     */
    public static function isSupported()
    {
        return '\\' !== DIRECTORY_SEPARATOR && function_exists('passthru') && function_exists('escapeshellarg');
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public function guess($path)
    {
        if (!self::isSupported()) {
            return;
        }

        ob_start();

        passthru(sprintf($this->cmd, escapeshellarg($path)), $return);
        if ($return > 0) {
            ob_end_clean();

            return;
        }

        $type = trim(ob_get_clean());
        if (false !== strpos($type, 'Audio file with ID3')) {
            return 'audio/mpeg';
        }
    }
}
