# 6. Test Doubles

Gerard Meszaros introduces the concept of Test Doubles in his “xUnit Test Patterns” book like so:

> Sometimes it is just plain hard to test the system under test (SUT) because it depends on other components that cannot be used in the test environment. This could be because they aren’t available, they will not return the results needed for the test or because executing them would have undesirable side effects. In other cases, our test strategy requires us to have more control or visibility of the internal behavior of the SUT.
> 
> When we are writing a test in which we cannot (or chose not to) use a real depended-on component (DOC), we can replace it with a Test Double. The Test Double doesn’t have to behave exactly like the real DOC; it merely has to provide the same API as the real one so that the SUT thinks it is the real one!

The `createStub(string $type)` and `createMock(string $type)` methods can be used in a test to automatically generate an object that can act as a test double for the specified original type (interface or extendable class). This test double object can be used in every context where an object of the original type is expected or required.

Limitation: final, private, and static methods

Please note that `final`, `private`, and `static` methods cannot be doubled. They are ignored by PHPUnit’s test double functionality and retain their original behavior except for `static` methods which will be replaced by a method throwing an exception.

Limitation: Enumerations and readonly classes

Enumerations (`enum`) are `final` classes and therefore cannot be doubled. `readonly` classes cannot be extended by classes that are not `readonly` and therefore cannot be doubled.

Favour doubling interfaces over doubling classes

Not only because of the limitations mentioned above, but also to improve your software design, favour the doubling of interfaces over the doubling of classes.

## Test Stubs

The practice of replacing an object with a test double that (optionally) returns configured return values is referred to as _stubbing_. You can use a _test stub_ to “replace a real component on which the SUT depends so that the test has a control point for the indirect inputs of the SUT. This allows the test to force the SUT down paths it might not otherwise execute” (Gerard Meszaros).

### Creating Test Stubs

#### `createStub()`

The `createStub(string $type)` method returns a test stub for the specified interface or extendable class.

All methods of the original type are replaced with an implementation that returns an automatically generated value that satisfies the method’s return type declaration without calling the original method. These methods are referred to as “doubled methods”.

The behaviour of doubled methods can be configured using methods such as `willReturn()` or `willThrowException()`. These methods are explained later.

#### `createStubForIntersectionOfInterfaces()`

The `createStubForIntersectionOfInterfaces(array $interfaces)` method can be used to create a test stub for an intersection of interfaces based on a list of interface names.

Consider you have the following interfaces `X` and `Y`:

Example 6.1 An interface named X
```php
<?php declare(strict_types=1);
interface X
{
    public function m(): bool;
}
```

Example 6.2 An interface named Y
```php
<?php declare(strict_types=1);
interface Y
{
    public function n(): int;
}
```

And you have a class that you want to test named `Z`:

Example 6.3 A class named Z
```php
<?php declare(strict_types=1);
interface Y
{
    public function n(): int;
}
```

To test `Z`, we need an object that satisfies the intersection type `X&Y`. We can use the `createStubForIntersectionOfInterfaces(array $interfaces)` method to create a test stub that satisfies `X&Y` like so:

Example 6.4 Using createStubForIntersectionOfInterfaces() to create a test stub for an intersection type
```php
<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class StubForIntersectionExampleTest extends TestCase
{
    public function testCreateStubForIntersection(): void
    {
        $o = $this->createStubForIntersectionOfInterfaces([X::class, Y::class]);

        // $o is of type X ...
        $this->assertInstanceOf(X::class, $o);

        // ... and $o is of type Y
        $this->assertInstanceOf(Y::class, $o);
    }
}
```

#### `createConfiguredStub()`

The `createConfiguredStub()` method is a convenience wrapper around `createStub()` that allows configuring return values using an associative array (`['methodName' => <return value>]`):

Example 6.5 Using createConfiguredStub() to create a test stub and configure return values
```php
<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class CreateConfiguredStubExampleTest extends TestCase
{
    public function testCreateConfiguredStub(): void
    {
        $o = $this->createConfiguredStub(
            SomeInterface::class,
            [
                'doSomething'     => 'foo',
                'doSomethingElse' => 'bar',
            ]
        );

        // $o->doSomething() now returns 'foo'
        $this->assertSame('foo', $o->doSomething());

        // $o->doSomethingElse() now returns 'bar'
        $this->assertSame('bar', $o->doSomethingElse());
    }
}
```

