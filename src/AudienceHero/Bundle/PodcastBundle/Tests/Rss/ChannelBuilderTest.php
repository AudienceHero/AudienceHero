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

namespace AudienceHero\Bundle\PodcastBundle\Tests\Rss;

use AppBundle\Entity\PodcastChannel;
use AppBundle\Entity\PodcastEpisode;
use AudienceHero\Bundle\CoreBundle\Entity\User;
use AudienceHero\Bundle\FileBundle\Entity\File;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class ChannelBuilderTest extends TestCase
{
//    private $builder;
//
//    public function setUp()
//    {
//        $client = static::createClient();
//        $this->builder = $client->getContainer()->get('app.rss.channel_builder');
//    }
//
//    private function setId($object, $id)
//    {
//        $rc = new \ReflectionClass($object);
//        $rp = $rc->getProperty('id');
//        $rp->setAccessible(true);
//        $rp->setValue($object, $id);
//    }
//
//    public function testFromPodcastChannel()
//    {
//        $user = new User();
//        $user->setUsername('foobar');
//
//        $pc = new PodcastChannel();
//        $this->setId($pc, 'channel_id');
//
//        $channelArtwork = new File();
//        $channelArtwork->setUri('https://www.example.com/channel.jpeg');
//
//        $episodeArtwork = new File();
//        $episodeArtwork->setUri('https://www.example.com/episode.jpeg');
//
//        $enclosure = new File();
//        $this->setId($enclosure, 'enclosure_id');
//        $enclosure->setSize(1024);
//        $enclosure->setExtension('mp3');
//        $enclosure->setDuration(124);
//        $enclosure->setUri('https://www.example.com/enclosure.mp3');
//        $enclosure->setContentType('audio/mp3');
//
//        $pc->setOwner($user);
//        $pc->setLanguage('fr');
//        $pc->setTitle('Channel Title');
//        $pc->setSlug('channel-title');
//        $pc->setSubtitle('Channel Subtitle');
//        $pc->setDescription('Channel Description');
//        $pc->setCopyright('Channel Copyright');
//        $pc->setAuthor('Channel Author');
//        $pc->setArtwork($channelArtwork);
//        $pc->setCategory('Music');
//        $pc->setItunesOwnerEmail('john.owner@example.com');
//        $pc->setItunesOwnerName('John Owner');
//        $pc->setIsExplicit(true);
//        $pc->setItunesBlock(true);
//        $pc->setIsComplete(true);
//
//        $pe = new PodcastEpisode();
//        $this->setId($pe, 'episode_id');
//        $pe->setChannel($pc);
//        $pe->setTitle('Episode Title');
//        $pe->setSlug('episode-title');
//        $pe->setSubtitle('Episode Subtitle');
//        $pe->setDescription('Episode Description');
//        $pe->setAuthor('john.episode_author@example.com');
//        $pe->setPublishedAt(new \DateTime('2016-07-29 18:00:00'));
//        $pe->setIsExplicit(true);
//        $pe->setItunesBlock(true);
//        $pe->setArtwork($episodeArtwork);
//        $pe->setFile($enclosure);
//
//        $channel = $this->builder->fromPodcastChannel($pc, new ArrayCollection([$pe]));
//        $this->assertInstanceOf('MarcW\RssWriter\Extension\Core\Channel', $channel);
//        $this->assertSame('Channel Title', $channel->getTitle());
//        $this->assertSame('Channel Description', $channel->getDescription());
//        $this->assertSame('http://foobar.localhost:8888/app_test.php/podcasts/channel-title', $channel->getLink());
//        $this->assertSame('Channel Copyright', $channel->getCopyright());
//        $this->assertSame('https://www.audiencehero.org', $channel->getGenerator());
//        $this->assertSame('fr', $channel->getLanguage());
//
//        $this->assertLessThanOrEqual(new \DateTime('now'), $channel->getLastBuildDate());
//
//        $extensions = $channel->getExtensions();
//        $this->assertCount(2, $extensions);
//        $this->assertInstanceOf('MarcW\RssWriter\Extension\Itunes\ItunesChannel', $extensions[0]);
//        $this->assertInstanceOf('MarcW\RssWriter\Extension\Atom\AtomLink', $extensions[1]);
//
//        $itunesChannel = $extensions[0];
//        $this->assertSame('Channel Subtitle', $itunesChannel->getSubtitle());
//        $this->assertSame('Channel Description', $itunesChannel->getSummary());
//        $this->assertSame('Channel Author', $itunesChannel->getAuthor());
//        $this->assertTrue($itunesChannel->getBlock());
//        $this->assertTrue($itunesChannel->getExplicit());
//        $this->assertTrue($itunesChannel->getComplete());
//        $this->assertInstanceOf('MarcW\RssWriter\Extension\Itunes\ItunesOwner', $itunesChannel->getOwner());
//        $this->assertSame('john.owner@example.com', $itunesChannel->getOwner()->getEmail());
//        $this->assertSame('John Owner', $itunesChannel->getOwner()->getName());
//        $this->assertSame(['Music'], $itunesChannel->getCategories());
//        $this->assertSame('http://img.localhost:8888/app_test.php/0x1500/square-center/https%253A%252F%252Fwww.example.com%252Fchannel.jpeg', $itunesChannel->getImage());
//
//        $atomLink = $extensions[1];
//        $this->assertSame('http://foobar.localhost:8888/app_test.php/podcasts/channel-title.xml', $atomLink->getHref());
//        $this->assertSame('self', $atomLink->getRel());
//        $this->assertNull($atomLink->getHreflang());
//        $this->assertNull($atomLink->getType());
//        $this->assertNull($atomLink->getTitle());
//
//        $this->assertCount(1, $channel->getItems());
//        $item = $channel->getItems()[0];
//        $this->assertInstanceOf('MarcW\RssWriter\Extension\Core\Item', $item);
//
//        $this->assertSame('Episode Title', $item->getTitle());
//        $this->assertSame('http://foobar.localhost:8888/app_test.php/podcasts/channel-title/episode-title', $item->getLink());
//        $this->assertSame('Episode Description', $item->getDescription());
//        $this->assertSame('john.episode_author@example.com', $item->getAuthor());
//        $this->assertSame($pe->getPublishedAt(), $item->getPubDate());
//
//        $enclosure = $item->getEnclosure();
//        $this->assertInstanceOf('MarcW\RssWriter\Extension\Core\Enclosure', $enclosure);
//        $this->assertSame('http://foobar.localhost:8888/app_test.php/podcasts/channel-title/episode-title.mp3', $enclosure->getUrl());
//        $this->assertSame(1024, $enclosure->getLength());
//        $this->assertSame('audio/mp3', $enclosure->getType());
//
//        $guid = $item->getGuid();
//        $this->assertInstanceOf('MarcW\RssWriter\Extension\Core\Guid', $guid);
//        $this->assertTrue($guid->getIsPermaLink());
//        $this->assertSame('http://foobar.localhost:8888/app_test.php/podcasts/channel-title/episode-title', $guid->getGuid());
//
//        $extensions = $item->getExtensions();
//        $this->assertCount(1, $extensions);
//        $this->assertInstanceOf('MarcW\RssWriter\Extension\Itunes\ItunesItem', $extensions[0]);
//        $itunesItem = $extensions[0];
//
//        $this->assertSame('Episode Subtitle', $itunesItem->getSubtitle());
//        $this->assertSame('02:04', $itunesItem->getDuration());
//        $this->assertSame('Episode Description', $itunesItem->getSummary());
//        $this->assertSame('john.episode_author@example.com', $itunesItem->getAuthor());
//        $this->assertSame('http://img.localhost:8888/app_test.php/0x1500/square-center/https%253A%252F%252Fwww.example.com%252Fepisode.jpeg', $itunesItem->getImage());
//        $this->assertTrue($itunesItem->getExplicit());
//        $this->assertTrue($itunesItem->getBlock());
//    }
}
