<?php

namespace Birdmin;

use Birdmin\Core\Model;

class Location extends Model
{
    protected $table = "locations";

    protected $fillable = [
        'name',
        'address',
        'address_2',
        'city',
        'state',
        'zip',
        'county',
        'country',
        'lat',
        'lng',
        'description',
        'directions'
    ];

    protected $searchable = ['name','address','city','state','zip','county'];


    protected $dates = ['created_at','updated_at'];

}
