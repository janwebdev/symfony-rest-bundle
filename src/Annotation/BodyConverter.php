<?php
declare(strict_types=1);

namespace Janwebdev\RestBundle\Annotation;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Class BodyConverter
 * @package Sellit\RestBundle\Annotations
 * @Annotation
 */
class BodyConverter extends ParamConverter
{
    private string $converter = 'fos_rest.request_body';

    /**
     * @return string
     */
    public function getConverter(): string
    {
        return $this->converter;
    }

    /**
     * @param string $converter
     */
    public function setConverter($converter): void
    {
        $this->converter = $converter;
    }
}
