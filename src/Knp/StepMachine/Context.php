<?php

namespace Knp\StepMachine;

interface Context
{
    /**
     * @return Symfony\Component\HttpFoundation\Request
     */
    public function getRequest();

    /**
     * @return Knp\StepMachine\Process
     */
    public function getProcess();

    /**
     * @return Knp\StepMachine\Storage
     */
    public function getStorage();
}
