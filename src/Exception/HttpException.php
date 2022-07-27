<?php
declare(strict_types=1);

namespace Janwebdev\RestBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException as SymfonyHttpException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use Exception;

class HttpException extends SymfonyHttpException
{
    private string $group;
    private string $key;
    private array $values;

    /**
     * HttpException constructor.
     * @param string $group
     * @param string $key
     * @param array $values
     * @param int $statusCode
     * @param string|null $message
     * @param Throwable|null $previous
     * @param array $headers
     * @param int|null $code
     */
    public function __construct(string $group, string $key, array $values, int $statusCode, string $message = null, Throwable $previous = null, array $headers = [], ?int $code = 0)
    {
        $this->group = $group;
        $this->key = $key;
        $this->values = $values;
        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }

    public static function from(Exception $exception, int $statusCode, array $headers = []): self
    {
        return new self(
            $exception->getGroup(),
            $exception->getKey(),
            $exception->getValues(),
            $statusCode,
            $exception->getMessage(),
            $exception,
            $headers,
            $exception->getCode()
        );
    }

    public static function to404(Exception $exception): self
    {
        return self::from($exception, Response::HTTP_NOT_FOUND);
    }

    public static function to403(Exception $exception): self
    {
        return self::from($exception, Response::HTTP_FORBIDDEN);
    }

    public static function to401(Exception $exception, string $challenge): self
    {
        return self::from($exception, Response::HTTP_UNAUTHORIZED, [
            'WWW-Authenticate' => $challenge
        ]);
    }

    public static function to400(Exception $exception): self
    {
        return self::from($exception, Response::HTTP_BAD_REQUEST);
    }

    public static function to417(Exception $exception): self
    {
        return self::from($exception, Response::HTTP_EXPECTATION_FAILED);
    }

    public function getGroup(): string
    {
        return $this->group;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getValues(): array
    {
        return $this->values;
    }
}
