<?php
declare(strict_types=1);

namespace Janwebdev\RestBundle\Validation;


use FOS\RestBundle\Request\RequestBodyParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationExceptionThrowingBodyConverterDecorator implements ParamConverterInterface
{
    public const VALIDATION_ERRORS_ARGUMENT_NAME = 'validationErrors';

    private RequestBodyParamConverter $delegate;
    private ValidationExceptionFactory $validationErrorsExceptionConverter;

    /**
     * ValidationExceptionThrowingBodyConverterDecorator constructor.
     * @param RequestBodyParamConverter $delegate
     * @param ValidationExceptionFactory $validationErrorsExceptionConverter
     */
    public function __construct(
        RequestBodyParamConverter $delegate,
        ValidationExceptionFactory $validationErrorsExceptionConverter
    )
    {
        $this->delegate = $delegate;
        $this->validationErrorsExceptionConverter = $validationErrorsExceptionConverter;
    }

    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $delegateApplied = $this->delegate->apply($request, $configuration);
        if ($delegateApplied) {
            $validationErrors = $request->attributes->get(self::VALIDATION_ERRORS_ARGUMENT_NAME);
            if ($validationErrors &&
                $validationErrors instanceof ConstraintViolationListInterface &&
                $validationErrors->count()
            ) {
                throw $this->validationErrorsExceptionConverter->buildFromViolationsList($validationErrors);
            }
        }
        return $delegateApplied;
    }

    public function supports(ParamConverter $configuration): bool
    {
        return $this->delegate->supports($configuration);
    }
}
