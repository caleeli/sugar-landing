<?php

namespace App\FRest;

/**
 * Description of AdicionaCliente
 *
 * @author davidcallizaya
 */
class AdicionaCliente extends FRest
{
    protected $path = '/SCIWCF/SCI.svc/addClient';
    protected $method = 'POST';
    protected $headers = [
        "content-type: application/json",
    ];
    //@todo: Agregar el texto del producto: cc_producto_descripcion, cc_producto_id
    //@todo: Agregar la glosa mensaje: cc_mensaje
    protected $body = [];
    protected $sample = 'success';

    public function __construct($body)
    {
        $this->body = $body;
    }
}
