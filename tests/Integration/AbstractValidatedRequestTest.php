<?php
declare(strict_types=1);

namespace DigitalRevolution\SymfonyRequestValidation\Tests\Integration;

use DigitalRevolution\SymfonyRequestValidation\InvalidRequestException;
use DigitalRevolution\SymfonyRequestValidation\Tests\Mock\MockValidatedRequest;
use DigitalRevolution\SymfonyRequestValidation\ValidationRules;
use DigitalRevolution\SymfonyValidationShorthand\Rule\InvalidRuleException;
use Generator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Validation;

/**
 * @coversNothing
 */
class AbstractValidatedRequestTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     * @param array<string, mixed> $data
     * @param Collection|array<string, string|Constraint|array<string|Constraint>>|null $rules
     * @throws InvalidRequestException|InvalidRuleException
     */
    public function testGetRequestValidation(array $data, $rules, bool $isValid): void
    {
        $request = new Request($data);
        $stack   = new RequestStack();
        $stack->push($request);

        $validator       = Validation::createValidator();
        $validationRules = new ValidationRules(['query' => $rules]);

        // expect exception
        if ($isValid === false) {
            $this->expectException(InvalidRequestException::class);
            new MockValidatedRequest($stack, $validator, $validationRules);
        }

        // expect success
        $request = new MockValidatedRequest($stack, $validator, $validationRules);
        static::assertTrue($request->isValid());
    }

    /**
     * @dataProvider dataProvider
     * @param array<string, mixed> $data
     * @param Collection|array<string, string|Constraint|array<string|Constraint>>|null $rules
     * @throws InvalidRequestException|InvalidRuleException
     */
    public function testPostRequestValidation(array $data, $rules, bool $isValid): void
    {
        $request = new Request([], $data);
        $stack   = new RequestStack();
        $stack->push($request);

        $validator       = Validation::createValidator();
        $validationRules = new ValidationRules(['request' => $rules]);

        // expect exception
        if ($isValid === false) {
            $this->expectException(InvalidRequestException::class);
            new MockValidatedRequest($stack, $validator, $validationRules);
        }

        // expect success
        $request = new MockValidatedRequest($stack, $validator, $validationRules);
        static::assertTrue($request->isValid());
    }

    /**
     * @dataProvider dataProvider
     * @param array<string, mixed> $data
     * @param Collection|array<string, string|Constraint|array<string|Constraint>>|null $rules
     * @throws InvalidRequestException|InvalidRuleException
     */
    public function testRequestAttributesValidation(array $data, $rules, bool $isValid): void
    {
        $request = new Request([], [], $data);
        $stack   = new RequestStack();
        $stack->push($request);

        $validator       = Validation::createValidator();
        $validationRules = new ValidationRules(['attributes' => $rules]);

        // expect exception
        if ($isValid === false) {
            $this->expectException(InvalidRequestException::class);
            new MockValidatedRequest($stack, $validator, $validationRules);
        }

        // expect success
        $request = new MockValidatedRequest($stack, $validator, $validationRules);
        static::assertTrue($request->isValid());
    }


    public function dataProvider(): Generator
    {
        // test required fields
        yield "required: name exists" => [['name' => 'Foobar'], ['name' => 'required'], true];
        yield "required: name is empty" => [['name' => ''], ['name' => 'required'], true];
        yield "required: name can't be empty" => [['name' => ''], ['name' => 'required|filled'], false];
        yield "required: name can't be null" => [['name' => null], ['name' => 'required'], false];
        yield "required: name is nullable" => [['name' => null], ['name' => 'required|nullable'], true];
        yield "required: name field is missing" => [[], ['name' => 'required'], false];

        // test optional string
        yield "optional: name exists" => [['name' => 'Foobar'], ['name' => 'string'], true];
        yield "optional: name is empty" => [['name' => ''], ['name' => 'string'], true];
        yield "optional: name can't be empty" => [['name' => ''], ['name' => 'string|filled'], false];
        yield "optional: name field is missing" => [[], ['name' => 'string'], true];
        yield "optional: name can't be null" => [['name' => null], ['name' => 'string'], false];
        yield "optional: name is nullable" => [['name' => null], ['name' => 'string|nullable'], true];

        // test string must be null or filled, never empty string
        yield "string: name can be null" => [['name' => null], ['name' => 'string|filled|nullable'], true];
        yield "string: name can't be empty string" => [['name' => ''], ['name' => 'string|filled|nullable'], false];
        yield "string: name can be filled" => [['name' => 'unittest'], ['name' => 'string|filled|nullable'], true];

        // test integer must be between ranges
        yield "integer: must be minimum of 4 (success)" => [['quantity' => '4'], ['quantity' => 'integer|min:4'], true];
        yield "integer: must be minimum of 5 (fails)" => [['quantity' => '4'], ['quantity' => 'integer|min:5'], false];
        yield "integer: must be maximum of 5 (success)" => [['quantity' => '5'], ['quantity' => 'integer|max:5'], true];
        yield "integer: must be maximum of 4 (fails)" => [['quantity' => '5'], ['quantity' => 'integer|max:4'], false];
        yield "integer: must between of 3-5 (success, min)" => [['quantity' => '3'], ['quantity' => 'integer|between:3,5'], true];
        yield "integer: must between of 3-5 (success, max)" => [['quantity' => '5'], ['quantity' => 'integer|between:3,5'], true];
        yield "integer: must between of 3-5 (too few)" => [['quantity' => '2'], ['quantity' => 'integer|between:3,5'], false];
        yield "integer: must between of 3-5 (too many)" => [['quantity' => '6'], ['quantity' => 'integer|between:3,5'], false];

        // test string must be within certain length
        yield "string: must be minimum 4 chars (success)" => [['name' => '1234'], ['name' => 'min:4'], true];
        yield "string: must be minimum 5 chars (fails)" => [['name' => '1234'], ['name' => 'min:5'], false];
        yield "string: must be maximum 5 chars (success)" => [['name' => '12345'], ['name' => 'max:5'], true];
        yield "string: must be maximum 4 chars (fails)" => [['name' => '12345'], ['name' => 'max:4'], false];
        yield "string: must between 3-5 chars (success, min)" => [['name' => '123'], ['name' => 'between:3,5'], true];
        yield "string: must between 3-5 chars (success, max)" => [['name' => '12345'], ['name' => 'between:3,5'], true];
        yield "string: must between 3-5 chars (too few)" => [['name' => '12'], ['name' => 'between:3,5'], false];
        yield "string: must between 3-5 chars (too many)" => [['name' => '123456'], ['name' => 'between:3,5'], false];
    }
}
