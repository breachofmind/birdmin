<?php

namespace Birdmin\Events;

use Illuminate\Queue\SerializesModels;
use Birdmin\Core\Model;

class ModelConstruct
{
    use SerializesModels;

    /**
     * The model instance.
     * @var Model
     */
    protected $model;

    /**
     * Create a new event instance.
     * @param $model Model
     * @return void
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function getModel()
    {
        return $this->model;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
