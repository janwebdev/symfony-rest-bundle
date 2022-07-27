<?php
declare(strict_types=1);

namespace Janwebdev\RestBundle\Exception\DTO;


class ExceptionDTO
{
    /**
     * @var array|Error[]
     */
    public array $errors = [];
}
