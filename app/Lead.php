<?php

namespace App;

use Exception;
use DateTime;
use DateInterval;

/**
 * Get Leads
 *
 * @author davidcallizaya
 */
class Lead
{
    const COD_AGENDA = 'sci_cod_agenda_c';
    const FULLNAME = 'crm_fullname_c';
    const VARIANT = 'crm_variant_c';
    const AMOUNT = 'crm_amount_c';
    const PHONE = 'phone_mobile';
    const CITY = 'crm_city_c';
    const LANDING_CODE = 'crm_landing_code_c';
    const FIRST_NAME = 'first_name';
    const LAST_NAME = 'last_name';
    const PRIMER_NOMBRE = 'crm_primer_nombre_c';
    const SEGUNDO_NOMBRE = 'crm_segundo_nombre_c';
    const APELLIDO_PATERNO = 'crm_apellido_paterno_c';
    const APELLIDO_MATERNO = 'crm_apellido_materno_c';
    const APELLIDO_CASADA = 'crm_apellido_casada_c';
    const TIPO_DOCUMENTO = 'crm_tipo_documento_c';
    const NRO_DOCUMENTO = 'crm_nro_documento_c';
    const EXTENSION = 'crm_extension_c';
    const CC_CIUDAD = 'cc_ciudad_c';
    const CC_AGENCIA = 'cc_agencia_c';
    const CC_USUARIO = 'cc_usuario_c';
    const STATUS = 'status';
    const EDAD = 'cc_edad_c';
    const EMAIL = 'crm_email_c';
    const CANAL_INGRESO = 'crm_canal_ingreso_c';
    const SFI_NRO_CUENTA = 'sfi_nro_cuenta_c';
    const SFI_NRO_PRESTAMO = 'sfi_nro_prestamo_c';
    const SFI_MONTO = 'sfi_monto_c';
    const SFI_PRODUCTO = 'sfi_producto_c';
    const SCI_OFICIAL_ASIGNADO = 'sci_oficial_asignado_c';
    const PRODUCTO_DESCRIPCION = 'cc_producto_descripcion_c';
    const USUARIO_ENVIADO = 'cc_usuario_nombre_c';
    const USUARIO_ENVIADO_CARGO = 'cc_usuario_cargo_c';
    const SCI_OFICIAL_ASIGNADO_FECHA = 'sci_oficial_asignado_fecha_c';

    private static $alias = [
        'variant' => 'crm_variant_c',
    ];

    private static $aliasCC = [
        "cc_nro_oportunidad"   => 'id',
        "cc_nombre_completo"   => 'crm_fullname_c',
        "cc_ciudad"            => 'cc_ciudad_c',
        "cc_email"             => 'crm_email_c',
        "cc_agencia"           => 'cc_agencia_c',
        "cc_usuario"           => 'cc_usuario_c',
        "cc_nombre"            => 'crm_primer_nombre_c',
        "cc_segundo_nombre"    => 'crm_segundo_nombre_c',
        "cc_paterno"           => 'crm_apellido_paterno_c',
        "cc_materno"           => 'crm_apellido_materno_c',
        "cc_apellido_casada"   => 'crm_apellido_casada_c',
        "cc_nro_documento"     => 'crm_nro_documento_c',
        "cc_tipo_documento"    => 'crm_tipo_documento_c',
        "cc_telefono"          => 'phone_mobile',
        "cc_nro_producto"      => 'cc_nro_producto_c',
        "cc_priorizar"         => 'cc_priorizar_c',
        "cc_monto"             => 'crm_amount_c',
        "cc_actividad_cliente" => 'cc_actividad_c',
        "cc_producto_id"       => 'cc_producto_id_c',
        "cc_ciudad_nombre"     => "cc_ciudad_nombre_c",
        "cc_agencia_nombre"    => "cc_agencia_nombre_c",
        "cc_usuario_nombre"    => "cc_usuario_nombre_c",
        "cc_usuario_cargo"    => "cc_usuario_cargo_c",
        "cc_producto_descripcion" => "cc_producto_descripcion_c",
        "cc_usuario_email"     => "cc_usuario_email_c",
        "cc_edad"              => "cc_edad_c",
        "cc_usuario_cc"        => "cc_usuario_cc_c",
    ];

