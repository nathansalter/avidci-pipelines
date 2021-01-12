<?php

namespace AvidCi\Pipelines;

class PipelineSecrets
{
    public function __construct(
        private array $secrets
    ){}

    public function getSecret(string $name, $default = null)
    {
        return $this->secrets[$name] ?? $default;
    }

    public function hasSecret(string $name): bool
    {
        return isset($this->secrets[$name]);
    }

    /**
     * Returns a subset of the secrets in this object. Useful to avoid leaking credentials to other plugins e.g.
     * ```
     * $unsafePlugin->run($pipe, $secrets->onlySecrets('foo', 'bar'));
     * ```
     *
     * @param string ...$names
     * @return PipelineSecrets
     */
    public function onlySecrets(string... $names): PipelineSecrets
    {
        return new PipelineSecrets(array_filter($this->secrets, fn (string $name) => in_array($name, $names), ARRAY_FILTER_USE_KEY));
    }

    /**
     * Maps secrets to different keys. Very useful if there are naming conflicts in plugins, or you just fancy calling
     * them by a more sensible name. Maps keys to the old names e.g.
     * ```
     * $plugin->run($pipe, $secrets->mapSecrets([
     *   "username" => "this_plugin_username",
     *   "password" => "this_plugin_password",
     * ]);
     * ```
     * Where this current object contains "this_plugin_username" and "this_plugin_password". Creates a new PipelineSecrets
     * object with "username" and "password".
     *
     * @param array $map
     * @return PipelineSecrets
     */
    public function mapSecrets(array $map): PipelineSecrets
    {
        return new PipelineSecrets(array_map(fn (string $oldName) => $this->getSecret($oldName), $map));
    }

    /**
     * This is an internal PHP method called when an object has `serialize()` called on it. This stops accidental
     * serialization of secrets by poorly written plugins.
     * @link https://www.php.net/manual/en/function.serialize.php
     */
    public function __serialize(): array
    {
        return [];
    }
}
