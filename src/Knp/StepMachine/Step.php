<?php

namespace Knp\StepMachine;

interface Step
{
    /**
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function execute(Context $context);

    /**
     * @return Knp\StepMachine\Response\CompleteResponse
     */
    public function complete();
}
