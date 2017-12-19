<?php
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Filter;
use SebastianBergmann\CodeCoverage\Report\Clover;
use SebastianBergmann\CodeCoverage\Report\Html\Facade;

/**
 * RoboFile.
 *
 * @author Marc Weistroff <marc@weistroff.net> 
 */
class RoboFile extends \Robo\Tasks
{
    const PHPDBG_EXEC = 'phpdbg -qrr';
    const PHP_EXEC = 'php';

    private $phpExec = self::PHP_EXEC;

    public function testMergeCoverage()
    {
        $finder = new Symfony\Component\Finder\Finder();
        $finder->name('coverage-*.php')->files()->in([__DIR__.'/build/phpunit', __DIR__.'/build/behat/report']);
        $reports = [];

        $filter = new Filter();
        $global = new CodeCoverage(null, $filter);
        $id = 'ah-test-suite';
        $global->setTests([$id => ['size' => 'unknown', 'status' => null]]);

        foreach ($finder as $file) {
            $cc = require $file;
            $global->merge($cc);
        }

        $writer = new Clover();
        $writer->process($global, __DIR__.'/build/coverage-clover');
        $writer = new Facade();
        $writer->process($global, __DIR__.'/build/coverage-html');
    }

    public function testClean()
    {
        if (file_exists('build')) {
            $this->taskCleanDir(['build'])->run();
        }

        if (file_exists('var/cache/test')) {
            $this->taskCleanDir(['var/cache/test'])->run();
        }

        $this->taskFilesystemStack()
             ->mkdir('build/behat/debug')
             ->mkdir('build/behat/report')
             ->mkdir('build/phpunit')
             ->run();

        $this->taskExec('./dbreset test')
             ->run();

    }

    public function testBehat($features = '')
    {
        $res = $this->taskExec("$this->phpExec ./vendor/bin/behat --tags '~@javascript' -f pretty -f junit")
             ->arg('--colors')
             ->arg($features)
             ->run();

        return $res->wasSuccessful();
    }

    public function testBehatJavascript($features = '')
    {
        $this->taskExec(' /Applications/Google\ Chrome\ Canary.app/Contents/MacOS/Google\ Chrome\ Canary')
             ->arg('--headless')
            ->arg('--disable-gpu')
             ->arg('--remote-debugging-port=9222')
            ->arg('--remote-debugging-host=0.0.0.0')
            ->background()
            ->run()
        ;

        $this->taskExec("$this->phpExec bin/console app:route:export-js --env=test")
             ->run();

        $this->taskExec("$this->phpExec bin/console server:run localhost:8888")
                ->arg('--ansi')
                ->arg('--quiet')
                ->arg('--env=test')
                ->background()
                ->run();

        $res = $this->taskExec("$this->phpExec vendor/bin/behat --verbose --tags '@javascript' -f pretty -f junit")
             ->arg('--colors')
             ->arg($features)
             ->run();

        return $res->wasSuccessful();
    }

    public function testPhpunit()
    {
        $cmd = "$this->phpExec ./vendor/bin/phpunit";
        if (getenv("NOCC") == 1) {
            $cmd = sprintf('%s --no-coverage', $cmd);
        }

        $res = $this->taskExec($cmd)->run();

        return $res->wasSuccessful();
    }

    public function testAll()
    {
        $this->testClean();
        $res1 = $this->testBehat();
        $res2 = $this->testPhpunit();
        $res3 = $this->testBehatJavascript();
        $this->testMergeCoverage();

        return $res1 && $res2 && $res3;
    }
}
