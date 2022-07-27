<?php
declare(strict_types=1);

namespace Janwebdev\RestBundle\Annotation;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Annotation
 */
class BodyConverter extends ParamConverter
{
	private const CONVERTER_NAME = 'fos_rest.request_body';

	public function __construct(
		$data = [],
		string $class = null,
		array $options = [],
		bool $isOptional = false,
		string $converter = null
	)
	{
		parent::__construct(
			$data,
			$class,
			$options,
			$isOptional,
			self::CONVERTER_NAME
		);
	}
}