    /**
     *
     * @param string $codigo
     */
    public static function findByAgenda($codigo)
    {
        $sugar = Sugar::getConnection();
        $where = self::COD_AGENDA . "='$codigo'";
        $leads = $sugar->get(
            "Leads", ['id', self::COD_AGENDA],
            [
            'where' => $where
            ]
        );
        if (empty($leads)) {
            throw new Exception("Lead not found for " . \App\Lead::COD_AGENDA . "=$codigo");
        }
        return $leads[0];
    }

    public static function update($id, $data)
    {
        $sugar = Sugar::getConnection();
        $where = "id='$id'";
        $leads = $sugar->get(
            "Leads", ['id'],
            [
            'where' => $where
            ]
        );
        if (empty($leads)) {
            throw new Exception("Lead not found $id");
        }
        $data['id'] = $id;
        $data = static::updateStates($data);
        return $sugar->set("Leads", $data);
    }

    private static function updateStates($data)
    {
        if (isset($data['sci_subestado_credito_char_c'])) {
            switch($data['sci_subestado_credito_char_c']) {
                case 'S':
                case 'G':
                case 'C':
                case 'O':
                case 'O':
                case 'T':
                    $data['status'] = 'In Process';
                    break;
                case 'A':
                    break;
                case 'R':
                    $data['status'] = 'NoCalifica';
                    break;
                case 'X':
                    $data['status'] = 'Anulado';
                    break;
            }
        }
        if (!empty($data['sfi_nro_cuenta_c'])) {
            $data['status'] = 'Converted';
            $data['fecha_conversion_c'] = Date('Y-m-d H:i:s');
        }
        if (!empty($data['sfi_nro_prestamo_c'])) {
            $data['status'] = 'Converted';
            $data['fecha_conversion_c'] = Date('Y-m-d H:i:s');
        }
        if (isset($data[self::SCI_OFICIAL_ASIGNADO]) && empty($data[self::SCI_OFICIAL_ASIGNADO_FECHA])) {
            $data[self::SCI_OFICIAL_ASIGNADO_FECHA] = date('Y-m-d H:i:s');
        }
        return $data;
    }

    private static function secure($input)
    {
        return str_replace(['"', "'"], '', $input);
    }

