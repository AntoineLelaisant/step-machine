<?php

namespace Knp\StepMachine;

interface Process
{
    /**
     * @param string $stepAlias
     * @param Step   Step
     */
    public function add($stepAlias, Step $step);

    /**
     * @return Step[]
     */
    public function getSteps();

    /**
     * @return Storage
     */
    public function getStorage();

    /**
     * @return Step
     */
    public function getFirstStep();

    /**
     * @return bool
     */
    public function completeCurrentStep();

    /**
     * @return Step
     */
    public function getCurrentStep();

    /**
     * @param string $stepAlias
     */
    public function forceCurrentStep($stepAlias);

    /**
     * @return bool
     */
    public function hasNextStep();

    /**
     * @return Step
     */
    public function getNextStep();

    /**
     * @return bool
     */
    public function hasPreviousStep();

    /**
     * @return Step
     */
    public function getPreviousStep();

    /**
     * @param Step $step
     *
     * @return string
     */
    public function getStepAlias(Step $context);
}
