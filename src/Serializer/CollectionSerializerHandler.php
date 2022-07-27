<?php
declare(strict_types=1);

namespace Janwebdev\RestBundle\Serializer;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;

class CollectionSerializerHandler implements SubscribingHandlerInterface
{
    public static function getSubscribingMethods(): array
    {
        $methods = [];
        foreach (
            [
                'Collection',
                Collection::class,
            ] as $class
        ) {
            $methods[] = [
                'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => $class,
                'method' => 'serializeCollectionToJson',
            ];
            $methods[] = [
                'direction' => GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
                'format' => 'json',
                'type' => $class,
                'method' => 'deserializeCollectionFromJson',
            ];
        }
        return $methods;
    }

    public function serializeCollectionToJson(
        JsonSerializationVisitor $visitor,
        Collection $collection,
        array $type,
        Context $context
    ): array
    {
        $type['name'] = 'array';

        $context->stopVisiting($collection);

        $result = $visitor->visitArray($collection->toArray(), $type);

        $context->startVisiting($collection);

        return $result;
    }

    public function deserializeCollectionFromJson(
        JsonDeserializationVisitor $visitor,
        array $data,
        array $type,
        Context $context
    ): ArrayCollection
    {
        // See above.
        $type['name'] = 'array';

        return new ArrayCollection($visitor->visitArray($data, $type));
    }

}
