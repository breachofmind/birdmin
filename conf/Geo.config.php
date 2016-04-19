<?php
use Birdmin\Support\ModelBlueprint;
use Birdmin\Support\FieldBlueprint as Field;
use Birdmin\Input;
use Birdmin\Location;

/**
 * ------------------------------------------
 * LOCATION MODEL
 * ------------------------------------------
 */
$locations = ModelBlueprint::create(Location::class, "locations")

    ->_name         ("Name",            Field::TITLE,   Input::TEXT)
    ->_address      ("Address",         Field::STRING,  Input::TEXT)
    ->_address_2    ("Address Line 2",  Field::STRING,  Input::TEXT)
    ->_city         ("City",            Field::STRING,  Input::TEXT)
    ->_state        ("State",           Field::STRING,  Input::SELECT, ['values' => 'states'])
    ->_zip          ("Postal Code",     Field::STRING,  Input::TEXT)
    ->_county       ("County",          Field::STRING,  Input::TEXT)
    ->_country      ("Country",         Field::STRING,  Input::SELECT, ['values' => 'countries'])
    ->_lat          ("Latitude",        [Field::FLOAT, 6], Input::NUMBER)
    ->_lng          ("Longitude",       [Field::FLOAT, 6], Input::NUMBER)
    ->_description  ("Description",     Field::TEXT,    Input::HTML)
    ->_directions   ("Directions",      Field::TEXT,    Input::HTML)

    ->fillable  ('*')
    ->in_table  (['name','address','city','state'])
    ->required  (['name'])

    ->icon          ('map-marker');