<?php
declare(strict_types=1);

namespace Janwebdev\RestBundle\Serializer;

use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Type\ParserInterface;
use Metadata\ClassMetadata;
use Metadata\Driver\DriverInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;

class TypedPropertyMetadataDriverDecorator implements DriverInterface
{
    /**
     * @var DriverInterface
     */
    private DriverInterface $delegate;
    /**
     * @var ParserInterface
     */
    private ParserInterface $typeParser;

    /**
     * TypedPropertyMetadataDriverDecorator constructor.
     * @param DriverInterface $delegate
     * @param ParserInterface $typeParser
     */
    public function __construct(DriverInterface $delegate, ParserInterface $typeParser)
    {
        $this->delegate = $delegate;
        $this->typeParser = $typeParser;
    }

    /**
     * @param ReflectionClass $class
     * @return ClassMetadata|null
     * @throws ReflectionException
     */
    public function loadMetadataForClass(ReflectionClass $class): ?ClassMetadata
    {
        $classMetadata = $this->delegate->loadMetadataForClass($class);
        if ($classMetadata !== null) {
            foreach ($classMetadata->propertyMetadata as $propertyMetadata) {
                if ($propertyMetadata instanceof PropertyMetadata && !$propertyMetadata->type) {
                    $reflectionProperty = $class->getProperty($propertyMetadata->name);
                    if ($reflectionProperty->hasType()) {
                        $type = $reflectionProperty->getType();
                        if ($type instanceof ReflectionNamedType) {
                            $propertyMetadata->setType($this->typeParser->parse($type->getName()));
                        }
                    }
                }
            }
        }
        return $classMetadata;
    }

}