    /**
     *
     * @param type $query Texto a buscar
     * @param type $status New|Assigned|In Process|Converted|NoDesea|NoCalifica|Inubicable|Anulado|Dead|Duplicado|Recycled
     * @param type $offset Inicia en 0
     * @return type
     */
    public static function findFromLanding($query, $status, $offset, $phone=null, $notLike=null, $dateFrom='', $dateTo='', $landing='')
    {
        $query = self::secure($query);
        $status = self::secure($status);
        $offset = self::secure($offset);
        $phone = self::secure($phone);
        //no funciona filtrar el leed en sugar
        //$notLike = self::secure($notLike);
        $like = '%' . preg_replace('/\s+|\'"/', '%', $query) . '%';
        $sugar = Sugar::getConnection();
        //not used more:  OR " . self::CITY . " like '$like'
        //and (' . self::FULLNAME . " like '$like' OR "
        //    . self::PHONE . " like '$like') and "
        $filterPhp = false;
        $filterDate = '';
        $filterStatus = self::STATUS . ($status==='New' ? ' in ("'.$status.'", "Guardado") ' : ' like "'.$status.'" ');
        if (!empty($phone)) {
            $limit = 1000;
        } elseif ($dateFrom || $dateTo) {
            //$filterDate = " and (date_entered>='$dateFrom 00:00:00') and (date_entered<='$dateTo 23:59:59')";
            $filterDate = " and (date_entered<='".self::fixDate("$dateTo 23:59:59")."')";
            $limit = 500;
            //$limit = 1000;
            $filterPhp = true;
            //$filterDate = ''
            $filterStatus = 'status in ("New","Guardado") ';
        } else {
            $limit = 20;
        }
        $where = $filterStatus . $filterDate
            . (!empty($phone) ? (' and ' . self::PHONE . "=\"$phone\"") :(' and ' . self::PHONE . " is not null"));
        $fields = [
                    'id',
                    self::FULLNAME,
                    self::PHONE,
                    self::CITY,
                    self::AMOUNT,
                    'date_entered',
                    self::LANDING_CODE,
                    self::PRIMER_NOMBRE,
                    self::SEGUNDO_NOMBRE,
                    self::APELLIDO_PATERNO,
                    self::APELLIDO_MATERNO,
                    self::APELLIDO_CASADA,
                    //self::TIPO_DOCUMENTO,
                    //self::NRO_DOCUMENTO,
                    self::CANAL_INGRESO,
                    //self::EXTENSION,
                    'cc_producto_id_c',
                    self::CC_CIUDAD,
                    self::CC_AGENCIA,
                    self::CC_USUARIO,
                    //self::STATUS,
                    self::FIRST_NAME,
                    self::LAST_NAME,
                    self::EDAD,
                    'cc_actividad_c',
                    self::EMAIL,
                    //'crm_extension_c',
                    ];
        if (!empty($phone)) {
        $fields = [
                    'id',
                    self::FULLNAME,
                    self::PHONE,
                    self::AMOUNT,
                    'date_entered',
                    self::LANDING_CODE,
                ];
        }
        error_log(json_encode($fields));
        $records = $sugar->get(
                    "Leads",
                    $fields, [
                        'where' => $where,
                        'offset' => $offset,
                        'limit' => $limit,
                        'order_by' => 'date_entered DESC',
                    ]
        );
        //error_log(json_encode([$filterPhp, $limit, count($records), $where]));
        if ($filterPhp) {
            $records = array_filter($records, function ($row) use($dateFrom, $dateTo) {
                $fechaIngreso = date_create($row['date_entered'], timezone_open('UTC'));
                date_timezone_set($fechaIngreso, timezone_open('America/La_Paz'));
                $dateEntered = date_format($fechaIngreso, 'Y-m-d H:i:s');
                return (!$dateFrom && !$dateTo) ||
                    (
                        (!$dateFrom || $dateEntered>="$dateFrom 00:00:00") &&
                        (!$dateTo || $dateEntered<="$dateTo 23:59:59")
                    );
            });
            $records = array_slice($records, 0, 20);
        }
        if ($landing) {
            $records = array_filter($records, function ($row) use($landing) {
                return $row[self::LANDING_CODE] == $landing;
            });
        }
        return (self::completeFromLanding($records));
    }

    private static function fixDate($date)
    {
        $fechaIngreso = date_create($date, timezone_open('America/La_Paz'));
        date_timezone_set($fechaIngreso, timezone_open('UTC'));
        return date_format($fechaIngreso, 'Y-m-d H:i:s');
    }

    private static function groupByPhone($leads)
    {
        for ($i = 0, $l = count($leads); $i < $l;$i++) {
            for ($j = $i + 1; $j < $l; $j++) {
                if ($leads[$i][self::PHONE] === $leads[$j][self::PHONE]) {
                    $similar = array_splice($leads, $j, 1);
                    array_splice($leads, $i + 1, 0, $similar);
                    $i++;
                }
            }
        }
        return $leads;
    }

    private static function completeFromLanding($leads)
    {
        foreach ($leads as &$lead) {
            if (empty($lead[self::PRIMER_NOMBRE]) && empty($lead[self::SEGUNDO_NOMBRE])) {
                static::completeLeadNames($lead);
            }
        }
        return $leads;
    }

