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
use AudienceHero\Bundle\PodcastBundle\Bridge\AudienceHero\SitemapBundle\PodcastEpisodeUrlsetBuilder;
use AudienceHero\Bundle\PodcastBundle\Entity\PodcastChannel;
use AudienceHero\Bundle\PodcastBundle\Entity\PodcastEpisode;
use AudienceHero\Bundle\PodcastBundle\Repository\PodcastEpisodeRepository;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Thepixeldeveloper\Sitemap\Url;
use Thepixeldeveloper\Sitemap\Urlset;

class PodcastEpisodeUrlsetBuilderTest extends \PHPUnit_Framework_TestCase
{
    /** @var ObjectProphecy */
    private $repository;
    /** @var ObjectProphecy */
    private $router;

    public function setUp()
    {
        $this->repository = $this->prophesize(PodcastEpisodeRepository::class);
        $this->router = $this->prophesize(UrlGeneratorInterface::class);
    }

    public function testGetName()
    {
        $builder = new PodcastEpisodeUrlsetBuilder($this->repository->reveal(), $this->router->reveal());
        $this->assertSame('podcast-episode', $builder->getName());
    }

    public function testBuildReturnsAnEmptyUrlset()
    {
        $this->repository->findByPrivacy(PublishableInterface::PRIVACY_PUBLIC)->willReturn([])->shouldBeCalled();
        $builder = new PodcastEpisodeUrlsetBuilder($this->repository->reveal(), $this->router->reveal());
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

        $ch1 = new PodcastChannel();
        $ch1->setOwner($owner);
        $ch1->setSlug('ch1');

        $ch2 = new PodcastChannel();
        $ch2->setOwner($owner);
        $ch2->setSlug('ch2');

        $date1 = new \DateTime('-1 month');
        $date2 = new \DateTime('-1 week');

        $ep1 = new PodcastEpisode();
        $ep1->setChannel($ch1);
        $ep1->setTitle('EP1');
        $ep1->setSlug('ep1');
        $ep1->setUpdatedAt($date1);

        $ep2 = new PodcastEpisode();
        $ep2->setChannel($ch2);
        $ep2->setTitle('EP2');
        $ep2->setSlug('ep2');
        $ep2->setUpdatedAt($date2);

        $ch1Artwork = new File();
        $ch1Artwork->setRemoteUrl('http://www.example.com/ch1.png');
        $ch1->setArtwork($ch1Artwork);

        $ep2Artwork = new File();
        $ep2Artwork->setRemoteUrl('http://www.example.com/ep2.png');
        $ep2->setArtwork($ep2Artwork);

        $this->router->generate('podcast_episodes_listen', [
            'username' => 'username',
            'slug' => 'ch1',
            'episodeSlug' => 'ep1',
        ], UrlGeneratorInterface::ABSOLUTE_URL)
            ->willReturn('http://username.example.com/podcasts/ch1/ep1')
            ->shouldBeCalled()
        ;

        $this->router->generate('podcast_episodes_listen', [
            'username' => 'username',
            'slug' => 'ch2',
            'episodeSlug' => 'ep2',
        ], UrlGeneratorInterface::ABSOLUTE_URL)
            ->willReturn('http://username.example.com/podcasts/ch2/ep2')
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
            'url' => 'http%3A%2F%2Fwww.example.com%2Fep2.png',
            'size' => '600x0',
        ], UrlGeneratorInterface::ABSOLUTE_URL)
            ->willReturn('http://img.example.com/?url=http%3A%2F%2Fwww.example.com%2Fep2.png&size=600x0')
            ->shouldBeCalled()
        ;

        $entities = [$ep1, $ep2];
        $this->repository->findByPrivacy(PublishableInterface::PRIVACY_PUBLIC)->willReturn($entities)->shouldBeCalled();

        $builder = new PodcastEpisodeUrlsetBuilder($this->repository->reveal(), $this->router->reveal());
        $urlset = $builder->build();
        $this->assertInstanceOf(Urlset::class, $urlset);
        $this->assertCount(2, $urlset->getUrls());

        /** @var Url $first */
        $first = $urlset->getUrls()[0];
        $this->assertSame('http://username.example.com/podcasts/ch1/ep1', $first->getLoc());
        $this->assertSame($date1->format('c'), $first->getLastMod());
        $this->assertSame('http://img.example.com/?url=http%3A%2F%2Fwww.example.com%2Fch1.png&size=600x0', $first->getSubelements()[0]->getLoc());
        $this->assertSame('EP1', $first->getSubelements()[0]->getCaption());

        /** @var Url $second */
        $second = $urlset->getUrls()[1];
        $this->assertSame('http://username.example.com/podcasts/ch2/ep2', $second->getLoc());
        $this->assertSame($date2->format('c'), $second->getLastMod());
        $this->assertSame('http://img.example.com/?url=http%3A%2F%2Fwww.example.com%2Fep2.png&size=600x0', $second->getSubelements()[0]->getLoc());
        $this->assertSame('EP2', $second->getSubelements()[0]->getCaption());
    }
}
