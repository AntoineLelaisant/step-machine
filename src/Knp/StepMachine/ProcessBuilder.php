<?php

namespace Knp\StepMachine;

interface ProcessBuilder
{
    /**
     * @param Scenario $scenario
     *
     * @return Process
     */
    public function build(Scenario $scenario);
}
