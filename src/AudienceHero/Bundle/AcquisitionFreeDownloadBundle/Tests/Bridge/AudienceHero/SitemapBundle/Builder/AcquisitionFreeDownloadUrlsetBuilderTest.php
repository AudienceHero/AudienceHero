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

namespace AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Tests\Bridge\AudienceHero\SitemapBundle\Builder;

use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Bridge\AudienceHero\SitemapBundle\Builder\AcquisitionFreeDownloadUrlsetBuilder;
use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Entity\AcquisitionFreeDownload;
use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Repository\AcquisitionFreeDownloadRepository;
use AudienceHero\Bundle\CoreBundle\Entity\Person;
use AudienceHero\Bundle\CoreBundle\Behavior\Publishable\Repository\Specification\PublishablePublic;
use AudienceHero\Bundle\FileBundle\Entity\File;
use Prophecy\Argument;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Thepixeldeveloper\Sitemap\Urlset;

class AcquisitionFreeDownloadUrlsetBuilderTest extends \PHPUnit_Framework_TestCase
{
    /** @var ObjectProphecy */
    private $repository;
    /** @var ObjectProphecy */
    private $router;

    public function setUp()
    {
        $this->repository = $this->prophesize(AcquisitionFreeDownloadRepository::class);
        $this->router = $this->prophesize(UrlGeneratorInterface::class);
    }

    public function testGetName()
    {
        $builder = new AcquisitionFreeDownloadUrlsetBuilder($this->repository->reveal(), $this->router->reveal());
        $this->assertSame('acquisition-free-download', $builder->getName());
    }

    public function testBuildReturnsAnEmptyUrlset()
    {
        $this->repository->match(Argument::type(PublishablePublic::class))->willReturn([])->shouldBeCalled();
        $builder = new AcquisitionFreeDownloadUrlsetBuilder($this->repository->reveal(), $this->router->reveal());
        $urlset = $builder->build();
        $this->assertInstanceOf(Urlset::class, $urlset);
        $this->assertCount(0, $urlset->getUrls());
    }

    public function testBuild()
    {
        $person = $this->prophesize(Person::class);
        $person->getUsername()->willReturn('username')->shouldBeCalled();
        $owner = $person->reveal();

        $date1 = new \DateTime('-1 month');
        $date2 = new \DateTime('-1 week');

        $fd1 = new AcquisitionFreeDownload();
        $fd1->setOwner($owner);
        $fd1->setTitle('FD1');
        $fd1->setSlug('fd1');
        $fd1->setUpdatedAt($date1);

        $fd2 = new AcquisitionFreeDownload();
        $fd2->setOwner($owner);
        $fd2->setTitle('FD2');
        $fd2->setSlug('fd2');
        $fd2->setUpdatedAt($date2);

        $fd1Artwork = new File();
        $fd1Artwork->setRemoteUrl('http://www.example.com/fd1.png');
        $fd1->setArtwork($fd1Artwork);

        $fd2Artwork = new File();
        $fd2Artwork->setRemoteUrl('http://www.example.com/fd2.png');
        $fd2->setArtwork($fd2Artwork);

        $this->router->generate('acquisition_free_downloads_listen', [
            'username' => 'username',
            'slug' => 'fd1',
        ], UrlGeneratorInterface::ABSOLUTE_URL)
            ->willReturn('http://username.example.com/fd1')
            ->shouldBeCalled()
        ;

        $this->router->generate('acquisition_free_downloads_listen', [
            'username' => 'username',
            'slug' => 'fd2',
        ], UrlGeneratorInterface::ABSOLUTE_URL)
            ->willReturn('http://username.example.com/fd2')
            ->shouldBeCalled()
        ;

        $this->router->generate('audience_hero_img_show_alt', [
            'url' => 'http%3A%2F%2Fwww.example.com%2Ffd1.png',
            'size' => '600x0',
        ], UrlGeneratorInterface::ABSOLUTE_URL)
            ->willReturn('http://img.example.com/?url=http%3A%2F%2Fwww.example.com%2Ffd1.png&size=600x0')
            ->shouldBeCalled()
        ;

        $this->router->generate('audience_hero_img_show_alt', [
            'url' => 'http%3A%2F%2Fwww.example.com%2Ffd2.png',
            'size' => '600x0',
        ], UrlGeneratorInterface::ABSOLUTE_URL)
            ->willReturn('http://img.example.com/?url=http%3A%2F%2Fwww.example.com%2Ffd2.png&size=600x0')
            ->shouldBeCalled()
        ;

        $entities = [$fd1, $fd2];
        $this->repository->match(Argument::type(PublishablePublic::class))->willReturn($entities)->shouldBeCalled();

        $builder = new AcquisitionFreeDownloadUrlsetBuilder($this->repository->reveal(), $this->router->reveal());
        $urlset = $builder->build();
        $this->assertInstanceOf(Urlset::class, $urlset);
        $this->assertCount(2, $urlset->getUrls());

        /** @var Url $first */
        $first = $urlset->getUrls()[0];
        $this->assertSame('http://username.example.com/fd1', $first->getLoc());
        $this->assertSame($date1->format('c'), $first->getLastMod());
        $this->assertSame('http://img.example.com/?url=http%3A%2F%2Fwww.example.com%2Ffd1.png&size=600x0', $first->getSubelements()[0]->getLoc());
        $this->assertSame('FD1', $first->getSubelements()[0]->getCaption());

        /** @var Url $second */
        $second = $urlset->getUrls()[1];
        $this->assertSame('http://username.example.com/fd2', $second->getLoc());
        $this->assertSame($date2->format('c'), $second->getLastMod());
        $this->assertSame('http://img.example.com/?url=http%3A%2F%2Fwww.example.com%2Ffd2.png&size=600x0', $second->getSubelements()[0]->getLoc());
        $this->assertSame('FD2', $second->getSubelements()[0]->getCaption());
    }
}
