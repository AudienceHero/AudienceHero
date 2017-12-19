<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Filter;
use SebastianBergmann\CodeCoverage\Report\PHP;
use Symfony\Component\HttpKernel\KernelInterface;
use Behat\Mink\Driver\Selenium2Driver;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\BeforeFeatureScope;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends RawMinkContext implements Context, KernelAwareContext
{
    /**
     * @var
     */
    private $kernel;

    /**
     * @var
     */
    private $page;

    /**
     * @var
     */
    private $driver;

    public static $cover;
    public static $coverName;

    /**
     * Initializes context.
     *
     * Every scenario gets it's own context object.
     * You can also pass arbitrary arguments to the context constructor through behat.yml.
     */
    public function __construct()
    {
    }

    /**
     * @beforeFeature
     */
    public static function beforeFeature(BeforeFeatureScope $scope)
    {
        if (getenv('AUDIENCE_HERO_CODE_COVERAGE') == 1) {
            $filter = new Filter();
            $whiteListDir = __DIR__.'/../../../src/*';
            $filter->addDirectoryToWhitelist($whiteListDir);
            $filter->removeDirectoryFromWhiteList(sprintf('%s/Tests', $whiteListDir));
            $filter->removeDirectoryFromWhiteList(sprintf('%s/DataFixtures', $whiteListDir));
            self::$cover = new CodeCoverage(null, $filter);
            self::$coverName = sprintf('%s:%s', $scope->getFeature()->getFile(), $scope->getFeature()->getLine());
            self::$cover->start(self::$coverName);
        }
    }

    /**
     * @afterFeature
     */
    public static function afterFeature()
    {
        if (getenv('AUDIENCE_HERO_CODE_COVERAGE') == 1) {
            static::$cover->stop();

            $writer = new PHP();
            $writer->process(static::$cover, sprintf(__DIR__.'/../../../build/behat/report/coverage-%s.php', sha1(self::$coverName)));
        }
    }

    /**
     * @beforeScenario
     */
    public function setUp(BeforeScenarioScope $scope)
    {
        $this->driver = $this->getSession()->getDriver();
        $this->page = $this->getSession()->getPage();

        if ($this->getSession()->getDriver() instanceof Selenium2Driver) {
            $this->getSession()->resizeWindow(1440, 900, 'current');
        }
    }

    /**
     * Sets HttpKernel instance.
     * This method will be automatically called by Symfony2Extension ContextInitializer.
     *
     * @param KernelInterface $kernel
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @Given /^I am logged in as "([^"]*)"$/
     */
    public function iAmConnectedAs($username)
    {
        $this->visitPath('/logout');
        $this->visitPath('/login');
        $this->assertSession()->pageTextContains('security.login.remember_me');
        $this->page->fillField('username', $username);
        $this->page->fillField('password', 'foobar');
        $this->page->pressButton('security.login.submit');
        $this->assertSession()->pageTextNotContains('security.login.remember_me');
    }

    /**
     * @When :username confirms its account
     */
    public function userConfirmsItsAccount($username)
    {
        $em = $this->kernel->getContainer()->get('doctrine')->getManager();
        $user = $em->getRepository('UserBundle:User')->findOneByUsername($username);
        if (!$user) {
            throw new \RuntimeException(sprintf('No user with username %s', $username));
        }

        $user->setConfirmationToken(null);
        $user->setEnabled(true);
        $em->flush();
    }


    /**
     * @When :username go to the password reset page
     */
    public function userResetsItsPassword($username)
    {
        $em = $this->kernel->getContainer()->get('doctrine')->getManager();
        $user = $em->getRepository('UserBundle:User')->findOneByUsername($username);
        if (!$user) {
            throw new \RuntimeException(sprintf('No user with username %s', $username));
        }

        $this->driver->visit('/resetting/reset/'.$user->getConfirmationToken());
    }

    /**
     * @Then I switch to the new window
     */
    public function iSwitchToNewWindow()
    {
        $windowNames = $this->getSession()->getWindowNames();
        if (count($windowNames) > 1) {
            $this->getSession()->switchToWindow($windowNames[1]);
        }
    }

    /**
     * @Then I switch to the previous window
     */
    public function iSwitchToPreviousWindow()
    {
        $windowNames = $this->getSession()->getWindowNames();
        if (count($windowNames) > 1) {
            $this->getSession()->switchToWindow($windowNames[0]);
        }
    }

    /**
     * @When /^I do not follow redirects$/
     */
    public function iDoNotFollowRedirects()
    {
        $this->driver->getClient()->followRedirects(false);
    }

    /**
     * @When /^I follow redirects$/
     */
    public function iDoFollowRedirects()
    {
        $this->driver->getClient()->followRedirects(true);
    }

    /**
     * @When I take a screenshot
     */
    public function takeAScreenshot()
    {
        $this->saveScreenshot();
    }

    /**
     * @Given I click the :arg1 element
     */
    public function iClickTheElement($selector)
    {
        $page = $this->getSession()->getPage();
        $element = $page->find('css', $selector);

        if (empty($element)) {
            throw new Exception("No html element found for the selector ('$selector')");
        }

        $element->click();
    }
}
