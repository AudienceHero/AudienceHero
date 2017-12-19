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

namespace AudienceHero\Bundle\PodcastBundle\Tests\Bridge\AudienceHero\SitemapBundle;

use AudienceHero\Bundle\CoreBundle\Entity\Person;
use AudienceHero\Bundle\CoreBundle\Behavior\Publishable\PublishableInterface;
use AudienceHero\Bundle\FileBundle\Entity\File;
use AudienceHero\Bundle\PodcastBundle\Bridge\AudienceHero\SitemapBundle\PodcastChannelUrlsetBuilder;
use AudienceHero\Bundle\PodcastBundle\Entity\PodcastChannel;
use AudienceHero\Bundle\PodcastBundle\Repository\PodcastChannelRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Thepixeldeveloper\Sitemap\Urlset;

class PodcastChannelUrlsetBuilderTest extends TestCase
{
    /** @var ObjectProphecy */
    private $repository;
    /** @var ObjectProphecy */
    private $router;

    public function setUp()
    {
        $this->repository = $this->prophesize(PodcastChannelRepository::class);
        $this->router = $this->prophesize(UrlGeneratorInterface::class);
    }

    public function testGetName()
    {
        $builder = new PodcastChannelUrlsetBuilder($this->repository->reveal(), $this->router->reveal());
        $this->assertSame('podcast-channel', $builder->getName());
    }

    public function testBuildReturnsAnEmptyUrlset()
    {
        $this->repository->findByPrivacy(PublishableInterface::PRIVACY_PUBLIC)->willReturn([])->shouldBeCalled();
        $builder = new PodcastChannelUrlsetBuilder($this->repository->reveal(), $this->router->reveal());
        $urlset = $builder->build();
        $this->assertInstanceOf(Urlset::class, $urlset);
        $this->assertCount(0, $urlset->getUrls());
    }

    public function testBuild()
    {
        $person = $this->prophesize(Person::class);
        $person->getEmail()->willReturn('username@example.com')->shouldBeCalled();
        $person->getUsername()->willReturn('username')->shouldBeCalled();
        $owner = $person->reveal();

        $date1 = new \DateTime('-1 month');
        $date2 = new \DateTime('-1 week');

        $ch1 = new PodcastChannel();
        $ch1->setOwner($owner);
        $ch1->setTitle('CH1');
        $ch1->setSlug('ch1');
        $ch1->setUpdatedAt($date1);

        $ch2 = new PodcastChannel();
        $ch2->setOwner($owner);
        $ch2->setTitle('CH2');
        $ch2->setSlug('ch2');
        $ch2->setUpdatedAt($date2);

        $ch1Artwork = new File();
        $ch1Artwork->setRemoteUrl('http://www.example.com/ch1.png');
        $ch1->setArtwork($ch1Artwork);

        $ch2Artwork = new File();
        $ch2Artwork->setRemoteUrl('http://www.example.com/ch2.png');
        $ch2->setArtwork($ch2Artwork);

        $this->router->generate('podcast_channels_listen', [
            'username' => 'username',
            'slug' => 'ch1',
        ], UrlGeneratorInterface::ABSOLUTE_URL)
            ->willReturn('http://username.example.com/podcasts/ch1')
            ->shouldBeCalled()
        ;

        $this->router->generate('podcast_channels_listen', [
            'username' => 'username',
            'slug' => 'ch2',
        ], UrlGeneratorInterface::ABSOLUTE_URL)
            ->willReturn('http://username.example.com/podcasts/ch2')
            ->shouldBeCalled()
        ;

        $this->router->generate('audience_hero_img_show_alt', [
            'url' => 'http%3A%2F%2Fwww.example.com%2Fch1.png',
            'size' => '600x0',
        ], UrlGeneratorInterface::ABSOLUTE_URL)
            ->willReturn('http://img.example.com/?url=http%3A%2F%2Fwww.example.com%2Fch1.png&size=600x0')
            ->shouldBeCalled()
        ;

        $this->router->generate('audience_hero_img_show_alt', [
            'url' => 'http%3A%2F%2Fwww.example.com%2Fch2.png',
            'size' => '600x0',
        ], UrlGeneratorInterface::ABSOLUTE_URL)
            ->willReturn('http://img.example.com/?url=http%3A%2F%2Fwww.example.com%2Fch2.png&size=600x0')
            ->shouldBeCalled()
        ;

        $entities = [$ch1, $ch2];
        $this->repository->findByPrivacy(PublishableInterface::PRIVACY_PUBLIC)->willReturn($entities)->shouldBeCalled();

        $builder = new PodcastChannelUrlsetBuilder($this->repository->reveal(), $this->router->reveal());
        $urlset = $builder->build();
        $this->assertInstanceOf(Urlset::class, $urlset);
        $this->assertCount(2, $urlset->getUrls());

        /** @var Url $first */
        $first = $urlset->getUrls()[0];
        $this->assertSame('http://username.example.com/podcasts/ch1', $first->getLoc());
        $this->assertSame($date1->format('c'), $first->getLastMod());
        $this->assertSame('http://img.example.com/?url=http%3A%2F%2Fwww.example.com%2Fch1.png&size=600x0', $first->getSubelements()[0]->getLoc());
        $this->assertSame('CH1', $first->getSubelements()[0]->getCaption());

        /** @var Url $second */
        $second = $urlset->getUrls()[1];
        $this->assertSame('http://username.example.com/podcasts/ch2', $second->getLoc());
        $this->assertSame($date2->format('c'), $second->getLastMod());
        $this->assertSame('http://img.example.com/?url=http%3A%2F%2Fwww.example.com%2Fch2.png&size=600x0', $second->getSubelements()[0]->getLoc());
        $this->assertSame('CH2', $second->getSubelements()[0]->getCaption());
    }
}
