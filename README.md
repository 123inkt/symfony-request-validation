[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.4-8892BF)](https://php.net/)
[![Minimum Symfony Version](https://img.shields.io/badge/symfony-%3E%3D%204.4-brightgreen)](https://symfony.com/doc/current/validation.html)
![Run tests](https://github.com/123inkt/symfony-request-validation/workflows/Run%20tests/badge.svg)

# Symfony Request Validation
A request validation component for Symfony. Ease the validation of request properties without the need for an entire Symfony Form.

## Installation
Include the library as dependency in your own project via: 
```
composer require "digitalrevolution/symfony-request-validation"
```

## Usage

1) Create your own `ExampleRequest` class which extends the `AbstractValidatedRequest` class.
2) Configure your own `ValidationRules`. See the [Validation shorthand library](https://github.com/123inkt/symfony-validation-shorthand) for 
more information about the rules.
3) Ensure your `ExampleRequest` class is registered as [service in your Symfony project](https://symfony.com/doc/current/service_container.html).  

```php
use DigitalRevolution\SymfonyRequestValidation\AbstractValidatedRequest;
use DigitalRevolution\SymfonyRequestValidation\ValidationRules;

class ExampleRequest extends AbstractValidatedRequest
{
    protected function getValidationRules(): ValidationRules
    {
        return new ValidationRules([
            'request' => [
                'productId'   => 'required|int|min:0',
                'productName' => 'required|string|between:50,255'
            ]
        ]);
    }

    public function getProductId(): int
    {
        return $this->request->request->getInt('productId');
    }
    
    public function getProductName(): int
    {
        return $this->request->request->getString('productName');
    }
}
```

All that remains is using your `ExampleRequest` class in your `Controller` and it will only be invoked when the request validation passes.
```php
class ExampleController
{
    /**
     * @Route("/", name="my_example")
     */
    public function index(ExampleRequest $request): Response
    {
        return ...;
    }
}
```

## Invalid request handling

By default if a request is invalid an `InvalidRequestException` will be thrown. If you prefer a different behaviour, overwrite the `handleViolations`
method.
```php
class ExampleRequest extends AbstractValidatedRequest
{
    ...
    
    protected function handleViolations(ConstraintViolationListInterface $violationList): void
    {
        $renderer = new ViolationListRenderer($violationList);
        $this->logger->error($renderer->render());
    }
}
```

Note: if no exceptions are thrown in the `handleViolations`, you'll always receive a request in your `Controller`. Use `Request->isValid()` to verify
the request is valid. 

## About us

At 123inkt (Part of Digital Revolution B.V.), every day more than 50 development professionals are working on improving our internal ERP
and our several shops. Do you want to join us? [We are looking for developers](https://www.werkenbij123inkt.nl/zoek-op-afdeling/it).
