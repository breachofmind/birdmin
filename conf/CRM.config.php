<?php
use Birdmin\Support\ModelBlueprint;
use Birdmin\Support\FieldBlueprint as Field;
use Birdmin\Input;
use Birdmin\Lead;

/**
 * ------------------------------------------
 * LEAD MODEL
 * ------------------------------------------
 */
$leads = ModelBlueprint::create(Lead::class, 'leads')

    ->_email        ('Email Address',   Field::TITLE,   Input::EMAIL)
    ->_first_name   ('First Name',      Field::STRING,  Input::TEXT)
    ->_last_name    ('Last Name',       Field::STRING,  Input::TEXT)
    ->_affiliation  ('Affiliation',     Field::STRING,  Input::TEXT)
    ->_phone        ('Phone',           Field::STRING,  Input::TEXT)
    ->_source       ('Source',          Field::STRING,  Input::TEXT)
    ->_interest     ('Interest',        Field::STRING,  Input::TEXT)
    ->_type         ('Type',            Field::STRING,  Input::TEXT)
    ->_comments     ('Comments',        Field::TEXT,    Input::TEXTAREA)
    ->_notes        ('Notes',           Field::TEXT,    Input::TEXTAREA)
    ->_session_id   ('Session ID',      Field::STRING,  Input::NONE)
    ->_valid        ('Valid',           Field::INTEGER, Input::NONE)
    ->useTimestamps ()

    ->fillable      ('*')
    ->in_table      (['email','first_name','last_name','affiliation','source'])
    ->searchable    (['email','affiliation','source'])
    ->required      (['email'])

    ->icon          ('users2')
    ->no_image      ('/cms/public/images/no-user.svg')
    ->url           ('leads/{uid}');