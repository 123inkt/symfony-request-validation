<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation;

use InvalidArgumentException;
use Symfony\Component\Validator\Constraint;

/**
 * @phpstan-type ConstraintList array<string, string|Constraint|array<string|Constraint>>|array<int, Constraint>
 * @phpstan-type DefinitionCollection array{query?: ConstraintList, request?: ConstraintList, attributes?: ConstraintList}
 */
class ValidationRules
{
    /** @phpstan-var DefinitionCollection $definitions */
    private array $definitions;

    private bool $allowExtraFields;

    /**
     * @phpstan-param DefinitionCollection $definitions
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
     * @phpstan-return DefinitionCollection $definitions
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
