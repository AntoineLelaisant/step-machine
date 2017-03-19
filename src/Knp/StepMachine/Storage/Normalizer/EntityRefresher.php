<?php

namespace Knp\StepMachine\Storage\Normalizer;

use Knp\StepMachine\Storage\Normalizer;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\ORM\ORMInvalidArgumentException;

class EntityRefresher implements Normalizer
{
    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * @var PropertyAccessor
     */
    private $accessor;

    private $refreshedObjects = [];

    /**
     * @param ObjectManager $em
     * @param PropertyAccessor $accessor
     */
    public function __construct(
        ObjectManager $em,
        PropertyAccessor $accessor
    ) {
        $this->em       = $em;
        $this->accessor = $accessor;
    }

    protected function refresh($data)
    {
        if (is_scalar($data)) {
            return $data;
        }

        if (
            is_array($data)
            || $data instanceof \ArrayAccess
            || $data instanceof \Traversable
        ) {
            foreach ($data as $key => $property) {
                 $data[$key] = $this->refresh($property);
            }

            return $data;
        }

        if (
            !is_object($data)
            || $this->em->contains($data)
        ) {
            return $data;
        }

        if (array_key_exists(spl_object_hash($data), $this->refreshedObjects)) {
            return $this->refreshedObjects[spl_object_hash($data)];
        }

        try {
            $className = $this->em->getClassMetadata(get_class($data))->getName();

            $entity = $this->em->getRepository($className)->find($data);

            $this->refreshedObjects[spl_object_hash($data)] = $entity;

            return $entity;
        } catch (MappingException $e) {
        } catch (ORMInvalidArgumentException $e) {}

        $reflection = new \ReflectionClass($data);

        $this->refreshedObjects[spl_object_hash($data)] = $data;

        foreach ($reflection->getProperties() as $property) {
            try {
                $property->setAccessible(true);
                $value = $property->getValue($data);
                $value = $this->refresh($value);

                $property->setValue($data, $value);
            } catch (\Exception $e) {}
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($data)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data)
    {
        $this->refreshedObjects = [];

        return $this->refresh($data);
    }
}
