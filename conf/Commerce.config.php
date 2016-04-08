<?php
use Birdmin\Support\ModelBlueprint;
use Birdmin\Support\FieldBlueprint as Field;
use Birdmin\Input;
use Birdmin\Product;
use Birdmin\ProductVariation;
use Birdmin\ProductBundle;

/**
 * ------------------------------------------
 * PRODUCT MODEL
 * ------------------------------------------
 */
$products = ModelBluePrint::create(Product::class, 'products')

    ->_name         ("Name",        Field::TITLE,   Input::TEXT)
    ->_slug         ('Slug',        Field::SLUG,    Input::SLUG)
    ->_status       ("Status",      Field::STATUS,  Input::RADIO)
    ->_brand        ("Brand",       Field::TEXT,    Input::TEXT)
    ->_sku          ("SKU",         Field::STRING,  Input::TEXT)
    ->_excerpt      ("Excerpt",     Field::STRING,  Input::TEXTAREA)
    ->_description  ("Description", Field::TEXT,    Input::HTML)
    ->_category_id  ("Category",    [Field::REFERENCE, ['id','categories']],        Input::MODEL)
    ->_bundle_id    ("Product Bundle", [Field::REFERENCE, ['id','product_bundles']],   Input::MODEL)
    ->_type         ("Type",        Field::STRING,  Input::TEXT)
    ->_attributes   ("Attributes",  Field::TEXT,    Input::HTML)
    ->useTimestamps ()
    ->useSoftDeletes()

    ->fillable      ('*')
    ->in_table      (['name','brand','type','sku','status'])
    ->searchable    (['name','sku','brand','status'])
    ->unique        (['slug','sku'])
    ->required      (['name','slug','status'])

    ->public        (true)
    ->icon          ('bag')
    ->no_image      ('/public/images/no-image.svg')
    ->url           ('products/{slug}')

    ->module('Birdmin\Components\RelatedMedia')
    ->module('Birdmin\Components\RelatedModels', ['Birdmin\ProductVariation'])

    ->setOptions ([
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
    ]);


/**
 * ------------------------------------------
 * VARIATION MODEL
 * ------------------------------------------
 */
$variations = ModelBluePrint::create(ProductVariation::class, 'product_variations')

    ->_name         ("Name",            Field::TITLE,   Input::TEXT)
    ->_product_id   ("Parent Product", [Field::REFERENCE, ['id','products']],   Input::MODEL)
    ->_status       ("Status",          Field::STATUS,  Input::RADIO)
    ->_sku          ("SKU",             Field::STRING,  Input::TEXT)
    ->_description  ("Description",     Field::TEXT,    Input::HTML)
    ->_attributes   ("Attributes",      Field::TEXT,    Input::HTML)
    ->_color        ("Color",           Field::STRING,  Input::COLOR)
    ->_color_name   ("Color Name",      Field::STRING,  Input::TEXT)
    ->useTimestamps ()
    ->useSoftDeletes()

    ->fillable      ('*')
    ->in_table      (['name','color','product_id','sku','status'])
    ->searchable    (['name','status','sku'])
    ->unique        (['sku'])
    ->required      (['name','status','product_id','sku'])

    ->icon          ('bag')
    ->no_image      ('/public/images/no-image.svg')
    ->url           ('variation/{slug}')

    ->module('Birdmin\Components\RelatedMedia')

    ->setOptions ([
        'product_id' => [
            'model' => \Birdmin\Product::class,
            'nullable' => false
        ],
        'status' => [
            'values' => Input::$statusFields
        ]
    ]);


/**
 * ------------------------------------------
 * PRODUCT BUNDLE MODEL
 * ------------------------------------------
 */
$bundles = ModelBluePrint::create(ProductVariation::class, 'product_bundles')

    ->_name         ("Name",        Field::TITLE,   Input::TEXT)
    ->_slug         ('Slug',        Field::SLUG,    Input::SLUG)
    ->_brand        ('Brand',       Field::STRING,  Input::TEXT)
    ->_website      ("Website",     Field::STRING,  Input::URL)
    ->_excerpt      ("Excerpt",     Field::TEXT,    Input::HTML)
    ->_attributes   ("Attributes",  Field::TEXT,    Input::HTML)
    ->_description  ("Description", Field::TEXT,    Input::HTML)
    ->_status       ("Status",      Field::STATUS,  Input::RADIO)
    ->useTimestamps ()
    ->useSoftDeletes()

    ->fillable      ('*')
    ->in_table      (['name','brand','excerpt','website'])
    ->searchable    (['name','brand'])
    ->unique        (['slug'])
    ->required      (['name','brand','slug'])

    ->icon          ('bag')
    ->no_image      ('/public/images/no-image.svg')
    ->url           ('bundle/{slug}')

    ->module('Birdmin\Components\RelatedMedia')

    ->setOptions ([
        'status' => [
            'values' => Input::$statusFields
        ]
    ]);