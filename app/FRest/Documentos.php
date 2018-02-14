<?php

namespace App\FRest;

/**
 * Description of Documentos
 *
 * @author davidcallizaya
 */
class Documentos extends FRest
{
    protected $path = '/documents/1';
    protected $method = 'GET';
    protected $sample = [
        "Documents"  => [
            [
                "descripcion" => "CARNET DE IDENTIDAD",
                "tdoc_id"     => 1
            ],
            [
                "descripcion" => "CARNET DE IDENTIDAD DUPLICADO",
                "tdoc_id"     => 10
            ],
            [
                "descripcion" => "CARNET DIPLOMATICO EN LIBRETA",
                "tdoc_id"     => 11
            ]
        ],
        "Extensions" => [
            [
                "descripcion" => "BENI",
                "extension"   => "BE",
                "tdoc_id"     => 1
            ],
            [
                "descripcion" => "COCHABAMBA",
                "extension"   => "CB",
                "tdoc_id"     => 1
            ],
            [
                "descripcion" => "CHUQUISACA",
                "extension"   => "CH",
                "tdoc_id"     => 1
            ]
        ]
    ];

}
