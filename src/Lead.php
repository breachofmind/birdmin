<?php

namespace Birdmin;

use Birdmin\Core\Model;

class Lead extends Model
{
    protected $table = 'leads';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'affiliation',
        'comments',
        'interest',
        'source',
        'session_id'
    ];

    protected $searchable = ['first_name','last_name','email','affiliation','interest','source'];

    protected $dates = ['updated_at','created_at'];
}
