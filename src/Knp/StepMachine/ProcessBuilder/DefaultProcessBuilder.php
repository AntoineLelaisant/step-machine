<?php

namespace Knp\StepMachine\ProcessBuilder;

use Knp\StepMachine\ProcessBuilder;
use Knp\StepMachine\Process\DefaultProcess;
use Knp\StepMachine\Context;
use Knp\StepMachine\Storage;
use Knp\StepMachine\Scenario;

class DefaultProcessBuilder implements ProcessBuilder
{
    /**
     * @var Storage
     */
    private $storage;

    /**
     * @param Storage $storage
     */
    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * {@inheritdoc}
     */
    public function build(Scenario $scenario)
    {
        $process = new DefaultProcess($this->storage);

        $steps = $scenario->getSteps();

        foreach ($steps as $alias => $step) {
            $process->add($alias, $step);
        }

        return $process;
    }
}
