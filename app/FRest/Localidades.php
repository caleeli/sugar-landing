<?php

namespace App\FRest;

/**
 * Description of Localidades
 *
 * @author davidcallizaya
 */
class Localidades extends FRest
{
    protected $path = '/places';
    protected $method = 'GET';
    protected $sample = [
        [
            "division_id"   => "DPTO",
            "localidad_id"  => 1,
            "localidad_sup" => "RAIZ",
            "nombre"        => "CHUQUISACA"
        ],
        [
            "division_id"   => "PROV",
            "localidad_id"  => 101,
            "localidad_sup" => "1",
            "nombre"        => "OROPEZA"
        ],
        [
            "division_id"   => "SECPROV",
            "localidad_id"  => 10101,
            "localidad_sup" => "101",
            "nombre"        => "CAPITAL - SUCRE"
        ]
    ];

}
