<?php

namespace App\Entity\Task;

use App\Entity\BaseEntity;

class TaskEntity extends BaseEntity
{
    protected $id;
    protected $task;
    protected $is_done;
    protected $is_deleted;
    protected $created;
    protected $modified;

    /**
     * TaskEntity constructor.
     * Accepts an assoc array of data matching properties of this class
     * and create the class
     *
     * @param array $data The data to use to create
     */
    public function __construct(array $data)
    {
        if (isset($data['id'])) {
            $this->id       = (int)$data['id'];
            $this->created  = $data['created'];
            $this->modified = $data['modified'];
        }

        $this->task       = $data['task'];
        $this->is_done    = (int)$data['is_done'] ?: 0;
        $this->is_deleted = (int)$data['is_deleted'] ?: 0;
    }

    /**
     * Id getter
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Task getter
     * @return string|null
     */
    public function getTask(): ?string
    {
        return $this->task;
    }

    /**
     * Is done getter
     * @return bool
     */
    public function isDone(): int
    {
        return $this->is_done;
    }

    /**
     * Is deleted getter
     * @return bool
     */
    public function isDeleted(): int
    {
        return $this->is_deleted;
    }

    /**
     * Created getter
     * @return string
     */
    public function getCreated(): string
    {
        return $this->created;
    }

    /**
     * Modified getter
     * @return string
     */
    public function getModified(): string
    {
        return $this->modified;
    }

    /**
     * Determines if the entity is valid for persisting to the database
     * @throws \Exception
     */
    public function validate(): void
    {
        if (empty($this->task)) {
            throw new \Exception("Task is a required field");
        }
        if (strlen($this->task) > 255) {
            throw new \Exception("Task can be no longer than 255 characters");
        }
    }

    /**
     * Custom json_encode output format
     * @return array Json representation of the entity
     */
    public function jsonSerialize(): array
    {
        return [
            'id'         => $this->id,
            'task'       => $this->task,
            'is_done'    => (bool) $this->is_done,
            'is_deleted' => (bool) $this->is_deleted,
            'created'    => $this->created,
            'modified'   => $this->modified
        ];
    }
}
