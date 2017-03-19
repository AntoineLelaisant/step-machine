<?php

namespace Knp\StepMachine;

use Knp\StepMachine\Response\CompleteResponse;
use Knp\StepMachine\Exception\ScenarioNotFoundException;
use Knp\StepMachine\Exception\LastStepReachedException;
use Knp\StepMachine\RouteGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Knp\StepMachine\Exception\StepNotFoundException;
use Knp\StepMachine\Exception\ForceStepDeniedException;

class StepMachine
{
    /**
     * @var ProcessBuilder
     */
    private $processBuilder;

    /**
     * @var ContextBuilder
     */
    private $contextBuilder;

    /**
     * @var RouteGenerator
     */
    private $routeGenerator;

    /**
     * @var Scenario[]
     */
    private $scenarios = [];

    /**
     * @param ProcessBuilder $processBuilder
     * @param ContextBuilder $contextBuilder
     * @param RouteGenerator $routeGenerator
     */
    public function __construct(
        ProcessBuilder $processBuilder,
        ContextBuilder $contextBuilder,
        RouteGenerator $routeGenerator
    ) {
        $this->processBuilder = $processBuilder;
        $this->contextBuilder = $contextBuilder;
        $this->routeGenerator = $routeGenerator;
    }

    /**
     * {@inheritdoc}
     *
     * @throws ScenarioNotFoundException
     */
    public function run($scenarioAlias, $stepAlias = null)
    {
        $scenario = $this->getScenario($scenarioAlias);
        $process = $this->processBuilder->build($scenario);
        $context = $this->contextBuilder->build($process);

        if (false === $this->ensureCurrentStep($context, $stepAlias)) {
            return $this->redirectToStep($context, $process->getCurrentStep($context));
        }

        $step = $process->getCurrentStep($context);

        $response = $step->execute($context);

        if ($response instanceof CompleteResponse) {
            try {
                $process->completeCurrentStep($context);
            } catch (LastStepReachedException $e) {
                $response = $scenario->finalize($context);
                $context->getStorage()->clear();

                return $response;
            }

            return $this->redirectToStep($context, $process->getCurrentStep($context));
        }

        return $response;
    }

    /**
     * @param Context $context
     * @param string $stepAlias
     *
     * @return bool
     */
    private function ensureCurrentStep(
        Context $context,
        $stepAlias = null
    ) {
        if (null === $stepAlias) {
            return false;
        }

        $process = $context->getProcess();

        $currentStep = $process->getCurrentStep($context);

        if ($stepAlias === $process->getStepAlias($currentStep)) {
            return true;
        }

        try {
            $process->forceCurrentStep($stepAlias);
        } catch (StepNotFoundException $e) {
        } catch (ForceStepDeniedException $e) {
        }

        return false;
    }

    /**
     * @param Context $context
     * @param Step $step
     *
     * @return RedirectResponse
     */
    private function redirectToStep(Context $context, Step $step)
    {
        return new RedirectResponse($this->routeGenerator->generate($context, $step));
    }

    /**
     * {@inheritdoc}
     *
     * @return StepMachine
     */
    public function addScenario($alias, Scenario $scenario)
    {
        $this->scenarios[$alias] = $scenario;

        return $this;
    }

    /**
     * @param string $scenarioAlias
     *
     * @return Scenario
     * @throws ScenarioNotFoundException
     */
    public function getScenario($scenarioAlias)
    {
        if (false === isset($this->scenarios[$scenarioAlias])) {
            throw new ScenarioNotFoundException();
        }

        return $this->scenarios[$scenarioAlias];
    }

    /**
     * @param Context $context
     *
     * @return Process
     */
    private function buildProcess(Context $context)
    {
        return $this->processBuilder->build($context);
    }
}
