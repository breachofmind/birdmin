<?php
namespace Birdmin\Support;


class ModelBlueprint {

    protected $class;

    protected $fillable = [];
    protected $guarded = [];
    protected $permissions = ['view','create','edit','delete'];
    protected $title = "id";
    protected $in_table = [];
    protected $required = [];
    protected $unique = [];
    protected $components = [];
    protected $labels = [
        'navigation' => 'Model',
        'singular' => 'model',
        'plural' => 'models'
    ];

}