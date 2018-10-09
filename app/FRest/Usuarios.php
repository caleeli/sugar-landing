<?php

namespace App\FRest;

/**
 * Description of Usuarios
 *
 * @author davidcallizaya
 */
class Usuarios extends FRest
{
    protected $path = '/users/{agencia}/{producto}';
    protected $method = 'GET';
    protected $sample = [
        [
            "us_usuario" => 1234,
            "us_nombre"  => 'Usuario',
            "us_paterno" => 'de Prueba',
            "us_cargo" => 'ENCARGADO DE PLATAFORMA',
        ]
    ];

    public function __construct($agencia, $producto)
    {
        $this->path = str_replace('{agencia}', $agencia, $this->path);
        $this->path = str_replace('{producto}', $producto, $this->path);
    }
}
