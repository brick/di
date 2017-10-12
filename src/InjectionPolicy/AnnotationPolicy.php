<?php

declare(strict_types=1);

namespace Brick\Di\InjectionPolicy;

use Brick\Di\Annotation\Inject;
use Brick\Di\InjectionPolicy;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\Reader;

/**
 * Controls injection with annotations.
 */
class AnnotationPolicy implements InjectionPolicy
{
    /**
     * The annotation reader.
     *
     * @var \Doctrine\Common\Annotations\Reader
     */
    private $reader;

    /**
     * Class constructor.
     *
     * @param \Doctrine\Common\Annotations\Reader $reader
     */
    public function __construct(Reader $reader)
    {
        AnnotationRegistry::registerFile(__DIR__ . '/../Annotation/Inject.php');

        $this->reader = $reader;
    }

    /**
     * {@inheritdoc}
     */
    public function isClassInjected(\ReflectionClass $class) : bool
    {
        foreach ($this->reader->getClassAnnotations($class) as $annotation) {
            if ($annotation instanceof Inject) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isMethodInjected(\ReflectionMethod $method) : bool
    {
        foreach ($this->reader->getMethodAnnotations($method) as $annotation) {
            if ($annotation instanceof Inject) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isPropertyInjected(\ReflectionProperty $property) : bool
    {
        foreach ($this->reader->getPropertyAnnotations($property) as $annotation) {
            if ($annotation instanceof Inject) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameterKey(\ReflectionParameter $parameter)
    {
        $function = $parameter->getDeclaringFunction();

        if ($function instanceof \ReflectionMethod) {
            foreach ($this->reader->getMethodAnnotations($function) as $annotation) {
                if ($annotation instanceof Inject) {
                    $value = $annotation->getValue($parameter->getName());

                    if ($value !== null) {
                        return $value;
                    }
                }
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getPropertyKey(\ReflectionProperty $property)
    {
        foreach ($this->reader->getPropertyAnnotations($property) as $annotation) {
            if ($annotation instanceof Inject) {
                $value = $annotation->getSingleValue();

                if ($value !== null) {
                    return $value;
                }
            }
        }

        return null;
    }
}
