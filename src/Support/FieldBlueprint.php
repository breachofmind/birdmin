<?php
namespace Birdmin\Support;


class FieldBlueprint {

    protected $name;

    protected $fillable = false;
    protected $guarded = false;

    public function __construct($name)
    {
        $this->name = $name;
    }

}