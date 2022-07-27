<?php
declare(strict_types=1);

namespace Janwebdev\RestBundle\Exception\DTO;


class ValidationError extends Error
{
    public const TRANSLATION_GROUP = 'validation';

    public string $field;

    /**
     * ValidationError constructor.
     * @param string $field
     * @param string $message
     * @param string|null $key
     * @param array|null $values
     */
    public function __construct(string $field, string $message, ?string $key, ?array $values = [])
    {
        parent::__construct($message, self::TRANSLATION_GROUP, $key, $values);
        $this->field = $field;
    }
}
