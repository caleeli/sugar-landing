<?php

namespace App\FRest;

/**
 * Description of Usuarios
 *
 * @author davidcallizaya
 */
class Sync extends FRest
{
    protected $path = '/getdataall';
    protected $method = 'GET';
    protected $sample = [
        [
        ]
    ];

    public function __construct()
    {
    }
}
