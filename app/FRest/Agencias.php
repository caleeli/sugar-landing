<?php

namespace App\FRest;

/**
 * Description of Agencias
 *
 * @author davidcallizaya
 */
class Agencias extends FRest
{
    protected $path = '/SCIWCF/SCI.svc/branches';
    protected $method = 'GET';
    protected $sample = [
        [
            "su_nombre"   => "COCHABAMBA",
            "su_oficinas" => [
                [
                    "of_nombre"  => "Oficina Central Sucursal",
                    "of_oficina" => 300
                ],
                [
                    "of_nombre"  => "Centro",
                    "of_oficina" => 301
                ],
                [
                    "of_nombre"  => "Capinota",
                    "of_oficina" => 302
                ]
            ]
        ]
    ];

}
