<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation;

use InvalidArgumentException;

class ValidationRules
{
    /**
     * @var array{
     *          query?:      Constraint|array<string, string|Constraint|array<string|Constraint>>,
     *          request?:    Constraint|array<string, string|Constraint|array<string|Constraint>>,
     *          attributes?: Constraint|array<string, string|Constraint|array<string|Constraint>>
     * } $definitions
     */
    private $definitions;

    /**
     * @param array{
     *          query?:      Constraint|array<string, string|Constraint|array<string|Constraint>>,
     *          request?:    Constraint|array<string, string|Constraint|array<string|Constraint>>,
     *          attributes?: Constraint|array<string, string|Constraint|array<string|Constraint>>
     *          } $definitions
     */
    public function __construct(array $definitions)
    {
        // expect no other keys than `query` or `request`
        if (count(array_diff(array_keys($definitions), ['query', 'request', 'attributes'])) > 0) {
            throw new InvalidArgumentException('Expecting at most `query`, `request` or `attribute` property to be set');
        }

        $this->definitions = $definitions;
    }

    /**
     * @return array{
     *          query?:      Constraint|array<string, string|Constraint|array<string|Constraint>>,
     *          request?:    Constraint|array<string, string|Constraint|array<string|Constraint>>,
     *          attributes?: Constraint|array<string, string|Constraint|array<string|Constraint>>
     * } $definitions
     */
    public function getDefinitions(): array
    {
        return $this->definitions;
    }
}
