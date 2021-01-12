<?php

namespace AvidCi\Plugins;

use AvidCi\Pipelines\RunningPipe;

class PhpUnitPlugin implements PluginInterface
{
    public function __construct(
        private ShellPlugin $shellPlugin
    ) {}

    public function test(RunningPipe $pipe, string $execLocation = 'bin/phpunit', ?string $phpunitXml = null)
    {
        if (!file_exists($execLocation) && file_exists('vendor/bin/phpunit')) {
            $execLocation = 'vendor/bin/phpunit';
        }
        $extraArgs = [];
        if (null !== $phpunitXml) {
            $extraArgs = [...$extraArgs, '--configuration', $phpunitXml];
        }

        $junitLocation = sprintf('%s/phpunit_out.xml', $pipe->getRoot());
        $this->shellPlugin->run($pipe, $execLocation, label: 'Running PHPUnit', arguments: [
            '--disallow-test-output',
            '--log-junit', $junitLocation,
            ...$extraArgs
        ]);

        $junit = simplexml_load_file($junitLocation);
    }
}
