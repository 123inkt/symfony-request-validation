<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation;

use InvalidArgumentException;
use Symfony\Component\Validator\Constraint;

class ValidationRules
{
    /** @var Constraint|array<string, string|Constraint|array<string|Constraint>>|null */
    private $queryRules;

    /** @var Constraint|array<string, string|Constraint|array<string|Constraint>>|null */
    private $requestRules;

    /**
     * @param array{
     *          query?:   Constraint|array<string, string|Constraint|array<string|Constraint>>,
     *          request?: Constraint|array<string, string|Constraint|array<string|Constraint>>
     *        } $definitions
     */
    public function __construct(array $definitions)
    {
        // expect no other keys than `query` or `request`
        if (count(array_diff(array_keys($definitions), ['query', 'request'])) > 0) {
            throw new InvalidArgumentException('Expecting at most `query` or `request` property to be set');
        }

        $this->queryRules   = $definitions['query'] ?? null;
        $this->requestRules = $definitions['request'] ?? null;
    }

    /**
     * @return Constraint|array<string, string|Constraint|array<string|Constraint>>|null
     */
    public function getQueryRules()
    {
        return $this->queryRules;
    }

    /**
     * @return Constraint|array<string, string|Constraint|array<string|Constraint>>|null
     */
    public function getRequestRules()
    {
        return $this->requestRules;
    }
}