    public static function completeLeadNames(&$lead)
    {
        if (empty($lead[self::FULLNAME]) && !empty($lead[self::FIRST_NAME]) && !empty($lead[self::LAST_NAME])) {
            $lead[self::FULLNAME] = $lead[self::FIRST_NAME] . ' ' . $lead[self::LAST_NAME];
            $lead[self::PRIMER_NOMBRE] = $lead[self::FIRST_NAME];
            $lead[self::APELLIDO_PATERNO] = $lead[self::LAST_NAME];
            return;
        }
        if (empty($lead[self::FULLNAME])) {
            return;
        }
        $fullname = preg_replace('/\s+/', ' ', $lead[self::FULLNAME]);
        $names = explode(' ', $fullname);
        $count = count($names);
        switch ($count) {
            case 4:
                $lead[self::PRIMER_NOMBRE] = $names[0];
                $lead[self::SEGUNDO_NOMBRE] = $names[1];
                $lead[self::APELLIDO_PATERNO] = $names[2];
                $lead[self::APELLIDO_MATERNO] = $names[3];
                break;
            case 3:
                $lead[self::PRIMER_NOMBRE] = $names[0];
                $lead[self::APELLIDO_PATERNO] = $names[1];
                $lead[self::APELLIDO_MATERNO] = $names[2];
                break;
            case 2:
                $lead[self::PRIMER_NOMBRE] = $names[0];
                $lead[self::APELLIDO_PATERNO] = $names[1];
                break;
            case 1:
                $lead[self::PRIMER_NOMBRE] = $names[0];
                break;
            default:
                $lead[self::PRIMER_NOMBRE] = $names[0];
                $lead[self::SEGUNDO_NOMBRE] = '';
                for ($i = 1; $i < $count - 2; $i++) {
                    $lead[self::SEGUNDO_NOMBRE] .= " " . $names[$i];
                }
                $lead[self::SEGUNDO_NOMBRE] = trim($lead[self::SEGUNDO_NOMBRE]);
                $lead[self::APELLIDO_PATERNO] = $names[$count - 2];
                $lead[self::APELLIDO_MATERNO] = $names[$count - 1];
        }
        $lead[self::FIRST_NAME] = preg_replace('/\s+/', ' ', @$lead[self::PRIMER_NOMBRE] . ' '
            . @$lead[self::SEGUNDO_NOMBRE]);
        $lead[self::LAST_NAME] = preg_replace('/\s+/', ' ', @$lead[self::APELLIDO_PATERNO] . ' '
            . @$lead[self::APELLIDO_MATERNO]);
    }

    public static function save($data)
    {
        $sugar = Sugar::getConnection();
        return $sugar->set("Leads", $data);
    }

    public static function fromUnbounce($input)
    {
        $data = [];
        foreach ($input as $key => $value) {
            $key = isset(self::$alias[$key]) ? self::$alias[$key] : $key;
            $data[$key] = implode(';', $value);
        }
        return $data;
    }

    public static function fromCC($input)
    {
        $data = [];
        foreach ($input as $key => $value) {
            $key = isset(self::$aliasCC[$key]) ? self::$aliasCC[$key] : $key;
            $data[$key] = $value;
        }
        $data['crm_fullname_c'] = preg_replace('/\s+/', ' ', @$data[self::PRIMER_NOMBRE] . ' '
            . @$data[self::SEGUNDO_NOMBRE] . ' '
            . @$data[self::APELLIDO_PATERNO] . ' '
            . @$data[self::APELLIDO_MATERNO] . ' '
            . empty($data[self::APELLIDO_CASADA])
                ? ''
                : 'de ' . $data[self::APELLIDO_CASADA]
        );
        $data[self::FIRST_NAME] = preg_replace('/\s+/', ' ', @$data[self::PRIMER_NOMBRE] . ' '
            . @$data[self::SEGUNDO_NOMBRE]);
        $data[self::LAST_NAME] = preg_replace('/\s+/', ' ', @$data[self::APELLIDO_PATERNO] . ' '
            . @$data[self::APELLIDO_MATERNO]);
        return $data;
    }

    private static $agencias = null;
    private static $usuarios = [];
    private static $productos = [
        '1' => 'Credito',
        '2' => 'Tarjeta de Crédito',
        '3' => 'Boleta de garantia',
        '4' => 'Caja de Ahorro',
        '5' => 'Cuenta Corriente',
        '6' => 'DPF',
        '7' => 'Mi Red',
        '8' => 'Comex',
        '9' => 'Seguros Masivos-Servicios',
    ];

