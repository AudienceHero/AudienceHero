<?php

use Dunglas\DoctrineJsonOdm\Bundle\DunglasDoctrineJsonOdmBundle;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\WebServerBundle\WebServerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new Knp\Bundle\MarkdownBundle\KnpMarkdownBundle(),
            new Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new Endroid\QrCode\Bundle\EndroidQrCodeBundle(),
            new Misd\PhoneNumberBundle\MisdPhoneNumberBundle(),
            new MarcW\RssWriter\Bundle\MarcWRssWriterBundle(),
            new EWZ\Bundle\RecaptchaBundle\EWZRecaptchaBundle(),
            new ApiPlatform\Core\Bridge\Symfony\Bundle\ApiPlatformBundle(),
            new DunglasDoctrineJsonOdmBundle(),
            new Bazinga\GeocoderBundle\BazingaGeocoderBundle(),
            new Nelmio\CorsBundle\NelmioCorsBundle(),
            new Lexik\Bundle\JWTAuthenticationBundle\LexikJWTAuthenticationBundle(),
            new Enqueue\Bundle\EnqueueBundle(),
            new Http\HttplugBundle\HttplugBundle(),
            new Sylius\Bundle\MailerBundle\SyliusMailerBundle(),

            new AudienceHero\Bundle\CoreBundle\AudienceHeroCoreBundle(),
            new AudienceHero\Bundle\AcquisitionFreeDownloadBundle\AudienceHeroAcquisitionFreeDownloadBundle(),
            new AudienceHero\Bundle\ActivityBundle\AudienceHeroActivityBundle(),
            new AudienceHero\Bundle\ContactBundle\AudienceHeroContactBundle(),
            new AudienceHero\Bundle\FOSUserBundle\AudienceHeroFOSUserBundle(),
            new AudienceHero\Bundle\ContactImportCsvBundle\AudienceHeroContactImportCsvBundle(),
            new AudienceHero\Bundle\FileBundle\AudienceHeroFileBundle(),
            new AudienceHero\Bundle\ImageServerBundle\AudienceHeroImageServerBundle(),
            new AudienceHero\Bundle\MailingCampaignBundle\AudienceHeroMailingCampaignBundle(),
            new AudienceHero\Bundle\PodcastBundle\AudienceHeroPodcastBundle(),
            new AudienceHero\Bundle\PromoBundle\AudienceHeroPromoBundle(),
            new AudienceHero\Bundle\SitemapBundle\AudienceHeroSitemapBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Nelmio\Alice\Bridge\Symfony\NelmioAliceBundle();
            $bundles[] = new Fidry\AliceDataFixtures\Bridge\Symfony\FidryAliceDataFixturesBundle();
            $bundles[] = new Hautelook\AliceBundle\HautelookAliceBundle();
        }

        return $bundles;
    }

    public function getRootDir()
    {
        return __DIR__;
    }
    public function getCacheDir()
    {
        return dirname(__DIR__).'/var/cache/'.$this->getEnvironment();
    }
    public function getLogDir()
    {
        return dirname(__DIR__).'/var/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
