<?php
use Birdmin\Support\ModelBlueprint;
use Birdmin\Support\FieldBlueprint;
use Birdmin\Input;
use Birdmin\Page;



$status = [
    'publish' => 'Publish',
    'draft' => 'Draft'
];

$blueprint = ModelBlueprint::create(Page::class)
    ->table('pages')
    ->labels([
        'singular'   => 'page',
        'plural'     => 'pages',
        'navigation' => 'Pages'
    ])

    ->field('title',    FieldBlueprint::STRING, 50)
    ->field('content',  FieldBlueprint::TEXT)
    ->field('status',   FieldBlueprint::STATUS)
    ->field('parent_id',FieldBlueprint::REFERENCE, ['pages','id'])
    ->title('title')

    ->fillable('title','content','parent_id')
    ->guarded('status')
    ->timestamps(true)

    ->permissions('create','edit','delete','view')

    ->in_table  ('title','status','slug')
    ->unique    ('slug')
    ->required  ('slug','status')

    ->input('title',    Input::TEXT,    'The title of the Page')
    ->input('content',  Input::HTML,    'The content of the page')
    ->input('status',   Input::RADIO,   'The publish status of the page', ['values'=>$status]);