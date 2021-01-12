<?php

namespace AvidCi\Plugins;

use AvidCi\ControlExceptions\HaltWithFailureException;
use AvidCi\Pipelines\RunningPipe;

class ShellPlugin implements PluginInterface
{
    /**
     * This is the base plugin for most simple operations. As we're using PHP8 you can specify the parameters out of
     * order. For example:
     * ```
     * $shellPlugin->run(
     *   pipe: $pipe,
     *   command: $command,
     *   showOutput: true
     * )
     * ```
     *
     * @param RunningPipe $pipe The currently running pipe
     * @param string $command The Command to run (e.g. ls)
     * @param string|null $label The label to show in the UI (e.g. List directory)
     * @param array $arguments A list of arguments to send through (e.g. ['-a', '-l'])
     * @param bool $showOutput Set to `true` to print the output lines to the UI
     * @param int $expectedReturn Use if you are expecting a different response code (e.g. 1)
     * @return array Returns the output lines to be processed by the pipeline or other plugins
     */
    public function run(
        RunningPipe $pipe,
        string $command,
        ?string $label = null,
        array $arguments = [],
        bool $showOutput = false,
        int $expectedReturn = 0
    ): array {
        $pipe->step(__CLASS__, __METHOD__, func_get_args());

        if (!$label) {
            $label = $command;
        }

        $pipe->outputLine(sprintf($label));

        $cleanArguments = fn (string $arg) => escapeshellarg($arg);
        exec(trim(sprintf('%s %s', $command, implode(' ', array_map($cleanArguments, $arguments)))), $output, $actualReturn);

        if ($showOutput) {
            $pipe->outputLine(implode(PHP_EOL, $output));
        }
        $pipe->log($command, [
            'arguments' => $arguments,
            'lines' => $output,
            'rc' => $actualReturn
        ]);

        if ($actualReturn !== $expectedReturn) {
            throw new HaltWithFailureException(sprintf('Incorrect exit code. Expected %d, got %d', $expectedReturn, $actualReturn));
        }

        return $output;
    }
}
