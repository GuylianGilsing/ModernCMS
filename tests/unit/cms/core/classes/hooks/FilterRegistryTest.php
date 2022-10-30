<?php

declare(strict_types=1);

namespace Tests\ModernCMS\Core\Classes\Hooks;

use ErrorException;
use ModernCMS\Core\Classes\Hooks\FilterRegistry;
use PHPUnit\Framework\TestCase;

class FilterRegistryTest extends TestCase
{
    public function testIfCanRegisterFilter(): void
    {
        // Arrange
        $registry = new FilterRegistry();

        $filterNameToAdd = 'test_filter_1';

        // Act
        $registry->register($filterNameToAdd);

        // Assert
        $internalData = $registry->getInternalData();

        $this->assertArrayHasKey($filterNameToAdd, $internalData);
    }

    public function testIfCannotRegisterFilterTwice(): void
    {
        // Arrange
        $registry = new FilterRegistry();

        $filterNameToAdd = 'test_filter_1';

        // Assert
        $this->expectException(ErrorException::class);

        // Act
        $registry->register($filterNameToAdd);
        $registry->register($filterNameToAdd);

        // Assert
        $internalData = $registry->getInternalData();

        $this->assertArrayHasKey($filterNameToAdd, $internalData);
    }

    public function testIfHasMethodReturnsTrueWhenNameExists(): void
    {
        // Arrange
        $internalData = [];
        $registry = new FilterRegistry();

        $filterNameToAdd = 'test_filter_1';

        // Act
        $registry->register($filterNameToAdd);
        $actionExists = $registry->has($filterNameToAdd);

        // Assert
        $internalData = $registry->getInternalData();

        $this->assertTrue($actionExists);
        $this->assertArrayHasKey($filterNameToAdd, $internalData);
    }

    public function testIfHasMethodReturnsFalseWhenNameDoesntExist(): void
    {
        // Arrange
        $registry = new FilterRegistry();

        // Act
        $actionExists = $registry->has('test_filter_1');

        // Assert
        $internalData = $registry->getInternalData();

        $this->assertFalse($actionExists);
        $this->assertEmpty($internalData);
    }

    public function testIfCanRegisterCallbackOnFilter(): void
    {
        // Arrange
        $registry = new FilterRegistry();

        $filterNameToAdd = 'test_filter_1';

        // Act
        $registry->register($filterNameToAdd);
        $registry->addCallbackToFilter($filterNameToAdd, fn() => 'callback 1');

        // Assert
        $internalData = $registry->getInternalData();

        $this->assertArrayHasKey($filterNameToAdd, $internalData);

        $this->assertIsArray($internalData[$filterNameToAdd]);
        $this->assertNotEmpty($internalData[$filterNameToAdd]);
        $this->assertCount(1, $internalData[$filterNameToAdd]);
    }

    public function testIfCanRegisterMultipleCallbacksOnFilter(): void
    {
        // Arrange
        $registry = new FilterRegistry();

        $filterNameToAdd = 'test_filter_1';

        // Act
        $registry->register($filterNameToAdd);

        $registry->addCallbackToFilter($filterNameToAdd, fn() => 'callback 1');
        $registry->addCallbackToFilter($filterNameToAdd, fn() => 'callback 2');
        $registry->addCallbackToFilter($filterNameToAdd, fn() => 'callback 3');

        // Assert
        $internalData = $registry->getInternalData();

        $this->assertArrayHasKey($filterNameToAdd, $internalData);

        $this->assertIsArray($internalData[$filterNameToAdd]);
        $this->assertNotEmpty($internalData[$filterNameToAdd]);
        $this->assertCount(3, $internalData[$filterNameToAdd]);
    }

    public function testIfCannotRegisterCallbackOnFilterThatDoesntExist(): void
    {
        // Arrange
        $registry = new FilterRegistry();

        // Assert
        $this->expectException(ErrorException::class);

        // Act
        $registry->addCallbackToFilter('test_filter_1', fn() => 'callback 1');

        // Assert
        $internalData = $registry->getInternalData();

        $this->assertEmpty($internalData);
    }

    public function testIfDispatchingFilterWithNoCallbacksReturnsTheSameFilterValue(): void
    {
        // Arrange
        $registry = new FilterRegistry();
        $registry->register('test_filter_1');

        $filterValue = ['1', '2', '3'];

        // Act
        $returnedFilterValue = $registry->dispatch('test_filter_1', $filterValue);

        // Assert
        $this->assertIsArray($returnedFilterValue);
        $this->assertCount(3, $returnedFilterValue);

        $this->assertEquals($returnedFilterValue, $filterValue);
    }

    public function testIfFilterValueGetsPassedAlongTheFilterChain(): void
    {
        // Arrange
        $registry = new FilterRegistry();
        $registry->register('test_filter_1');

        // Act
        $registry->addCallbackToFilter('test_filter_1', function(array $values)
        {
            $values[] = 'callback_1';

            return $values;
        });

        $registry->addCallbackToFilter('test_filter_1', function(array $values)
        {
            $values[] = 'callback_2';

            return $values;
        });

        $registry->addCallbackToFilter('test_filter_1', function(array $values)
        {
            $values[] = 'callback_3';

            return $values;
        });

        $filterValues = $registry->dispatch('test_filter_1', []);

        // Assert
        $expectedFilterValues = ['callback_1', 'callback_2', 'callback_3'];

        $this->assertIsArray($filterValues);
        $this->assertCount(3, $filterValues);

        $this->assertEquals($expectedFilterValues, $filterValues);
    }

    public function testIfMultipleParametersGetPassedInRightOrder(): void
    {
        // Arrange
        $registry = new FilterRegistry();
        $registry->register('test_filter_1');

        // Act
        $registry->addCallbackToFilter('test_filter_1', function(array $values, string $param1, string $param2, string $param3)
        {
            $values[] = 'callback_1';

            echo json_encode([$param1, $param2, $param3]);

            return $values;
        });

        $registry->addCallbackToFilter('test_filter_1', function(array $values, string $param1, string $param2, string $param3)
        {
            $values[] = 'callback_2';

            echo json_encode([$param1, $param2, $param3]);

            return $values;
        });

        $registry->addCallbackToFilter('test_filter_1', function(array $values, string $param1, string $param2, string $param3)
        {
            $values[] = 'callback_3';

            echo json_encode([$param1, $param2, $param3]);

            return $values;
        });

        ob_start();
        $filterValues = $registry->dispatch('test_filter_1', [], ['param1', 'param2', 'param3']);
        $output = ob_get_clean();

        // Assert parameter output
        $this->assertIsString($output);
        $this->assertNotEmpty($output);

        $expectedParameterOutput = '';
        $expectedParameterOutput .= '["param1","param2","param3"]';
        $expectedParameterOutput .= '["param1","param2","param3"]';
        $expectedParameterOutput .= '["param1","param2","param3"]';

        $this->assertEquals($expectedParameterOutput, $output);

        // Assert filter values
        $expectedFilterValues = ['callback_1', 'callback_2', 'callback_3'];

        $this->assertIsArray($filterValues);
        $this->assertCount(3, $filterValues);

        $this->assertEquals($expectedFilterValues, $filterValues);
    }
}
