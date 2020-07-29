<?php

namespace Tests\Integration;

class TaskTest extends BaseTestCase
{
    private static $task;
    private const TASK_KEYS = [
        'id',
        'task',
        'is_done',
        'is_deleted',
        'modified',
        'created'
    ];

    /**
     * Test ability to get all tasks
     * GET /v1/tasks
     */
    public function testGetTasks(): void
    {
        $response = $this->runApp('GET', '/v1/tasks');
        $result   = json_decode((string) $response->getBody());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        $this->assertTrue(is_array($result));
        $this->assertTrue(count($result) > 0);
        $this->assertTrue(self::isTaskValid($result[0]));
    }

    /**
     * Test ability to get one task
     * GET /v1/tasks/{id}
     */
    public function testGetTask(): void
    {
        $response = $this->runApp('GET', '/v1/tasks/1');
        $result   = json_decode((string) $response->getBody());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        $this->assertTrue($result instanceof \stdClass);
        $this->assertTrue(self::isTaskValid($result));
        $this->assertEquals(1, $result->id);
    }

    /**
     * Test attempting to fetch non-existent task
     * GET /v1/tasks/{id}
     */
    public function testTaskNotFound(): void
    {
        $response = $this->runApp('GET', '/v1/tasks/xxx');
        $result   = json_decode((string) $response->getBody());

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('application/problem+json', $response->getHeaderLine('Content-Type'));
        $this->assertTrue(property_exists($result, 'message'));
    }

    /**
     * Test the creation of a new task without sending a task
     * POST /v1/tasks
     */
    public function testCreateTaskWithOutTask(): void
    {
        $response = $this->runApp('POST', '/v1/tasks', ['task' => '']);
        $result   = json_decode((string) $response->getBody());

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('application/problem+json', $response->getHeaderLine('Content-Type'));
        $this->assertTrue(property_exists($result, 'message'));
    }

    /**
     * Test the creation of a new task with task that is too long
     * POST /v1/tasks
     */
    public function testCreateTaskWithBadTask(): void
    {
        $response = $this->runApp('POST', '/v1/tasks', ['task' => str_repeat('a', 300)]);
        $result   = json_decode((string) $response->getBody());

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('application/problem+json', $response->getHeaderLine('Content-Type'));
        $this->assertTrue(property_exists($result, 'message'));
    }

    /**
     * Test the creation of a new task
     * POST /v1/tasks
     */
    public function testCreateTask(): void
    {
        $response   = $this->runApp('POST', '/v1/tasks', ['task' => 'New Task']);
        $result     = json_decode((string) $response->getBody());
        self::$task = $result;

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        $this->assertTrue($result instanceof \stdClass);
        $this->assertTrue(self::isTaskValid($result));
        $this->assertEquals('New Task', $result->task);
    }

    /**
     * Test if we can retrieve the task we just created
     * GET /v1/tasks/{id}
     */
    public function testGetCreatedTask(): void
    {
        $response = $this->runApp('GET', '/v1/tasks/'.self::$task->id);
        $result   = json_decode((string) $response->getBody());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        $this->assertTrue($result instanceof \stdClass);
        $this->assertTrue(self::isTaskValid($result));
        $this->assertEquals(self::$task->id, $result->id);
        $this->assertEquals(self::$task->task, $result->task);
    }

    /**
     * Test if we can update an existing task
     * PUT /v1/tasks/{id}
     */
    public function testUpdateTask(): void
    {
        $newTaskName = 'My Updated Task';
        $this->assertNotEquals($newTaskName, self::$task->task);
        $task = self::$task;
        $task->task = $newTaskName;

        $response = $this->runApp('PUT', '/v1/tasks/'.self::$task->id, (array) $task);
        $result   = json_decode((string) $response->getBody());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        $this->assertTrue($result instanceof \stdClass);
        $this->assertTrue(self::isTaskValid($result));
        $this->assertEquals(self::$task->id, $result->id);
        $this->assertEquals($newTaskName, $result->task);
    }

    /**
     * Test if we get not found when attempting to update non-existent task
     * PUT /v1/tasks/{id}
     */
    public function testUpdateNotFound(): void
    {
        $task       = clone self::$task;
        $task->id   = 999999999999;

        $response = $this->runApp('PUT', '/v1/tasks/'.$task->id, (array) $task);
        $result   = json_decode((string) $response->getBody());

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('application/problem+json', $response->getHeaderLine('Content-Type'));
        $this->assertTrue(property_exists($result, 'message'));
    }

    /**
     * Test if we can update an existing task with bad data
     * PUT /v1/tasks/{id}
     */
    public function testUpdateTaskWithBadTask(): void
    {
        $task = self::$task;
        $task->task = '';

        $response = $this->runApp('PUT', '/v1/tasks/'.self::$task->id, (array) $task);
        $result   = json_decode((string) $response->getBody());

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('application/problem+json', $response->getHeaderLine('Content-Type'));
        $this->assertTrue(property_exists($result, 'message'));
    }

    /**
     * Test if we can delete a task
     * DELETE /v1/tasks/{id}
     */
    public function testDeleteTask(): void
    {
        $response = $this->runApp('DELETE', '/v1/tasks/'.self::$task->id);
        $this->assertEquals(204, $response->getStatusCode());
    }

    /**
     * Test trying to delete a non-existent task
     * GET /v1/tasks/{id}
     */
    public function testGetDeleteNotFound(): void
    {
        $response = $this->runApp('GET', '/v1/tasks/xxx');
        $result   = json_decode((string) $response->getBody());

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('application/problem+json', $response->getHeaderLine('Content-Type'));
        $this->assertTrue(property_exists($result, 'message'));
    }

    /**
     * Test trying to get a deleted task
     * GET /v1/tasks/{id}
     */
    public function testGetDeletedTask(): void
    {
        $response = $this->runApp('GET', '/v1/tasks/'.self::$task->id);
        $result   = json_decode((string) $response->getBody());

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('application/problem+json', $response->getHeaderLine('Content-Type'));
        $this->assertTrue(property_exists($result, 'message'));
    }

    private static function isTaskValid(\stdClass $task): bool
    {
        return (
            count(self::TASK_KEYS) === count(get_object_vars($task)) &&
            count(array_diff(self::TASK_KEYS, array_keys(get_object_vars($task)))) === 0
        );
    }
}