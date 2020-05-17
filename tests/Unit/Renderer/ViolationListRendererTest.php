<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Unit\Renderer;

use DigitalRevolution\SymfonyRequestValidation\Renderer\ViolationListRenderer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * @coversDefaultClass \DigitalRevolution\SymfonyRequestValidation\Renderer\ViolationListRenderer
 * @covers ::__construct
 */
class ViolationListRendererTest extends TestCase
{
    /**
     * @covers ::render
     */
    public function testRender(): void
    {
        $violationA = new ConstraintViolation('Peter Parker', '', [], '', 'contact[name]', '');
        $violationB = new ConstraintViolation('Bruce Wayne', '', [], '', 'contact.name', '');

        $violationList = new ConstraintViolationList();
        $violationList->add($violationA);
        $violationList->add($violationB);

        $renderer = new ViolationListRenderer($violationList);
        $output   = $renderer->render();

        $expected = "contact.name: Peter Parker\n";
        $expected .= "contact.name: Bruce Wayne";

        static::assertSame($expected, $output);
    }
}
