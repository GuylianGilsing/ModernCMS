<?php

declare(strict_types=1);

namespace Tests\ModernCMS\Core\Classes\Hooks;

use ErrorException;
use ModernCMS\Core\Classes\Hooks\ActionRegistry;
use PHPUnit\Framework\TestCase;

class ActionRegistryTest extends TestCase
{
    public function testIfCanRegisterAction(): void
    {
        // Arrange
        $registry = new ActionRegistry();

        $actionNameToAdd = 'test_action_1';

        // Act
        $registry->register($actionNameToAdd);

        // Assert
        $internalData = $registry->getInternalData();

        $this->assertArrayHasKey($actionNameToAdd, $internalData);
    }

    public function testIfCannotRegisterActionTwice(): void
    {
        // Arrange
        $registry = new ActionRegistry();

        $actionNameToAdd = 'test_action_1';

        // Assert
        $this->expectException(ErrorException::class);

        // Act
        $registry->register($actionNameToAdd);
        $registry->register($actionNameToAdd);

        // Assert
        $internalData = $registry->getInternalData();

        $this->assertArrayHasKey($actionNameToAdd, $internalData);
    }

    public function testIfHasMethodReturnsTrueWhenNameExists(): void
    {
        // Arrange
        $internalData = [];
        $registry = new ActionRegistry();

        $actionNameToAdd = 'test_action_1';

        // Act
        $registry->register($actionNameToAdd);
        $actionExists = $registry->has($actionNameToAdd);

        // Assert
        $internalData = $registry->getInternalData();

        $this->assertTrue($actionExists);
        $this->assertArrayHasKey($actionNameToAdd, $internalData);
    }

    public function testIfHasMethodReturnsFalseWhenNameDoesntExist(): void
    {
        // Arrange
        $registry = new ActionRegistry();

        // Act
        $actionExists = $registry->has('test_action_1');

        // Assert
        $internalData = $registry->getInternalData();

        $this->assertFalse($actionExists);
        $this->assertEmpty($internalData);
    }

    public function testIfCanRegisterCallbackOnAction(): void
    {
        // Arrange
        $registry = new ActionRegistry();

        $actionNameToAdd = 'test_action_1';

        // Act
        $registry->register($actionNameToAdd);
        $registry->addCallbackToAction($actionNameToAdd, fn() => 'callback 1');

        // Assert
        $internalData = $registry->getInternalData();

        $this->assertArrayHasKey($actionNameToAdd, $internalData);

        $this->assertIsArray($internalData[$actionNameToAdd]);
        $this->assertNotEmpty($internalData[$actionNameToAdd]);
        $this->assertCount(1, $internalData[$actionNameToAdd]);
    }

    public function testIfCanRegisterMultipleCallbacksOnAction(): void
    {
        // Arrange
        $registry = new ActionRegistry();

        $actionNameToAdd = 'test_action_1';

        // Act
        $registry->register($actionNameToAdd);

        $registry->addCallbackToAction($actionNameToAdd, fn() => 'callback 1');
        $registry->addCallbackToAction($actionNameToAdd, fn() => 'callback 2');
        $registry->addCallbackToAction($actionNameToAdd, fn() => 'callback 3');

        // Assert
        $internalData = $registry->getInternalData();

        $this->assertArrayHasKey($actionNameToAdd, $internalData);

        $this->assertIsArray($internalData[$actionNameToAdd]);
        $this->assertNotEmpty($internalData[$actionNameToAdd]);
        $this->assertCount(3, $internalData[$actionNameToAdd]);
    }

    public function testIfCannotRegisterCallbackOnActionThatDoesntExist(): void
    {
        // Arrange
        $registry = new ActionRegistry();

        // Assert
        $this->expectException(ErrorException::class);

        // Act
        $registry->addCallbackToAction('test_action_1', fn() => 'callback 1');

        // Assert
        $internalData = $registry->getInternalData();

        $this->assertEmpty($internalData);
    }

    public function testIfCanDispatchRegisteredCallbacksOnAction(): void
    {
        // Arrange
        $registry = new ActionRegistry();

        $actionNameToAdd = 'test_action_1';
        $registry->register($actionNameToAdd);

        $registry->addCallbackToAction($actionNameToAdd, function(){echo 'callback 1';});
        $registry->addCallbackToAction($actionNameToAdd, function(){echo 'callback 2';});
        $registry->addCallbackToAction($actionNameToAdd, function(){echo 'callback 3';});

        // Act
        ob_start();
        $registry->dispatch($actionNameToAdd);
        $output = ob_get_clean();

        // Assert
        $this->assertIsString($output);
        $this->assertNotEmpty($output);
        $this->assertEquals('callback 1callback 2callback 3', $output);

        $internalData = $registry->getInternalData();

        $this->assertArrayHasKey($actionNameToAdd, $internalData);

        $this->assertIsArray($internalData[$actionNameToAdd]);
        $this->assertNotEmpty($internalData[$actionNameToAdd]);
        $this->assertCount(3, $internalData[$actionNameToAdd]);
    }

    public function testIfDispatchedActionContainsSpecifiedParameters(): void
    {
        // Arrange
        $registry = new ActionRegistry();

        $actionNameToAdd = 'test_action_1';
        $registry->register($actionNameToAdd);

        $parameters = ['param1', 2, ['param3']];
        $registry->addCallbackToAction($actionNameToAdd, function(string $param1, int $param2, array $param3)
        {
            echo json_encode([
                'param1' => $param1,
                'param2' => $param2,
                'param3' => $param3,
            ]);
        });

        // Act
        ob_start();
        $registry->dispatch($actionNameToAdd, $parameters);
        $output = ob_get_clean();

        // Assert
        $expectedMessage = json_encode([
            'param1' => $parameters[0],
            'param2' => $parameters[1],
            'param3' => $parameters[2]
        ]);

        $this->assertIsString($output);
        $this->assertNotEmpty($output);
        $this->assertEquals($expectedMessage, $output);
    }
}
