<?php

namespace Knp\StepMachine;

use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;

class SessionBag extends AttributeBag
{
    const STORAGE_KEY = '_step_machine_bag';
    const NAME        = 'step_machine_bag';

    private $name = self::NAME;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct(self::STORAGE_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::NAME;
    }
}
