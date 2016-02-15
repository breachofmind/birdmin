<?php
use Birdmin\Support\ModelBlueprint;
use Birdmin\Support\FieldBlueprint as Field;
use Birdmin\Input;
use Birdmin\Page;
use Birdmin\Category;
use Birdmin\User;

/**
 * ------------------------------------------
 * PAGE MODEL
 * ------------------------------------------
 */
$pageOptions = [
    'status' => ['values'=>Input::$statusFields],
    'type' => ['values'=>[
        'normal'  => 'Normal',
        'landing' => 'Landing Page'
        ]
    ],
    'parent_id' => [
        'model' => Page::class,
        'nullable' => true
    ],
    'user_id' => [
        'model' => User::class,
        'nullable' => false
    ]
];

$pages = ModelBlueprint::create(Page::class, 'pages')

    ->_title        ("Title",       Field::TITLE,   Input::TEXT)
    ->_slug         ('Slug',        Field::SLUG,    Input::SLUG)
    ->_content      ("Content",     Field::TEXT,    Input::HTML)
    ->_status       ("Status",      Field::STATUS,  Input::RADIO)
    ->_type         ("Type",        Field::STRING,  Input::RADIO)
    ->_parent_id    ("Parent Page", [Field::REFERENCE, ['pages','id']], Input::MODEL)
    ->_user_id      ("Author",      [Field::REFERENCE, ['users','id']], Input::MODEL)

    ->inputOptions  ($pageOptions)
    ->fillable      ('*')
    ->in_table      ('title','status','slug')
    ->searchable    ('title','status','type')
    ->unique        ('slug')
    ->required      ('slug','status')
    ->icon          ('file-empty');

$pages->indexTable()
    ->columns([
        'url'           => ["URL",      10, 'Birdmin\Formatters\url'],
        'updated_at'    => ["Modified", 2,  'Birdmin\Formatters\date']
    ]);



/**
 * ------------------------------------------
 * CATEGORY MODEL
 * ------------------------------------------
 */
$categoryOptions = [
    'status' => $pageOptions['status'],
    'parent_id' => [
        'model' => Category::class,
        'nullable'=> true
    ]
];

$category = ModelBlueprint::create(Category::class, 'categories')

    ->_name         ('Name',            Field::TITLE,      Input::TEXT)
    ->_slug         ('Slug',            Field::SLUG,       Input::SLUG)
    ->_description  ('Description',     Field::TEXT,       Input::HTML)
    ->_status       ('Status',          Field::STATUS,     Input::RADIO)
    ->_excerpt      ('Excerpt',         Field::STRING,     Input::TEXTAREA)
    ->_object       ('Object Type',     Field::STRING,     Input::TEXT)
    ->_parent_id    ('Parent Category', [Field::REFERENCE, ['categories','id']], Input::MODEL)

    ->inputOptions($categoryOptions)
    ->fillable('*')
    ->in_table('name','status','object','parent_id')
    ->searchable('name','status','object')
    ->unique('slug')
    ->required('name','slug')
    ->icon('tag')
    ->url(null);

$category->indexTable()
    ->columns([
        'url' => ["URL",      10, 'Birdmin\Formatters\url'],
    ]);