<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Constraint;

use Symfony\Component\Validator\Constraint;

class RequestConstraint extends Constraint
{
    /** @var string */
    public const MISSING_QUERY_CONSTRAINT = 'b62ab5ca-ee6f-4baf-bdef-ffbe14f674d6';
    /** @var string */
    public const MISSING_REQUEST_CONSTRAINT = 'c3990dad-3638-449b-9dd3-4dd42e90c52f';
    /** @var string */
    public const INVALID_BODY_CONTENT = '3b41f393-5f46-471e-8a2e-c4035d5fb3cb';
    /** @var string */
    public const WRONG_VALUE_TYPE = '08937cc5-9ea6-460c-9917-d3f6ba912998';

    /** @var array<string, string> */
    protected const ERROR_NAMES = [
        self::WRONG_VALUE_TYPE           => 'WRONG_VALUE_TYPE',
        self::MISSING_QUERY_CONSTRAINT   => 'MISSING_QUERY_CONSTRAINT',
        self::MISSING_REQUEST_CONSTRAINT => 'MISSING_REQUEST_CONSTRAINT',
        self::INVALID_BODY_CONTENT       => 'INVALID_BODY_CONTENT',
    ];

    public string $wrongTypeMessage = 'Expect value to be of type Symfony\Component\HttpFoundation\Request';
    public string $queryMessage = 'Request::query is not empty, but there is no constraint configured.';
    public string $requestMessage = 'Request::request is not empty, but there is no constraint configured.';
    public string $invalidBodyMessage = 'Request::content cant be decoded';

    /** @var Constraint|Constraint[]|null */
    public Constraint|array|null $query = null;

    /** @var Constraint|Constraint[]|null */
    public Constraint|array|null $request = null;

    /** @var Constraint|Constraint[]|null */
    public Constraint|array|null $attributes = null;

    public bool $allowExtraFields = false;

    /**
     * @param array{
     *     query?: Constraint|Constraint[],
     *     request?: Constraint|Constraint[],
     *     attributes?: Constraint|Constraint[],
     *     allowExtraFields?: bool
     *     }|null $options
     */
    public function __construct(?array $options = null)
    {
        // make sure defaults are set
        $options                     = $options ?? [];
        $options['query']            = $options['query'] ?? null;
        $options['request']          = $options['request'] ?? null;
        $options['attributes']       = $options['attributes'] ?? null;
        $options['allowExtraFields'] = $options['allowExtraFields'] ?? false;

        parent::__construct($options);
    }

    public function getRequiredOptions(): array
    {
        return ['query', 'request', 'attributes', 'allowExtraFields'];
    }
}
