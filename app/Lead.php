<?php

namespace App;

use Exception;

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
        "cc_producto_descripcion" => "cc_producto_descripcion_c",
        "cc_usuario_email"     => "cc_usuario_email_c",
        "cc_edad"                 => "cc_edad_c",
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
                case 'A':
                    $data['status'] = 'In Process';
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
        }
        if (!empty($data['sfi_nro_prestamo_c'])) {
            $data['status'] = 'Converted';
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
    public static function findFromLanding($query, $status, $offset, $phone=null, $notLike=null)
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
        $where = self::STATUS . ' like "'.$status.'" and ' . self::PHONE . " is not null"
            . (!empty($phone) ? ' and ' . self::PHONE . "=\"$phone\"" :'');
        return (self::completeFromLanding($sugar->get(
                    "Leads",
                    [
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
                    self::TIPO_DOCUMENTO,
                    self::NRO_DOCUMENTO,
                    self::EXTENSION,
                    'cc_producto_id_c',
                    self::CC_CIUDAD,
                    self::CC_AGENCIA,
                    self::CC_USUARIO,
                    self::STATUS,
                    self::FIRST_NAME,
                    self::LAST_NAME,
                    self::EDAD,
                    'cc_actividad_c',
                    self::EMAIL,
                    'crm_extension_c',
                    ], [
                        'where' => $where,
                        'offset' => $offset,
                        'limit' => 20,
                        'order_by' => 'date_entered DESC',
                    ]
        )));
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
}
