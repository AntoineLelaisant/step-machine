<?php

namespace Knp\StepMachine\Storage\Normalizer;

use Knp\StepMachine\Storage\Normalizer;

class Serializer implements Normalizer
{
    public function normalize($data)
    {
        return serialize($data);
    }

    public function denormalize($data)
    {
        return unserialize($data);
    }
}
