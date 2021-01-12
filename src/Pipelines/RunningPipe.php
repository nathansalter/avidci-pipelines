<?php

namespace AvidCi\Pipelines;

interface RunningPipe
{
    /**
     * Return the fully qualified file root of the currently running pipe project. This is useful for providing full
     * paths to plugins which require it.
     */
    public function getRoot(): string;

    /**
     * Move the pipeline forward a step. This will ensure a new step is displayed in the UI. Plugin authors can
     * implement this with the following line of code:
     * ```
     * $pipe->step(__CLASS__, __METHOD__, func_get_args());
     * ```
     *
     * @param string $plugin The class name of the Plugin
     * @param string $method The method called
     * @param array $arguments The arguments sent to the method
     * @return $this This currently running pipe
     */
    public function step(string $plugin, string $method, array $arguments): self;

    /**
     * Output a single line to the UI.
     *
     * @param string $output The string to show to the user. HTML is escaped.
     * @return $this
     */
    public function outputLine(string $output): self;

    /**
     * Add an internal log to this RunningPipe. This will be available during and after the run of this Pipe.
     *
     * @param string $message A short message to show in the log list
     * @param array $log More information to be shown if required
     * @return $this
     */
    public function log(string $message, array $log = []): self;
}
