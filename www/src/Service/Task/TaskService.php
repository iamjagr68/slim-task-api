<?php

namespace App\Service\Task;

use App\Exception\ValidationException;
use App\Service\BaseService;
use App\Entity\Task\TaskEntity;

final class TaskService extends BaseService
{

    /**
     * Get an array of TaskEntities from the database
     * @return TaskEntity[] Array of TaskEntities fetched from the database
     */
    public function getAll(): array
    {
        $sql  = "SELECT * FROM `task` WHERE is_deleted = 0";
        $stmt = $this->db->query($sql);

        $results = [];
        while ($row = $stmt->fetch()) {
            $results[] = new TaskEntity($row);
        }

        return $results;
    }

    /**
     * Get a single task from the database
     * @param int $task_id Id of the task to be fetched
     * @return TaskEntity|null The task or null if the task can't be found
     */
    public function getOne(int $task_id): ?TaskEntity
    {
        $sql    = "SELECT * FROM `task` WHERE `id` = :task_id AND is_deleted = 0";
        $stmt   = $this->db->prepare($sql);
        $result = $stmt->execute(["task_id" => $task_id]);

        if ($result && $stmt->rowCount()) {
            return new TaskEntity($stmt->fetch());
        }

        return null;
    }

    /**
     * Save a task to the database
     * @param TaskEntity $task Task to save
     * @return TaskEntity|null Task after saving
     */
    public function insert(TaskEntity $task): ?TaskEntity
    {
        // Validate the task before attempting to save
        self::validate($task);

        $sql    = "
            INSERT INTO `task` (`task`,`is_done`)
            VALUES (:task, :is_done)";
        $stmt   = $this->db->prepare($sql);
        $result = $stmt->execute([
            'task'       => $task->getTask(),
            'is_done'    => $task->isDone(),
        ]);

        if (!$result) {
            return null;
        }

        return $this->getOne($this->db->lastInsertId());
    }

    /**
     * Update a task in the database
     * @param TaskEntity $task Task to update
     * @return TaskEntity|null Task after updating
     */
    public function update(TaskEntity $task): ?TaskEntity
    {
        // Validate the task before attempting to save
        self::validate($task);

        $sql  = "
            UPDATE task
            SET
                task = :task,
                is_done = :is_done,
                is_deleted = :is_deleted
            WHERE
                id = :id AND is_deleted = 0";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            'task'       => $task->getTask(),
            'is_done'    => $task->isDone(),
            'is_deleted' => $task->isDeleted(),
            'id'         => $task->getId()
        ]);

        if (!$result) {
            return null;
        }

        return $this->getOne($task->getId());
    }

    /**
     * Soft delete a task
     * @param int $task_id Id of the task to be deleted
     * @return bool True if successful false if failed
     */
    public function delete(int $task_id) {
        $sql    = "
            UPDATE task
            SET
                is_deleted = 1
            WHERE
                id = :task_id AND is_deleted = 0";
        $stmt   = $this->db->prepare($sql);
        $result = $stmt->execute(["task_id" => $task_id]);

        return $result && $stmt->rowCount();
    }

    /**
     * Validates the TaskEntity before persisting to the database
     * @param TaskEntity $task The task to validate
     * @return bool True if the task is valid
     * @throws ValidationException
     */
    protected static function validate(TaskEntity $task): bool
    {
        // Validate that the task was set
        if (empty(trim($task->getTask()))) {
            throw new ValidationException('Task is required');
        }

        // Validate that task is no longer than 255 characters
        if (strlen($task->getTask()) > 255) {
            throw new ValidationException(
                'Task can be no longer than 255 characters');
        }

        return true;
    }

}