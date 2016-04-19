<?php
use Birdmin\Support\ModelBlueprint;
use Birdmin\Support\FieldBlueprint as Field;
use Birdmin\Input;
use Birdmin\User;
use Birdmin\Role;
use Birdmin\Media;

/**
 * ------------------------------------------
 * USER MODEL
 * ------------------------------------------
 */
$users = ModelBlueprint::create(User::class, 'users')

    ->_email        ('Email Address',   Field::TITLE,   Input::EMAIL)
    ->_first_name   ('First Name',      Field::STRING,  Input::TEXT)
    ->_last_name    ('Last Name',       Field::STRING,  Input::TEXT)
    ->_phone        ('Phone',           Field::STRING,  Input::TEXT)
    ->_password     ('Password',        Field::STRING,  Input::PASSWORD)
    ->_website      ('Website',         Field::STRING,  Input::URL)
    ->_affiliation  ('Affiliation',     Field::STRING,  Input::TEXT)
    ->_position     ('Position',        Field::STRING,  Input::TEXT)
    ->_bio          ('Personal Info',   Field::TEXT,    Input::HTML)

    ->in_table      (['email','first_name','last_name','phone'])
    ->unique        (['email'])
    ->required      (['email','first_name','last_name'])
    ->hidden        (['password'])

    ->permissions   (['view','manage','create','edit','delete'])
    ->no_image      ('/cms/public/images/no-user.svg')
    ->icon          ('users2')
    ->public        (false);

/**
 * ------------------------------------------
 * ROLE MODEL
 * ------------------------------------------
 */
$roles = ModelBlueprint::create(Role::class, "roles")

    ->_name         ('Name',        Field::TITLE, Input::TEXT)
    ->_description  ('Description', Field::TEXT,  Input::TEXTAREA)

    ->in_table  (['name','description'])
    ->unique    (['name'])
    ->required  (['name'])

    ->icon          ('user-lock')
    ->public        (false);



/**
 * ------------------------------------------
 * MEDIA MODEL
 * ------------------------------------------
 */
$media = ModelBlueprint::create(Media::class, "media")

    ->_title        ('Title',       Field::TITLE,     Input::TEXT)
    ->_alt_text     ('Alt Text',    Field::STRING,    Input::TEXT)
    ->_file_name    ('File Name',   Field::STRING,    Input::NONE)
    ->_file_type    ('File Type',   Field::STRING,    Input::NONE)
    ->_file_size    ('File Size',   Field::INTEGER,   Input::NONE)
    ->_category     ('Category',    Field::STRING,    Input::TEXT)
    ->_caption      ('Caption',     Field::TEXT,      Input::TEXTAREA)
    ->_metadata     ('Metadata',    Field::TEXT,      Input::HASH)
    ->_etag         ('ETag',        Field::TEXT)

    ->in_table  (['file_name','file_type','file_size','title'])
    ->required  (['file_name','file_type','title'])

    ->icon          ('picture')
    ->public        (false);

/**
 * ------------------------------------------
 * INPUT MODEL
 * ------------------------------------------
 */
$inputs = ModelBlueprint::create(Input::class, "inputs")

    ->_label        ("Label",       Field::TITLE,   Input::TEXT)
    ->_object       ("Object",      Field::STRING,  Input::TEXT)
    ->_name         ("Name",        Field::STRING,  Input::TEXT)
    ->_type         ("Type",        Field::STRING,  Input::TEXT)
    ->_options      ("Options",     Field::TEXT,    Input::CODE)
    ->_description  ("Description", Field::TEXT,    Input::TEXTAREA)
    ->_in_table     ("In Table",    Field::INTEGER, Input::TOGGLE)
    ->_required     ("Required",    Field::INTEGER, Input::TOGGLE)
    ->_unique       ("Unique",      Field::INTEGER, Input::TOGGLE)
    ->_priority     ("Priority",    Field::INTEGER, Input::NUMBER)

    ->in_table  (['label','object','name','type','priority'])
    ->required  (['label','object','name','type'])

    ->icon          ('select2')
    ->public        (false)

    ->labels([
        'navigation' => "Input Register"
    ]);