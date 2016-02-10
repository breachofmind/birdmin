<?php
use Birdmin\Support\ModelBlueprint;
use Birdmin\Support\FieldBlueprint;
use Birdmin\Input;
use Birdmin\Page;

$pageTypes = [
    'normal'  => 'Normal',
    'landing' => 'Landing Page'
];

ModelBlueprint::create(Page::class)
    ->table('pages')
    ->icon('file-empty')
    ->fields([
        'title'     => FieldBlueprint::TITLE,
        'content'   => FieldBlueprint::TEXT,
        'status'    => FieldBlueprint::STATUS,
        'type'      => FieldBlueprint::STRING,
        'parent_id' => [FieldBlueprint::REFERENCE, ['pages','id']],
    ])
    ->fillable      ('*')
    ->timestamps    (true)
    ->softDeletes   (true)
    ->in_table      ('title','status','slug')
    ->unique        ('slug')
    ->required      ('slug','status')

    ->inputs([
        'title'     => [Input::TEXT,    'The page title'],
        'slug'      => [Input::SLUG,    'A unique URL slug for this page', ['reference'=>'title']],
        'content'   => [Input::HTML,    'The page body or content'],
        'status'    => [Input::RADIO,   'The publish status of the page', ['values'=>Input::$statusFields]],
        'type'      => [Input::RADIO,   'The special type or grouping of this page', ['values'=>$pageTypes]],
        'parent_id' => [Input::MODEL,   'The parent page to whom this page is related', ['model'=> Page::class, 'nullable'=>true]],
    ])
    ->indexTable()
        ->columns([
            'url'           => ["URL",      10, 'Birdmin\Formatters\url'],
            'updated_at'    => ["Modified", 2,  'Birdmin\Formatters\date']
        ]);