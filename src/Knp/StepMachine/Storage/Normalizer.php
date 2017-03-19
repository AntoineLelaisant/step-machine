<?php

namespace Knp\StepMachine\Storage;

interface Normalizer
{
    public function normalize($data);

    public function denormalize($data);
}
