<?php
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Calculator;

class CalculatorTest extends TestCase {

    protected Calculator $calculator;

    public function setUp(): void
    {
        // ...code
    }

    public static function additionProvider(): array
    {
        // ...code
    }

    #[DataProvider('additionProvider')]
    public function testAdd($a, $b, $expected): void 
    {

        // ...code
    }

    public function testDivisor(): void 
    {
        // ...code
    }

    public function testExceptionDivisor(): void 
    {
        // On teste les exception avec $this->expectExceptionMessage()
    }
}
