<?php

namespace Knp\StepMachine\Scenario;

use Knp\StepMachine\Scenario;
use Knp\StepMachine\Process;
use Knp\StepMachine\Step;

abstract class AbstractScenario implements Scenario
{
    /**
     * @var Step[]
     */
    private $steps;

    /**
     * @return Step[]
     */
    public function getSteps()
    {
        return $this->steps;
    }

    /**
     * @param string $stepAlias
     * @param Step $step
     *
     * @return AbstractScenario
     */
    public function addStep($stepAlias, Step $step)
    {
        $this->steps[$stepAlias] = $step;

        return $this;
    }
}