    public static function completeCiudadNombre(&$lead) {
        if (!static::$agencias) {
            static::$agencias = (array) (new \App\FRest\Agencias())->call();
        }
        if (empty($lead['cc_ciudad_nombre_c']) && !empty($lead['cc_ciudad_c'])) {
            $agencia = (array) array_first(static::$agencias, function ($index, $agencia) use($lead) {
		$agencia = (array) $agencia;
                return $agencia['su_sucursal']==$lead['cc_ciudad_c'];
            });
            if ($agencia) {
                $lead['cc_ciudad_nombre_c'] = $agencia['su_nombre'];
            }
        }
    }

    public static function completeAgenciaNombre(&$lead) {
        if (!static::$agencias) {
            static::$agencias = (array) (new \App\FRest\Agencias())->call();
        }
        if (empty($lead['cc_agencia_nombre_c']) && !empty($lead['cc_agencia_c'])) {
            array_first(static::$agencias, function ($index, $agencia) use(&$lead) {
                $agencia = (array) $agencia;
		foreach((array) $agencia['su_oficinas'] as $oficina) {
                    $oficina = (array) $oficina;
                    if ($oficina['of_oficina']==$lead['cc_agencia_c'])  {
                        $lead['cc_agencia_nombre_c'] = $oficina['of_nombre'];
                        return true;
                    }
                }
            });
        }
    }

    public static function completeUsuarioNombre(&$lead)
    {
        $producto = $lead['cc_nro_producto_c'];
        if (!$producto) $producto = '1';
        $agencia = $lead['cc_agencia_c'];
        for ($ttl = 0; $ttl < 2; $ttl++) {
            if (empty(static::$usuarios["$agencia,$producto"])) {
                static::$usuarios["$agencia,$producto"] = (array) (new \App\FRest\Usuarios($agencia, $producto))->call();
            }
            if (empty($lead['cc_usuario_nombre_c']) && !empty($lead['cc_usuario_c'])) {
                $user = array_first(static::$usuarios["$agencia,$producto"],
                                    function ($index, $usuario) use(&$lead) {
                    $usuario = (array) $usuario;
                    if ($usuario['us_usuario'] == $lead['cc_usuario_c']) {
                        $lead['cc_usuario_nombre_c'] = $usuario['us_nombre'] . ' ' . $usuario['us_paterno'];
                        return true;
                    }
                });
                if ($user) {
                    $lead['cc_nro_producto_c'] = $producto;
                    break;
                }
                $producto = $producto == '1' ? '2' : '1';
            } else {
                break;
            }
        }
        if (empty($lead['cc_usuario_nombre_c']) && !empty($lead['cc_usuario_c'])) {
                $lead['cc_usuario_nombre_c'] = 'Usuario ' . $lead['cc_usuario_c'];
        }
    }

    public static function completeProductoNombre(&$lead) {
        if (empty($lead['cc_producto_descripcion_c']) && !empty($lead['cc_producto_id_c'])) {
            $lead['cc_producto_descripcion_c'] = static::$productos[''.$lead['cc_producto_id_c']];
        }
    }

    /**
     * Busca el historico de un lead por numero de celular.
     *
     * @param type $phone
     */
    public static function buscarHistorico($id, $phone)
    {
        $id = self::secure($id);
        $phone = self::secure($phone);
        $sugar = Sugar::getConnection();
        $where = self::PHONE . "=\"$phone\" and " . self::STATUS
            . " not in (\"Duplicado\") and id != \"" . $id . "\"";
        $fields = [
                    'id',
                    self::FULLNAME,
                    self::PHONE,
                    'date_entered',
                    self::PRODUCTO_DESCRIPCION,
                    self::STATUS,
                    self::SCI_OFICIAL_ASIGNADO,
                    self::USUARIO_ENVIADO,
                    self::LANDING_CODE,
                    self::CANAL_INGRESO,
                ];
        $records = $sugar->get(
                    "Leads",
                    $fields, [
                        'where' => $where,
                        'limit' => 15,
                        'order_by' => 'date_entered DESC',
                    ]
        );
        return $records;
    }
}
