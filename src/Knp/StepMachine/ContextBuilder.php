<?php

namespace Knp\StepMachine;

use Symfony\Component\HttpFoundation\RequestStack;
use Knp\StepMachine\Context\DefaultContext;

class ContextBuilder
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @param Process $process
     *
     * @return DefaultContext
     */
    public function build(Process $process)
    {
        return new DefaultContext(
            $this->requestStack->getCurrentRequest(),
            $process
        );
    }
}
