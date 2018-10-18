<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

/**
 * Description of SincronizarController
 *
 * @author David Callizaya <davidcallizaya@gmail.com>
 */
class SincronizarController extends Controller
{

    private $verificacion;
    private $verificacionMsg;

    public function index()
    {
        ini_set('memory_limit', '-1');
        set_time_limit(-1);
        file_put_contents(public_path('js/sincronizacion.sql'), '');
        $res = (new \App\FRest\Sync())->call();
        if (gettype($res) !== 'array') {
            echo $res;
            return;
        }
        $response = [];
        $validados = 0;
        $fallados = 0;
        $file = fopen(public_path('js/sincronizacion.sql'), 'w');
        fwrite($file, "START TRANSACTION;\n");
        fwrite($file, "SET time_zone = '-04:00';\n");
        foreach ($res as $re) {
            $val = $this->procesar($re);
            fwrite($file,
                "update leads set status='" . $val['datos']['status'] . "' where id='".$val['id']."';\n");
            fwrite($file,
                sprintf("update leads_cstm set 
            fecha_conversion_c='%s',
            sci_cod_agenda_c='%s',
            sci_fecha_asignacion_c='%s',
            sci_oficial_asignado_c='%s',
            sci_oficial_asignado_fecha_c='%s',
            sci_subestado_credito_char_c='%s',
            sci_subestado_credito_desc_c='%s',
            sfi_monto_c='%s',
            sfi_nro_cuenta_c='%s',
            sfi_nro_prestamo_c='%s',
            sfi_producto_c='%s'
            where id_c='%s';\n", $val['datos']['fecha_conversion_c'],
                    $val['datos']['sci_cod_agenda_c'],
                    $val['datos']['sci_fecha_asignacion_c'],
                    $val['datos']['sci_oficial_asignado_c'],
                    $val['datos']['sci_oficial_asignado_fecha_c'],
                    $val['datos']['sci_subestado_credito_char_c'],
                    $val['datos']['sci_subestado_credito_desc_c'],
                    $val['datos']['sfi_monto_c'],
                    $val['datos']['sfi_nro_cuenta_c'],
                    $val['datos']['sfi_nro_prestamo_c'],
                    $val['datos']['sfi_producto_c'],
                    $val['id']
            ));
            if (!$val['verificacion']) {
                $fallados++;
            } else {
                $validados++;
            }
            $response[] = $val;
        }
        fwrite($file, "COMMIT;\n");
        fclose($file);
        $sqlUrl = '/js/sincronizacion.sql';
        return view('sync',
            compact('response', 'fallados', 'validados', 'sqlUrl'));
    }

    public function process()
    {
        $val = json_decode(request()->input('d'), true);
        DB::table('leads')
            ->where('id', $val['id'])
            ->update([
                'status' => $val['datos']['status'],
        ]);
        $datos = $val['datos'];
        unset($datos['status']);
        unset($datos['fecha_rechazado_c']);
        DB::table('leads_cstm')
            ->where('id_c', $val['id'])
            ->update($datos);
        return response()->json($val);
    }

    private function procesar($fila)
    {
        $this->verificacion = true;
        $this->verificacionMsg = '';
        $fila = (array) $fila;
        $id = $this->checkNroOp($fila['cc_nro_oportunidad']);
        $data = [
            'status' => $this->checkStatus($fila['cc_estado']),
            'fecha_conversion_c' => $this->checkFecha($fila['cc_fecha_conversion']),
            'fecha_rechazado_c' => $this->checkFecha($fila['cc_fecha_rechazado']),
            'sci_cod_agenda_c' => $this->checkCodAgenda($fila['sci_cod_agenda_c']),
            'sci_fecha_asignacion_c' => $this->checkFecha($fila['sci_fecha_asignacion_c']),
            'sci_oficial_asignado_c' => $this->checkOficial($fila['sci_oficial_asignado_c']),
            //'sci_oficial_asignado_desc_c' => $this->checkOficialDesc($fila['sci_oficial_asignado_desc_c']),
            'sci_oficial_asignado_fecha_c' => $this->checkFecha($fila['sci_oficial_asignado_fecha_c']),
            //'sci_sub_estado_credito' => $this->checkSubestado($fila['sci_sub_estado_credito']),
            'sci_subestado_credito_char_c' => $this->checkSubestadoChar($fila['sci_subestado_credito_char_c']),
            'sci_subestado_credito_desc_c' => $this->checkSubestadoDesc($fila['sci_subestado_credito_desc_c']),
            'sfi_monto_c' => $this->checkMonto($fila['sfi_monto_c']),
            'sfi_nro_cuenta_c' => $this->checkCuenta($fila['sfi_nro_cuenta_c']),
            'sfi_nro_prestamo_c' => $this->checkCuenta($fila['sfi_nro_prestamo_c']),
            'sfi_producto_c' => $this->checkProducto($fila['sfi_producto_c']),
        ];
        return ['verificacion' => $this->verificacion, 'verificacionMsg' => $this->verificacionMsg, 'id' => $id, 'datos' => $data, 'original' => $fila];
    }

    private function checkNroOp($num)
    {
        $this->addMsg(!!$num, 'Missing value cc_nro_oportunidad');
        return $num;
    }

    private function addMsg($ok, $msg)
    {
        $this->verificacionMsg .= $ok ? '' : $msg . '<br>';
        return $ok;
    }

    private function checkStatus($status)
    {
        $this->verificacion &= $this->addMsg(in_array($status,
                [
            'In Process',
            'NoCalifica',
            'Anulado',
            'Converted',
            'Guardado',
            'New',
            'Assigned',
            'Recycled',
            'NoDesea',
            'Inubicable',
            'Anulado',
            'Duplicado',
                ], true), 'Estado no valido ' . $status);
        return $status;
    }

    private function checkFecha($fecha)
    {
        /* @var $datetime \DateTime */
        if ($fecha === null) {
            return $fecha;
        }
        $this->verificacion &= $this->addMsg(preg_match('/^(\d\d)\/(\d\d)\/(\d\d\d\d)$/',
                $fecha), 'Fecha no valida ' . $fecha);
        $datetime = \DateTime::createFromFormat('d/m/Y', $fecha);
        $datetime->setTime(8, 0, 0);
        $this->verificacion &= $this->addMsg($datetime !== false,
            'Fecha no valida ' . $fecha);
        return $datetime ? $datetime->format('Y-m-d H:i:s') : $fecha;
    }

    private function checkCodAgenda($cod)
    {
        return $cod;
    }

    private function checkOficial($oficial)
    {
        return $oficial;
    }

    private function checkOficialDesc($desc)
    {
        return $desc;
    }

    private function checkSubestado($subestado)
    {
        return $subestado;
    }

    private function checkSubestadoChar($subestadoChar)
    {
        return $subestadoChar;
    }

    private function checkSubestadoDesc($subestadoDesc)
    {
        return $subestadoDesc;
    }

    private function checkMonto($monto)
    {
        return $monto;
    }

    private function checkCuenta($cuenta)
    {
        return $cuenta;
    }

    private function checkProducto($prod)
    {
        return $prod;
    }
}
