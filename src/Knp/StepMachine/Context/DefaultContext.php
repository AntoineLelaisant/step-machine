<?php

namespace Knp\StepMachine\Context;

use Symfony\Component\HttpFoundation\Request;
use Knp\StepMachine\Context;
use Knp\StepMachine\Storage;
use Knp\StepMachine\Process;

class DefaultContext implements Context
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Process
     */
    private $process;

    /**
     * @param Request $request
     * @param Process $process
     * @param Storage $storage
     */
    public function __construct(
        Request $request,
        Process $process
    ) {
        $this->request = $request;
        $this->process = $process;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * {@inheritdoc}
     */
    public function getProcess()
    {
        return $this->process;
    }

    /**
     * {@inheritdoc}
     */
    public function getStorage()
    {
        return $this->process->getStorage();
    }
}
