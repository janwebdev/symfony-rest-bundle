<?php
declare(strict_types=1);

namespace Janwebdev\RestBundle\Exception\DTO;


class Error
{
    public string $message;
    public ?string $group;
    public ?string $key;
    /**
     * @var array|string[]|null
     */
    public ?array $values;

    /**
     * ExceptionDTOError constructor.
     * @param string $message
     * @param string|null $group
     * @param string|null $key
     * @param array|string[]|null $values
     */
    public function __construct(string $message, ?string $group = null, ?string $key = null, ?array $values = null)
    {
        $this->message = $message;
        $this->group = $group;
        $this->key = $key;
        $this->values = $values;
    }
}
