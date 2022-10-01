<?php declare(strict_types=1);
use PHPUnit\Event\Facade;
use PHPUnit\Runner\CodeCoverage;
use PHPUnit\TextUI\Configuration\Registry;
use PHPUnit\TextUI\XmlConfiguration\Loader;
use PHPUnit\TextUI\XmlConfiguration\PhpHandler;
use PHPUnit\TestRunner\TestResult\PassedTests;

// php://stdout does not obey output buffering. Any output would break
// unserialization of child process results in the parent process.
if (!defined('STDOUT')) {
    define('STDOUT', fopen('php://temp', 'w+b'));
    define('STDERR', fopen('php://stderr', 'wb'));
}

{iniSettings}
ini_set('display_errors', 'stderr');
set_include_path('{include_path}');

$composerAutoload = {composerAutoload};
$phar             = {phar};

ob_start();

if ($composerAutoload) {
    require_once $composerAutoload;

    define('PHPUNIT_COMPOSER_INSTALL', $composerAutoload);
} else if ($phar) {
    require $phar;
}

function __phpunit_run_isolated_test()
{
    $dispatcher = Facade::initForIsolation(
        PHPUnit\Event\Telemetry\HRTime::fromSecondsAndNanoseconds(
            {offsetSeconds},
            {offsetNanoseconds}
        )
    );

    require_once '{filename}';

    if ({collectCodeCoverageInformation}) {
        CodeCoverage::activate(
            unserialize('{codeCoverageFilter}'),
            {pathCoverage}
        );

        if ({cachesStaticAnalysis}) {
            CodeCoverage::instance()->cacheStaticAnalysis(unserialize('{codeCoverageCacheDirectory}'));
        }
    }

    $test = new {className}('{name}');
    $test->setData('{dataName}', unserialize('{data}'));
    $test->setDependencyInput(unserialize('{dependencyInput}'));
    $test->setInIsolation(TRUE);

    ob_end_clean();

    $test->run();

    $output = '';

    if (!$test->hasExpectationOnOutput()) {
        $output = $test->output();
    }

    ini_set('xdebug.scream', '0');

    // Not every STDOUT target stream is rewindable
    @rewind(STDOUT);

    if ($stdout = @stream_get_contents(STDOUT)) {
        $output         = $stdout . $output;
        $streamMetaData = stream_get_meta_data(STDOUT);

        if (!empty($streamMetaData['stream_type']) && 'STDIO' === $streamMetaData['stream_type']) {
            @ftruncate(STDOUT, 0);
            @rewind(STDOUT);
        }
    }

    print serialize(
        [
            'testResult'    => $test->result(),
            'codeCoverage'  => {collectCodeCoverageInformation} ? CodeCoverage::instance() : null,
            'numAssertions' => $test->numberOfAssertionsPerformed(),
            'output'        => $output,
            'events'        => $dispatcher->flush(),
            'passedTests'   => PassedTests::instance()
        ]
    );
}

$configurationFilePath = '{configurationFilePath}';

if ('' !== $configurationFilePath) {
    $configuration = (new Loader)->load($configurationFilePath);

    (new PhpHandler)->handle($configuration->php());

    unset($configuration);
}

function __phpunit_error_handler($errno, $errstr, $errfile, $errline)
{
   return true;
}

set_error_handler('__phpunit_error_handler');

{constants}
{included_files}
{globals}

restore_error_handler();

if (isset($GLOBALS['__PHPUNIT_BOOTSTRAP'])) {
    require_once $GLOBALS['__PHPUNIT_BOOTSTRAP'];

    unset($GLOBALS['__PHPUNIT_BOOTSTRAP']);
}

Registry::loadFrom('{serializedConfiguration}');

__phpunit_run_isolated_test();
