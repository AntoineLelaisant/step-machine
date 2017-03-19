<?php

namespace Knp\StepMachine\Storage;

use Knp\StepMachine\Storage;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Knp\StepMachine\SessionBag;
use Knp\StepMachine\Storage\Normalizer;

class SessionStorage implements Storage, Normalizer
{
    private $session;

    private $normalizers = [];

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function set($key, $value)
    {
        return $this->getBag()->set($key, $this->normalize($value));
    }

    public function get($key, $default = null)
    {
        if ($this->has($key)) {
            return $this->denormalize($this->getBag()->get($key));
        }

        return $default;
    }

    public function remove($key)
    {
        return $this->getBag()->remove($key);
    }

    public function clear()
    {
        return $this->getBag()->clear();
    }

    public function has($key)
    {
        return $this->getBag()->has($key);
    }

    private function getBag()
    {
        return $this->session->getBag(SessionBag::NAME);
    }

    public function normalize($data)
    {
        foreach ($this->normalizers as $normalizer) {
            $data = $normalizer->normalize($data);
        }

        return $data;
    }

    public function denormalize($data)
    {
        foreach ($this->normalizers as $normalizer) {
            $data = $normalizer->denormalize($data);
        }

        return $data;
    }

    public function addNormalizer(Normalizer $normalizer)
    {
        $this->normalizers[] = $normalizer;

        return $this;
    }
}
