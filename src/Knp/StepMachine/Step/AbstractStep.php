<?php

namespace Knp\StepMachine\Step;

use Knp\StepMachine\Step;
use Knp\StepMachine\Response\CompleteResponse;

abstract class AbstractStep implements Step
{
    public function complete()
    {
        return new CompleteResponse();
    }
}
