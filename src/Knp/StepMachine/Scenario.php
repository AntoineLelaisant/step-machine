<?php

namespace Knp\StepMachine;

interface Scenario
{
    /**
     * @return Steps[]
     */
    public function getSteps();

    /**
     * @param Context $context
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function finalize(Context $context);
}
