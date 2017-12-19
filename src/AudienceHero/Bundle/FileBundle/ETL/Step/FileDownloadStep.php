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

namespace AudienceHero\Bundle\FileBundle\ETL\Step;

use AudienceHero\Bundle\FileBundle\Filesystem\FileSystemInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Http\Client\HttpClient;
use Http\Message\MessageFactory;
use Psr\Log\LoggerInterface;

/**
 * DownloadStep.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class FileDownloadStep implements StepInterface
{
    /** @var Client */
    private $client;
    /** @var LoggerInterface */
    private $logger;
    /**
     * @var MessageFactory
     */
    private $messageFactory;
    /**
     * @var FileSystemInterface
     */
    private $fileSystem;

    public function __construct(FileSystemInterface $fileSystem, MessageFactory $messageFactory, HttpClient $client, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
        $this->messageFactory = $messageFactory;
        $this->fileSystem = $fileSystem;
    }

    public function run(Context $context): void
    {
        $file = $context->getFile();
        $download = sprintf('%s.%s', tempnam(sys_get_temp_dir(), 'dl'), $file->getExtension());
        try {
            $url = $this->fileSystem->resolveUrl($file);
            $request = $this->messageFactory->createRequest('GET', $url);
            $response = $this->client->sendRequest($request);
            if (200 !== $response->getStatusCode()) {
                throw new \RuntimeException(sprintf('Cannot download file %s: %s', $file->getRemoteUrl(), $response->getBody()->getContents()));
            }
            $dest = fopen($download, 'w');
            if (false === $dest) {
                throw new \RuntimeException(sprintf('Cannot open file %s for writing', $download));
            }
            if (false === stream_copy_to_stream($response->getBody()->detach(), $dest)) {
                throw new \RuntimeException(sprintf('Failed to copy response to %s', $download));
            }
            fclose($dest);
            $context->setPath($download);
        } catch (RequestException $e) {
            $this->logger->error(sprintf('Cannot download file %s: %s. %s. %s.', $file->getRemoteUrl(), $e->getMessage(), (string) $e->getRequest()->getUri(), $e->getResponse()->getBody()));
            @unlink($download);

            return;
        } catch (\Exception $e) {
            $this->logger->error(sprintf('Cannot download file %s: %s.', $file->getRemoteUrl(), $e->getMessage()));
            @unlink($download);

            return;
        }
    }

    public function supports(Context $context): bool
    {
        if (!$context->getFile()) {
            return false;
        }

        return true;
    }

    public function getPriority(): int
    {
        return StepInterface::PRIORITY_FIRST;
    }
}
