<?php
use Birdmin\Support\ModelBlueprint;
use Birdmin\Support\FieldBlueprint as Field;
use Birdmin\Input;
use Birdmin\Page;
use Birdmin\Category;
use Birdmin\User;
use Birdmin\Post;

/**
 * ------------------------------------------
 * PAGE MODEL
 * ------------------------------------------
 */

$pages = ModelBlueprint::create(Page::class, 'pages')

    ->_title        ("Title",       Field::TITLE,   Input::TEXT)
    ->_slug         ('Slug',        Field::SLUG,    Input::SLUG, ['references' => 'title'])
    ->_content      ("Content",     Field::TEXT,    Input::HTML)
    ->_status       ("Status",      Field::STATUS,  Input::RADIO, ['values'=>Input::$statusFields])
    ->_type         ("Type",        Field::STRING,  Input::RADIO)
    ->_parent_id    ("Parent Page", [Field::REFERENCE, ['pages','id']], Input::MODEL)
    ->_user_id      ("Author",      [Field::REFERENCE, ['users','id']], Input::MODEL)

    ->fillable      ('*')
    ->in_table      (['title','status','slug'])
    ->searchable    (['title','status','type'])
    ->unique        (['slug'])
    ->required      (['slug','status'])

    ->icon          ('file-empty')

    ->setOptions ([
        'type' => [
            'values'=>[
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
        ],
    ]);



/**
 * ------------------------------------------
 * CATEGORY MODEL
 * ------------------------------------------
 */

$categories = ModelBlueprint::create(Category::class, 'categories')

    ->_name         ('Name',            Field::TITLE,      Input::TEXT)
    ->_slug         ('Slug',            Field::SLUG,       Input::SLUG, ['references' => 'name'])
    ->_description  ('Description',     Field::TEXT,       Input::HTML)
    ->_status       ('Status',          Field::STATUS,     Input::RADIO, ['values' => Input::$statusFields])
    ->_excerpt      ('Excerpt',         Field::STRING,     Input::TEXTAREA)
    ->_object       ('Object Type',     Field::STRING,     Input::TEXT)
    ->_parent_id    ('Parent Category', [Field::REFERENCE, ['categories','id']], Input::MODEL)

    ->fillable      ('*')
    ->in_table      (['name','status','object','parent_id'])
    ->searchable    (['name','status','object'])
    ->unique        (['slug'])
    ->required      (['name','slug'])

    ->icon          ('tag')
    ->url           ('category/{slug}')

    ->setOptions([
        'parent_id' => [
            'model' => Category::class,
            'nullable'=> true
        ],
    ]);


/**
 * ------------------------------------------
 * POST MODEL
 * ------------------------------------------
 */
$posts = ModelBlueprint::create(Post::class, "posts")

    ->_title        ('Title',           Field::TITLE,   Input::TEXT)
    ->_slug         ('Slug',            Field::SLUG,    Input::SLUG, ['references' => 'title'])
    ->_user_id      ('Author',          [Field::REFERENCE, ['users','id']], Input::MODEL)
    ->_excerpt      ('Excerpt',         Field::TEXT,    Input::TEXTAREA)
    ->_content      ('Content',         Field::TEXT,    Input::HTML)
    ->_published_at ('Publish Date',    Field::DATE,    Input::DATE)
    ->_status       ('Status',          Field::STATUS,  Input::RADIO, ['values' => Input::$statusFields])
    ->_type         ('Post Type',       Field::STRING,  Input::SELECT)
    ->_location_id  ('Location',        [Field::REFERENCE, ['locations','id']])

    ->in_table  (['title','user_id','published_at','status'])
    ->unique    (['slug'])
    ->required  (['title','slug','user_id'])
    ->dates     (['published_at'])

    ->icon ('files')

    ->setOptions([
        'type' => [
            'values' => [
                'post' => 'Post',
                'revision' => 'Revision'
            ]
        ],
        'user_id' => [
            'model' => 'Birdmin\User',
            'nullable' => false
        ]
    ]);