<?php

namespace AvidCi\Pipelines;

class RunStatus
{
    public function __construct(
        private bool $success,
        private array $context = [],
        private ?\Throwable $exception = null
    ){}

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function getException(): ?\Throwable
    {
        return $this->exception;
    }
}
