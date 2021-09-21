<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation;

use InvalidArgumentException;
use Symfony\Component\Validator\Constraint;

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

    /** @var bool */
    private $allowExtraFields;

    /**
     * @param array{
     *          query?:      Constraint|array<string, string|Constraint|array<string|Constraint>>,
     *          request?:    Constraint|array<string, string|Constraint|array<string|Constraint>>,
     *          attributes?: Constraint|array<string, string|Constraint|array<string|Constraint>>
     *          } $definitions
     * @param bool $allowExtraFields Allow the request to have extra fields, not present in the definition list
     */
    public function __construct(array $definitions, bool $allowExtraFields = false)
    {
        // expect no other keys than `query` or `request`
        if (count(array_diff(array_keys($definitions), ['query', 'request', 'attributes'])) > 0) {
            throw new InvalidArgumentException('Expecting at most `query`, `request` or `attribute` property to be set');
        }

        $this->definitions      = $definitions;
        $this->allowExtraFields = $allowExtraFields;
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

    public function getAllowExtraFields(): bool
    {
        return $this->allowExtraFields;
    }
}