### Configuring Test Stubs

#### `willReturn()`

Using the `willReturn()` method, for instance, you can configure a doubled method to return a specified value when it is called. This configured value must be compatible with the method’s return type declaration.

Consider that we have a class that we want to test, `SomeClass`, which depends on `Dependency`:

Example 6.6 The class we want to test
```php
<?php declare(strict_types=1);
final class SomeClass
{
    public function doSomething(Dependency $dependency): string
    {
        $result = '';

        // ...

        return $result . $dependency->doSomething();
    }
}
```

Example 6.7 The dependency we want to stub
```php
<?php declare(strict_types=1);
interface Dependency
{
    public function doSomething(): string;
}
```

Here is a first example of how to use the `createStub(string $type)` method to create a test stub for `Dependency` so that we can test `SomeClass` without using a real implementation of `Dependency`:

Example 6.8 Stubbing a method call to return a fixed value
```php
<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class SomeClassTest extends TestCase
{
    public function testDoesSomething(): void
    {
        $sut = new SomeClass;

        // Create a test stub for the Dependency interface
        $dependency = $this->createStub(Dependency::class);

        // Configure the test stub
        $dependency->method('doSomething')
            ->willReturn('foo');

        $result = $sut->doSomething($dependency);

        $this->assertStringEndsWith('foo', $result);
    }
}
```

Limitation: Methods named “method”

The example shown above only works when the original interface or class does not declare a method named “method”.

If the original interface or class does declare a method named “method” then `$stub->expects($this->any())->method('doSomething')->willReturn('foo');` has to be used.

Deprecation: Doubling interfaces (or classes) that have a method named “method”

As of PHPUnit 10.3, the support for doubling interfaces (or classes) that have a method named “method” is soft-deprecated, meaning the deprecation is in documentation only.

Starting with PHPUnit 11, doubling interfaces (or classes) that have a method named “method” will trigger a deprecation warning. Support for doubling interfaces (or classes) that have a method named “method” will be removed in PHPUnit 12.

In the example shown above, we first use the `createStub()` method to create a test stub, an object that looks like an instance of `Dependency`.

