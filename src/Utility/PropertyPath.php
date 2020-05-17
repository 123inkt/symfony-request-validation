<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Utility;

class PropertyPath
{
    /**
     * Transform symfony's property path to string array.
     *
     * Formats:
     * - product.name
     * - product[1].name
     * - [product][name]
     *
     * @return string[]
     */
    public static function toArray(?string $propertyPath): array
    {
        if ($propertyPath === null || $propertyPath === '') {
            return [];
        }

        // transform all brackets to dot
        $propertyPath = str_replace(['[', ']'], '.', $propertyPath);

        // trim any leading and trailing dots
        $propertyPath = trim($propertyPath, '.');

        // replace any double dots to single
        $propertyPath = str_replace('..', '.', $propertyPath);

        return explode('.', $propertyPath);
    }
}
