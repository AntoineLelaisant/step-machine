<?php

namespace Knp\StepMachine;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Knp\StepMachine\Context;

class RouteGenerator
{
    /**
     * @var UrlGeneratorInterface
     */
    private $generator;

    /**
     * @param UrlGeneratorInterface $generator
     */
    public function __construct(UrlGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    /**
     * @param Context $context
     * @param Step $step
     * @param string $stepNameParameter
     *
     * @return string the given step URL
     */
    public function generate(
        Context $context,
        Step $step,
        $stepNameParameter = 'stepName'
    ) {
        $process = $context->getProcess();

        $route = $context->getRequest()->get('_route');
        $params = array_merge($context->getRequest()->get('_route_params'), [
            $stepNameParameter => $process->getStepAlias($step),
        ]);

        return $this->generator->generate($route, $params);
    }
}