We then use the [Fluent Interface](http://martinfowler.com/bliki/FluentInterface.html) that PHPUnit provides to specify the behavior for the test stub.

“Behind the scenes”, PHPUnit automatically generates a new PHP class that implements the desired behavior when the `createStub()` method is used.

Please note that `createStub()` will automatically and recursively stub return values based on a method’s return type. Consider the example shown below:

Example 6.9 A method with a return type declaration
```php
<?php declare(strict_types=1);
class C
{
    public function m(): D
    {
        // Do something.
    }
}
```

In the example shown above, the `C::m()` method has a return type declaration indicating that this method returns an object of type `D`. When a test double for `C` is created and no return value is configured for `m()` using `willReturn()` (see above), for instance, then when `m()` is invoked PHPUnit will automatically create a test double for `D` to be returned.

Similarly, if `m` had a return type declaration for a scalar type then a return value such as `0` (for `int`), `0.0` (for `float`), or `[]` (for `array`) would be generated.

A list of desired return values can also be specified. Here is an example:

Example 6.10 Using willReturn() to stub a method call to return a list of values in the specified order
```php
<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class OnConsecutiveCallsExampleTest extends TestCase
{
    public function testOnConsecutiveCallsStub(): void
    {
        // Create a stub for the SomeClass class.
        $stub = $this->createStub(SomeClass::class);

        // Configure the stub.
        $stub->method('doSomething')
            ->willReturn(1, 2, 3);

        // $stub->doSomething() returns a different value each time
        $this->assertSame(1, $stub->doSomething());
        $this->assertSame(2, $stub->doSomething());
        $this->assertSame(3, $stub->doSomething());
    }
}
```

#### `willThrowException()`

Instead of returning a value, a stubbed method can also raise an exception. Here is an example that shows how to use `willThrowException()` to do this:

Example 6.11 Using willThrowException() to stub a method call to throw an exception
```php
<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class ThrowExceptionExampleTest extends TestCase
{
    public function testThrowExceptionStub(): void
    {
        // Create a stub for the SomeClass class.
        $stub = $this->createStub(SomeClass::class);

        // Configure the stub.
        $stub->method('doSomething')
            ->willThrowException(new Exception);

        // $stub->doSomething() throws Exception
        $stub->doSomething();
    }
}
```

#### `willReturnArgument()`

Sometimes you want to return one of the arguments of a method call (unchanged) as the result of a stubbed method call. Here is an example that shows how you can achieve this using `willReturnArgument()`:

Example 6.12 Using willReturnArgument() to stub a method call to return one of the arguments
```php
<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class ReturnArgumentExampleTest extends TestCase
{
    public function testReturnArgumentStub(): void
    {
        // Create a stub for the SomeClass class.
        $stub = $this->createStub(SomeClass::class);

        // Configure the stub.
        $stub->method('doSomething')
            ->willReturnArgument(0);

        // $stub->doSomething('foo') returns 'foo'
        $this->assertSame('foo', $stub->doSomething('foo'));

        // $stub->doSomething('bar') returns 'bar'
        $this->assertSame('bar', $stub->doSomething('bar'));
    }
}
```

#### `willReturnCallback()`

When the stubbed method call should return a calculated value instead of a fixed one (see `willReturn()`) or an (unchanged) argument (see `willReturnArgument()`), you can use `willReturnCallback()` to have the stubbed method return the result of a callback function or method. Here is an example:

Example 6.13 Using willReturnCallback() to stub a method call to return a value from a callback
```php
<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class ReturnCallbackExampleTest extends TestCase
{
    public function testReturnCallbackStub(): void
    {
        // Create a stub for the SomeClass class.
        $stub = $this->createStub(SomeClass::class);

        // Configure the stub.
        $stub->method('doSomething')
            ->willReturnCallback('str_rot13');

        // $stub->doSomething($argument) returns str_rot13($argument)
        $this->assertSame('fbzrguvat', $stub->doSomething('something'));
    }
}
```

#### `willReturnSelf()`

When testing a fluent interface, it is sometimes useful to have a stubbed method return a reference to the stubbed object. Here is an example that shows how you can use `willReturnSelf()` to achieve this:

Example 6.14 Using willReturnSelf() to stub a method call to return a reference to the stub object
```php
<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class ReturnSelfExampleTest extends TestCase
{
    public function testReturnSelf(): void
    {
        // Create a stub for the SomeClass class.
        $stub = $this->createStub(SomeClass::class);

        // Configure the stub.
        $stub->method('doSomething')
            ->willReturnSelf();

        // $stub->doSomething() returns $stub
        $this->assertSame($stub, $stub->doSomething());
    }
}
```

#### `willReturnMap()`

Sometimes a stubbed method should return different values depending on a predefined list of arguments. Here is an example that shows how to use `willReturnMap()` to create a map that associates arguments with corresponding return values:

Example 6.15 Using willReturnMap() to stub a method call to return the value from a map
```php
<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class ReturnMapExampleTest extends TestCase
{
    public function testReturnMapStub(): void
    {
        // Create a stub for the SomeClass class.
        $stub = $this->createStub(SomeClass::class);

        // Create a map of arguments to return values.
        $map = [
            ['a', 'b', 'c', 'd'],
            ['e', 'f', 'g', 'h'],
        ];

        // Configure the stub.
        $stub->method('doSomething')
            ->willReturnMap($map);

        // $stub->doSomething() returns different values depending on
        // the provided arguments.
        $this->assertSame('d', $stub->doSomething('a', 'b', 'c'));
        $this->assertSame('h', $stub->doSomething('e', 'f', 'g'));
    }
}
```

## Mock Objects

The practice of replacing an object with a test double that verifies expectations, for instance asserting that a method has been called, is referred to as _mocking_.

You can use a _mock object_ “as an observation point that is used to verify the indirect outputs of the SUT as it is exercised. Typically, the mock object also includes the functionality of a test stub in that it must return values to the SUT if it hasn’t already failed the tests but the emphasis is on the verification of the indirect outputs. Therefore, a mock object is a lot more than just a test stub plus assertions; it is used in a fundamentally different way” (Gerard Meszaros).

### Creating Mock Objects

#### `createMock()`

The `createMock(string $type)` method returns a mock object for the specified interface or extendable class.

All methods of the original type are replaced with an implementation that returns an automatically generated value that satisfies the method’s return type declaration without calling the original method. These methods are referred to as “doubled methods”.

The behaviour of doubled methods can be configured using methods such as `willReturn()` or `willThrowException()`. These methods are explained in the section on Test Stubs above.

Expectations for invocations of doubled methods (“method must be called with specified arguments”, “method must not be called”, etc.) can be configured using the mock object’s `expects()` method.

#### `createMockForIntersectionOfInterfaces()`

The `createMockForIntersectionOfInterfaces(array $interfaces)` method can be used to create a mock object for an intersection of interfaces based on a list of interface names.

Consider you have the following interfaces `X` and `Y`:

Example 6.16 An interface named X
```php
<?php declare(strict_types=1);
interface X
{
    public function m(): bool;
}
```

Example 6.17 An interface named Y
```php
<?php declare(strict_types=1);
interface Y
{
    public function n(): int;
}
```

And you have a class that you want to test named `Z`:

Example 6.18 A class named Z
```php
<?php declare(strict_types=1);
final class Z
{
    public function doSomething(X&Y $input): bool
    {
        $result = false;

        // ...

        return $result;
    }
}
```

To test `Z`, we need an object that satisfies the intersection type `X&Y`. We can use the `createMockForIntersectionOfInterfaces(array $interfaces)` method to create a test stub that satisfies `X&Y` like so:

Example 6.19 Using createMockForIntersectionOfInterfaces() to create a mock object for an intersection type
```php
<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class MockForIntersectionExampleTest extends TestCase
{
    public function testCreateMockForIntersection(): void
    {
        $o = $this->createMockForIntersectionOfInterfaces([X::class, Y::class]);

        // $o is of type X ...
        $this->assertInstanceOf(X::class, $o);

        // ... and $o is of type Y
        $this->assertInstanceOf(Y::class, $o);
    }
}
```

#### `createConfiguredMock()`

The `createConfiguredMock()` method is a convenience wrapper around `createMock()` that allows configuring return values using an associative array (`['methodName' => <return value>]`):

Example 6.20 Using createConfiguredMock() to create a mock object and configure return values
```php
<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class CreateConfiguredMockExampleTest extends TestCase
{
    public function testCreateConfiguredMock(): void
    {
        $o = $this->createConfiguredMock(
            SomeInterface::class,
            [
                'doSomething'     => 'foo',
                'doSomethingElse' => 'bar',
            ],
        );

        // $o->doSomething() now returns 'foo'
        $this->assertSame('foo', $o->doSomething());

        // $o->doSomethingElse() now returns 'bar'
        $this->assertSame('bar', $o->doSomethingElse());
    }
}
```

#### `getMockForAbstractClass()`

The `getMockForAbstractClass()` method returns a mock object for an abstract class. All abstract methods of the given abstract class are mocked. This allows for testing the concrete methods of an abstract class.

Example 6.21 An abstract class with a concrete method
```php
<?php declare(strict_types=1);
abstract class AbstractClass
{
    public function concreteMethod()
    {
        return $this->abstractMethod();
    }

    abstract public function abstractMethod();
}
```

Example 6.22 A test for a concrete method of an abstract class
```php
<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class AbstractClassTest extends TestCase
{
    public function testConcreteMethod(): void
    {
        $stub = $this->getMockForAbstractClass(AbstractClass::class);

        $stub->expects($this->any())
            ->method('abstractMethod')
            ->willReturn(true);

        $this->assertTrue($stub->concreteMethod());
    }
}
```
> [!WARNING]
> Deprecation: `getMockForAbstractClass()` is deprecated
> 
> As of PHPUnit 10.1, the `getMockForAbstractClass()` method is soft-deprecated, meaning its declaration is annotated with `@deprecated` so that IDEs and static analysis tools can warn about its usage.
> 
> Starting with PHPUnit 11, using the `getMockForAbstractClass()` method will trigger a deprecation warning. The method will be removed in PHPUnit 12.

#### `getMockForTrait()`

The `getMockForTrait()` method returns a mock object that uses a specified trait. All abstract methods of the given trait are mocked. This allows for testing the concrete methods of a trait.

Example 6.23 A trait with an abstract method
```php
<?php declare(strict_types=1);

trait AbstractTrait
{
    public function concreteMethod()
    {
        return $this->abstractMethod();
    }

    abstract public function abstractMethod();
}
```

Example 6.24 A test for a concrete method of a trait
```php
<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class AbstractTraitTest extends TestCase
{
    public function testConcreteMethod(): void
    {
        $mock = $this->getMockForTrait(AbstractTrait::class);

        $mock->expects($this->any())
            ->method('abstractMethod')
            ->willReturn(true);

        $this->assertTrue($mock->concreteMethod());
    }
}
```

> [!WARNING]
> Deprecation: `getMockForTrait()` is deprecated
> 
> As of PHPUnit 10.1, the `getMockForTrait()` method is soft-deprecated, meaning its declaration is annotated with `@deprecated` so that IDEs and static analysis tools can warn about its usage.
> 
> Starting with PHPUnit 11, using the `getMockForTrait()` method will trigger a deprecation warning. The method will be removed in PHPUnit 12.

#### `getMockFromWsdl()`

When your application interacts with a web service you want to test it without actually interacting with the web service. To create stubs and mocks of web services, the `getMockFromWsdl()` method can be used.

This method returns a mock object based on a web service description in WSDL whereas `createMock()` returns a mock object based on an interface or on a class.

Here is an example that shows how to stub the web service described in `HelloService.wsdl`:

Example 6.25 Stubbing a web service
```php
<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class WsdlStubExampleTest extends TestCase
{
    public function testWebserviceCanBeStubbed(): void
    {
        $service = $this->getMockFromWsdl(__DIR__ . '/HelloService.wsdl');

        $service->method('sayHello')
            ->willReturn('Hello');

        $this->assertSame('Hello', $service->sayHello('message'));
    }
}
```
> [!WARNING]
> Deprecation: `getMockFromWsdl()` is deprecated
>
> As of PHPUnit 10.1, the `getMockFromWsdl()` method is soft-deprecated, meaning its declaration is annotated with `@deprecated` so that IDEs and static analysis tools can warn about its usage.
> 
> Starting with PHPUnit 11, using the `getMockFromWsdl()` method will trigger a deprecation warning. The method will be removed in PHPUnit 12.

### Configuring Mock Objects

Here is an example: suppose we want to test that the correct method, `update()` in our example, is called on an object that observes another object.

Here is the code for the `Subject` class and the `Observer` interface that are part of the System under Test (SUT):

Example 6.26 Subject class that is part of the System under Test (SUT)
```php
<?php declare(strict_types=1);
final class Subject
{
    private array $observers = [];

    public function attach(Observer $observer): void
    {
        $this->observers[] = $observer;
    }

    public function doSomething(): void
    {
        // ...

        $this->notify('something');
    }

    private function notify(string $argument): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($argument);
        }
    }

    // ...
}
```

Example 6.27 Observer interface that is part of the System under Test (SUT)
```php
<?php declare(strict_types=1);
interface Observer
{
    public function update(string $argument): void;
}
```

Here is an example that shows how to use a mock object to test the interaction between `Subject` and `Observer` objects:

Example 6.28 Testing that a method gets called once and with a specified argument
```php
<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class SubjectTest extends TestCase
{
    public function testObserversAreUpdated(): void
    {
        $observer = $this->createMock(Observer::class);

        $observer->expects($this->once())
            ->method('update')
            ->with($this->identicalTo('something'));

        $subject = new Subject;

        $subject->attach($observer);

        $subject->doSomething();
    }
}
```

___

We first use the `createMock()` method to create a mock object for the `Observer`.

Because we are interested in verifying the communication between two objects (that a method is called and which arguments it is called with), we use the `expects()` and `with()` methods to specify what this communication should look like.

The `with()` method can take any number of arguments, corresponding to the number of arguments to the method being mocked. You can specify more advanced constraints on the method’s arguments than a simple match.

[Constraints](https://docs.phpunit.de/en/11.0/test-doubles.htmlassertions.html#appendixes-assertions-assertthat-tables-constraints) shows the constraints that can be applied to method arguments and here is a list of the matchers that are available to specify the number of invocations:

-   `any()` returns a matcher that matches when the method it is evaluated for is executed zero or more times
    
-   `never()` returns a matcher that matches when the method it is evaluated for is never executed
    
-   `atLeastOnce()` returns a matcher that matches when the method it is evaluated for is executed at least once
    
-   `once()` returns a matcher that matches when the method it is evaluated for is executed exactly once
    
-   `atMost(int $count)` returns a matcher that matches when the method it is evaluated for is executed at most `$count` times
    
-   `exactly(int $count)` returns a matcher that matches when the method it is evaluated for is executed exactly `$count` times
    

## MockBuilder API

As mentioned before, when the defaults used by the `createStub()` and `createMock()` methods to generate the test double do not match your needs then you can use the `getMockBuilder($type)` method to customize the test double generation using a fluent interface. The methods provided by the Mock Builder are documented below.

### `setMockClassName()`

`setMockClassName($name)` can be used to specify a class name for the generated test double class.

Deprecation: `setMockClassName()` is deprecated

As of PHPUnit 10.3, the `setMockClassName()` method is soft-deprecated, meaning its declaration is annotated with `@deprecated` so that IDEs and static analysis tools can warn about its usage.

Starting with PHPUnit 11, using the `setMockClassName()` method will trigger a deprecation warning. The method will be removed in PHPUnit 12.

### `setConstructorArgs()`

`setConstructorArgs(array $args)` can be called to provide a parameter array that is passed to the original class’ constructor (which is not replaced with a dummy implementation by default).

### `disableOriginalConstructor()`

`disableOriginalConstructor()` can be used to disable the call to the constructor of the original class.

`enableOriginalConstructor()` can be used to make it explicit that the constructor of the original class should be called (which is the default behaviour).

### `disableOriginalClone()`

`disableOriginalClone()` can be used to disable the call to the clone constructor of the original class.

`enableOriginalClone()` can be used to make it explicit that the clone constructor of the original class should be called (which is the default behaviour).

### `enableArgumentCloning()`

`enableArgumentCloning()` can be used to enable the cloning of arguments passed to doubled methods.

`disableArgumentCloning()` can be used to make it explicit that arguments passed to doubled methods are not cloned (which is the default behaviour).

Deprecation: `enableArgumentCloning()` and `disableArgumentCloning()` are deprecated

As of PHPUnit 10.1, the `enableArgumentCloning()` and `disableArgumentCloning()` methods are soft-deprecated, meaning their declaration is annotated with `@deprecated` so that IDEs and static analysis tools can warn about their usage.

Starting with PHPUnit 11, using the `enableArgumentCloning()` and `disableArgumentCloning()` methods will trigger a deprecation warning. The methods will be removed in PHPUnit 12.

### `disableAutoReturnValueGeneration()`

`disableAutoReturnValueGeneration()` can be used to disable the automatic generation of return values when no return value is configured.

`enableAutoReturnValueGeneration()` can be used to make it explicit that automatic generation of return values when no return value is configured is enabled (which is the default).

Deprecation: `disableAutoReturnValueGeneration()` and `enableAutoReturnValueGeneration()` are deprecated

As of PHPUnit 10.3, the `disableAutoReturnValueGeneration()` and `enableAutoReturnValueGeneration()` methods are soft-deprecated, meaning their declaration is annotated with `@deprecated` so that IDEs and static analysis tools can warn about their usage.

Starting with PHPUnit 11, using the `disableAutoReturnValueGeneration()` and `enableAutoReturnValueGeneration()` methods will trigger a deprecation warning. The methods will be removed in PHPUnit 12.

### `disallowMockingUnknownTypes()`

`disallowMockingUnknownTypes()` can be used to disallow the doubling of unknown types.

`allowMockingUnknownTypes()` can be used to make it explicit that the doubling of unknown types is allowed (which is the default).

Deprecation: `disallowMockingUnknownTypes()` and `allowMockingUnknownTypes()` are deprecated

As of PHPUnit 10.1, the `disallowMockingUnknownTypes()` and `allowMockingUnknownTypes()` methods are soft-deprecated, meaning their declaration is annotated with `@deprecated` so that IDEs and static analysis tools can warn about their usage.

Starting with PHPUnit 11, using the `disallowMockingUnknownTypes()` and `allowMockingUnknownTypes()` methods will trigger a deprecation warning. The methods will be removed in PHPUnit 12.

### `disableAutoload()`

`disableAutoload()` can be used to disable PHP’s autoloading functionality during the generation of the test double class.

`enableAutoload()` can be used to make it explicit that PHP’s autoloading functionality should be enabled (which is the default behaviour).

Deprecation: `disableAutoload()` and `enableAutoload()` are deprecated

As of PHPUnit 10.1, the `disableAutoload()` and `enableAutoload()` methods are soft-deprecated, meaning their declaration is annotated with `@deprecated` so that IDEs and static analysis tools can warn about their usage.

Starting with PHPUnit 11, using the `disableAutoload()` and `enableAutoload()` methods will trigger a deprecation warning. The methods will be removed in PHPUnit 12.

### `enableProxyingToOriginalMethods()`

`enableProxyingToOriginalMethods()` can be used to enable the invocation of the original methods. The object to be used for invoking the original methods must be configured using `setProxyTarget()`.

`disableProxyingToOriginalMethods()` can be used to make it explicit that the original methods are not invoked (which is the default behaviour).

Deprecation: `enableProxyingToOriginalMethods()`, `setProxyTarget()`, and `disableProxyingToOriginalMethods()` are deprecated

As of PHPUnit 10.1, the `enableProxyingToOriginalMethods()`, `setProxyTarget()`, and `disableProxyingToOriginalMethods()` methods are soft-deprecated, meaning their declaration is annotated with `@deprecated` so that IDEs and static analysis tools can warn about their usage.

Starting with PHPUnit 11, using the `enableProxyingToOriginalMethods()`, `setProxyTarget()`, and `disableProxyingToOriginalMethods()` methods will trigger a deprecation warning. The methods will be removed in PHPUnit 12.

### `onlyMethods()`

`onlyMethods(array $methods)` can be called on the Mock Builder object to specify the methods that are to be replaced with a configurable test double. The behavior of the other methods is not changed. The specified methods must exist in the class that is mocked.

### `addMethods()`

`addMethods(array $methods)` can be called on the Mock Builder object to specify the methods that do not exist in the interface or class that is mocked. Methods that do exist in the interface or class remain unchanged.

Deprecation: `addMethods()` is deprecated

As of PHPUnit 10.1, the `addMethods()` method is soft-deprecated, meaning its declaration is annotated with `@deprecated` so that IDEs and static analysis tools can warn about its usage.

Starting with PHPUnit 11, using the `addMethods()` method will trigger a deprecation warning. The method will be removed in PHPUnit 12.

### `getMock()`

`getMock()` generates and returns a mock object based on the configuration made using previous methods calls. The call to `getMock()` must be the last in the method chain.

### `getMockForAbstractClass()`

`getMockForAbstractClass()` generates and returns a mock object based on the configuration made using previous methods calls. The call to `getMockForAbstractClass()` must be the last in the method chain.

Deprecation: `getMockForAbstractClass()` is deprecated

As of PHPUnit 10.1, the `getMockForAbstractClass()` method is soft-deprecated, meaning its declaration is annotated with `@deprecated` so that IDEs and static analysis tools can warn about its usage.

Starting with PHPUnit 11, using the `getMockForAbstractClass()` method will trigger a deprecation warning. The method will be removed in PHPUnit 12.

### `getMockForTrait()`

`getMockForTrait()` generates and returns a mock object based on the configuration made using previous methods calls. The call to `getMockForTrait()` must be the last in the method chain.

Deprecation: `getMockForTrait()` is deprecated

As of PHPUnit 10.1, the `getMockForTrait()` method is soft-deprecated, meaning its declaration is annotated with `@deprecated` so that IDEs and static analysis tools can warn about its usage.

Starting with PHPUnit 11, using the `getMockForTrait()` method will trigger a deprecation warning. The method will be removed in PHPUnit 12.

Here is an example that shows how to use the Mock Builder’s fluent interface to configure the creation of a test stub. The configuration of this test double uses the same best practice defaults used by `createStub()` and `createMock()`:

Example 6.29 Using the Mock Builder API to configure how the test double class is generated
```php
<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class MockBuilderExampleTest extends TestCase
{
    public function testStub(): void
    {
        // Create a stub for the SomeClass class.
        $stub = $this->getMockBuilder(SomeClass::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        // Configure the stub.
        $stub->method('doSomething')
            ->willReturn('foo');

        // Calling $stub->doSomething() will now return
        // 'foo'.
        $this->assertSame('foo', $stub->doSomething());
    }
}
```