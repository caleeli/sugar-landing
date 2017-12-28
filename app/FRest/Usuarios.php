<?php

namespace App\FRest;

/**
 * Description of Usuarios
 *
 * @author davidcallizaya
 */
class Usuarios extends FRest
{
    protected $path = '/SCIWCF/SCI.svc/users/{agencia}/{producto}';
    protected $method = 'GET';
    protected $sample = [
    ];

    public function __construct($agencia, $producto)
    {
        $this->path = str_replace('{agencia}', $agencia, $this->path);
        $this->path = str_replace('{producto}', $producto, $this->path);
    }
}
