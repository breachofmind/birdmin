<?php
use Birdmin\Support\ModelBlueprint;
use Birdmin\Support\FieldBlueprint as Field;
use Birdmin\Input;
use Birdmin\Product;

/**
 * ------------------------------------------
 * PRODUCT MODEL
 * ------------------------------------------
 */
$productOptions = [
    'slug' => ['reference' => 'name'],
    'category_id' => [
        'model' => \Birdmin\Category::class,
        'nullable' => true
    ],
    'bundle_id' => [
        'model' => \Birdmin\ProductBundle::class,
        'nullable' => true
    ],
    'status' => [
        'values' => Input::$statusFields
    ]
];

$products = ModelBlueprint::create(Product::class, 'products')

    ->_name         ("Name",        Field::TITLE,   Input::TEXT)
    ->_slug         ('Slug',        Field::SLUG,    Input::SLUG)
    ->_status       ("Status",      Field::STATUS,  Input::RADIO)
    ->_brand        ("Brand",       Field::TEXT,    Input::TEXT)
    ->_sku          ("SKU",         Field::STRING,  Input::TEXT)
    ->_excerpt      ("Excerpt",     Field::STRING,  Input::TEXTAREA)
    ->_description  ("Description", Field::TEXT,    Input::HTML)
    ->_category_id  ("Category",    [Field::REFERENCE, ['id','categories']],        Input::MODEL)
    ->_bundle_id    ("Bundle",      [Field::REFERENCE, ['id','product_bundles']],   Input::MODEL)
    ->_type         ("Type",        Field::STRING,  Input::TEXT)
    ->_attributes   ("Attributes",  Field::TEXT,    Input::HTML)

    ->inputOptions  ($productOptions)
    ->fillable      ('*')
    ->in_table      ('name','brand','type','sku','status')
    ->searchable    ('name','sku','brand','status')
    ->unique        ('slug','sku')
    ->required      ('name','slug','status')
    ->icon          ('bag')
    ->no_image      ('/public/images/no-image.svg')
    ->url('products/{slug}')

    ->module('Birdmin\Components\RelatedMedia')
    ->module('Birdmin\Components\RelatedModels', ['Birdmin\ProductVariation']);

$products->indexTable()
    ->bulk(true)
    ->columns([
        'url'           => ["URL",      10, 'Birdmin\Formatters\url'],
        'preview'       => ["Preview",  -1,  'Birdmin\Formatters\preview']
    ]);