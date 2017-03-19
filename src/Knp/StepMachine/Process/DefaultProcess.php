<?php

namespace Knp\StepMachine\Process;

use Knp\StepMachine\Process;
use Knp\StepMachine\Step;
use Knp\StepMachine\Context;
use Knp\StepMachine\Exception\LastStepReachedException;
use Knp\StepMachine\Exception\StepNotFoundException;
use Knp\StepMachine\Exception\ForceStepDeniedException;
use Knp\StepMachine\Storage;

class DefaultProcess implements Process
{
    const CURRENT_STEP_KEY = '_current_step';

    /**
     * @var Step[]
     */
    private $steps = [];

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
     *
     * @return DefaultProcess
     */
    public function add($stepAlias, Step $step)
    {
        $this->steps[$stepAlias] = $step;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSteps()
    {
        return $this->steps;
    }

    /**
     * @return Storage
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstStep()
    {
        $steps = array_values($this->steps);

        return reset($steps);
    }

    /**
     * {@inheritdoc}
     *
     * @throws LastStepReachedException
     */
    public function completeCurrentStep()
    {
        $this->storage->set(
            self::CURRENT_STEP_KEY,
            array_search($this->getNextStep(), $this->steps)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentStep()
    {
        $currentStepName = $this->storage->get(self::CURRENT_STEP_KEY);

        if (false === array_key_exists($currentStepName, $this->steps)) {
            if (null !== $currentStepName) {
                $this->storage->clear();
            }

            return $this->getFirstStep();
        }

        return $this->steps[$currentStepName];
    }

    /**
     * {@inheritdoc}
     *
     * @throws StepNotFoundException
     * @throws ForceStepDeniedException
     */
    public function forceCurrentStep($stepAlias)
    {
        if (false === array_key_exists($stepAlias, $this->steps)) {
            throw new StepNotFoundException();
        }

        if ($stepAlias === $this->getStepAlias($this->getFirstStep())) {
            $this->storage->set(self::CURRENT_STEP_KEY, $stepAlias);

            return;
        }

        $validatedSteps = $this->getValidatedSteps();

        if (!in_array($stepAlias, $validatedSteps)) {
            throw new ForceStepDeniedException('You can\'t force the current step with unvalidated step');
        }

        $this->storage->set(self::CURRENT_STEP_KEY, $stepAlias);
    }

    /**
     * {@inheritdoc}
     */
    public function hasNextStep()
    {
        try {
            $this->getNextStep();

            return true;
        } catch (LastStepReachedException $e) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getNextStep()
    {
        $steps = array_values($this->steps);

        foreach ($steps as $index => $step) {
            if ($step === $this->getCurrentStep()) {
                if (!isset($steps[$index+1])) {
                    throw new LastStepReachedException();
                }

                return $steps[$index+1];
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasPreviousStep()
    {
        try {
            $this->getPreviousStep();

            return true;
        } catch(StepNotFoundException $e) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getPreviousStep()
    {
        $steps = array_values($this->steps);

        foreach ($steps as $index => $step) {
            if ($step === $this->getCurrentStep()) {
                if (!isset($steps[$index-1])) {
                    throw new StepNotFoundException();
                }

                return $steps[$index-1];
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws StepNotFoundException
     */
    public function getStepAlias(Step $step)
    {
        $alias = array_search($step, $this->steps);

        if (!$alias) {
            throw new StepNotFoundException();
        }

        return $alias;
    }

    /**
     * @param Context $context
     *
     * @return Step[]
     */
    private function getValidatedSteps()
    {
        if (false === $this->storage->has(self::CURRENT_STEP_KEY)) {
            return [];
        }

        $currentStepName = $this->storage->get(self::CURRENT_STEP_KEY);

        $validatedSteps = array_keys($this->steps);

        while ($currentStepName !== array_pop($validatedSteps)) {}

        return $validatedSteps;
    }
}
