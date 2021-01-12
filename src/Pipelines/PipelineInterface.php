<?php

namespace AvidCi\Pipelines;

interface PipelineInterface
{
    public function run(RunningPipe $pipe, PipelineSecrets $secrets): RunStatus;
}
