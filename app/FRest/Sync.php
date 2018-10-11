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
            "cc_estado" => 0,
            "cc_fecha_conversion" => null,
            "cc_fecha_rechazado" => null,
            "cc_nro_oportunidad" => "d9dc8a19-b230-70e0-d830-5ae28467499c",
            "sci_cod_agenda_c" => null,
            "sci_fecha_asignacion_c" => "27/04/2018",
            "sci_oficial_asignado_c" => null,
            "sci_oficial_asignado_desc_c" => null,
            "sci_oficial_asignado_fecha_c" => null,
            "sci_sub_estado_credito" => 0,
            "sci_subestado_credito_char_c" => "",
            "sci_subestado_credito_desc_c" => "",
            "sfi_monto_c" => "de 7.000 a 35.000 Bs.",
            "sfi_nro_cuenta_c" => null,
            "sfi_nro_prestamo_c" => null,
            "sfi_producto_c" => null
        ],
        [
            "cc_estado" => 0,
            "cc_fecha_conversion" => null,
            "cc_fecha_rechazado" => null,
            "cc_nro_oportunidad" => "30b47a60-e01d-67c7-60c9-5b06d3b86fb1",
            "sci_cod_agenda_c" => null,
            "sci_fecha_asignacion_c" => "24\/05\/2018",
            "sci_oficial_asignado_c" => null,
            "sci_oficial_asignado_desc_c" => null,
            "sci_oficial_asignado_fecha_c" => null,
            "sci_sub_estado_credito" => 0,
            "sci_subestado_credito_char_c" => "",
            "sci_subestado_credito_desc_c" => "",
            "sfi_monto_c" => "de 70.000 Bs. hacia adelante",
            "sfi_nro_cuenta_c" => null,
            "sfi_nro_prestamo_c" => null,
            "sfi_producto_c" => null
        ]
    ];

    public function __construct()
    {
        $this->sample = json_decode(file_get_contents('/Users/davidcallizaya/Downloads/getdataall.json'));
    }
}
