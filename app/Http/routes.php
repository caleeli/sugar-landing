<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::post('/landing/{service}/{code}', function ($service, $code, \Illuminate\Http\Request $request) {
    sci_check_request($request);
    $all = $request->all();
    //error_log(json_encode($all));
    if ($service=='unbounce') { //instapage normal post
        $data_json = json_decode($all['data_json']);
        //error_log(json_encode([$service, $code, $all]));
        //error_log(json_encode($data_json));
        $lead = \App\Lead::fromUnbounce($data_json);
    } else {
        $lead = $all;
    }
    error_log(var_export($lead, true));
    \App\Lead::completeLeadNames($lead);
    $lead['crm_landing_code_c'] = $code;
    if (empty($lead['crm_email_c']) && !empty($lead['email1'])) $lead['crm_email_c'] = $lead['email1'];
    if (empty($lead['email1']) && !empty($lead['crm_email_c'])) $lead['email1'] = $lead['crm_email_c'];
    $lead['crm_canal_ingreso_c'] = 'LANDING';
    $results = \App\Lead::save($lead);

    error_log(print_r($results, true));
});

Route::post('/lead/{id}', function ($id, \Illuminate\Http\Request $request) {
    sci_check_request($request);
    try {
        $json = $request->json();
        $data = [];
        foreach($json as $key => $value) {
            $data[$key] = $value;
        }

        \App\Lead::completeLeadNames($data);
        $results = \App\Lead::update($id, $data);

        error_log(print_r($results, true));

        return ["success" => true, "data" => $results];
    } catch (Exception $ee) {
        return response([
            "success" => false,
            "error"   => $ee->getMessage()
            ], 400);
    }
});

Route::post('/lead/agenda/{codigo}', function ($codigo, \Illuminate\Http\Request $request) {
    sci_check_request($request);
    try {
        $json = $request->json();
        $data = [];
        foreach($json as $key => $value) {
            $data[$key] = $value;
        }

        $lead = \App\Lead::findByAgenda($codigo);
        $data['id'] = $lead['id'];

        $results = \App\Lead::save($data);

        error_log(print_r($results, true));

        return ["success" => true, "data" => $results];
    } catch (Exception $ee) {
        return response([
            "success" => false,
            "error"   => $ee->getMessage()
            ], 400);
    }
});

Route::get('/lead/find', function (\Illuminate\Http\Request $request) {
    sci_check_request($request);
    $leads = \App\Lead::findFromLanding(
        $request->input('query'),
        $request->input('status', 'New'),
        $request->input('offset', 0),
        null,
        null,
        $request->input('dateFrom', ''),
        $request->input('dateTo', '')
    );

    return ["success" => true, "data" => $leads];
});

Route::get('/rest/documentos', function () {
    $res = (new \App\FRest\Documentos())->call();
    return json_encode($res);
});

Route::get('/rest/localidades', function () {
    return (new \App\FRest\Localidades())->call();
});

Route::get('/rest/usuarios/{agencia}/{producto}', function ($agencia, $producto) {
    return (new \App\FRest\Usuarios($agencia, $producto))->call();
});

Route::get('/rest/agencias', function () {
    return (new \App\FRest\Agencias())->call();
});

Route::get('/rest/productos', function () {
    return (new \App\FRest\Productos())->call();
});

Route::get('/rest/getdataall', function () {
    return (new \App\FRest\Sync())->call();
});
Route::get('/sincronizar', 'SincronizarController@index');

Route::post('/rest/adicionarCliente', function (\Illuminate\Http\Request $request) {
    sci_check_request($request);
    //$json -> sci
    $json = json_decode($request->input("json"));
    //$json1 -> sugar
    $json1 = json_decode($request->input("json"));
        $json1->crm_enviado_a_sci_c = 1;
        $json1->status = 'Assigned';
        $json1->sci_fecha_asignacion_c = date('Y-m-d H:i:s');
        $results = \App\Lead::save(App\Lead::fromCC($json1));
    $json->cc_nro_oportunidad = $results['id'];
    unset($json->cc_ciudad_nombre);
    unset($json->cc_agencia_nombre);
    unset($json->cc_usuario_nombre);
    unset($json->cc_usuario_cargo);
    unset($json->cc_usuario_email);
    unset($json->cc_edad);
    unset($json->crm_extension_c);
    unset($json->cc_usuario_cc);
    return response()->json((new \App\FRest\AdicionaCliente($json))->call());
});

Route::post('/rest/guardarCliente', function (\Illuminate\Http\Request $request) {
    sci_check_request($request);
    //$json -> sci
    $json = json_decode($request->input("json"));
    //$json1 -> sugar
    $json1 = json_decode($request->input("json"));
        //A solicitud se filtraran los leads que han sido guardadas
        //if ($json1->cc_usuario_email) {
            $json1->status = 'Guardado';
        //}
    $results = \App\Lead::save(App\Lead::fromCC($json1));
    return ['success' => true];
});

Route::get('/lead/{id}/duplicados', function ($id, \Illuminate\Http\Request $request) {
    if (empty($request->input('phone'))) {
        return ["success" => false, "data" => []];
    }
    if (empty($request->input('landing'))) {
        return ["success" => false, "data" => []];
    }
    $leads = \App\Lead::findFromLanding(
        '',
        'New',
        0,
        $request->input('phone'),
        $id
    );

    return ["success" => true, "data" => $leads];
});
Route::post('/lead/{id}/quitarDuplicados', function ($id, \Illuminate\Http\Request $request) {
    sci_check_request($request);
    $json = json_decode($request->input("json"));
    foreach($json->duplicados as $dupId) {
        if (empty($dupId) || $dupId == $id) continue;
        $data[\App\Lead::STATUS] = 'Duplicado';
        \App\Lead::update($dupId, $data);
    }

    return ["success" => true];
});
Route::get('/lead/completeData', function (\Illuminate\Http\Request $request) {
/*dump(DB::select('select count(*) from leads_cstm where cc_usuario_c is not null'
        . ' or cc_agencia_c is not null'
        . ' or cc_ciudad_c is not null'
        . ' or cc_producto_id_c is not null'));*/
    foreach (DB::select('select * from leads_cstm where (cc_usuario_c is not null'
        . ' or cc_agencia_c is not null'
        . ' or cc_ciudad_c is not null'
        . ' or cc_producto_id_c is not null) and (cc_usuario_nombre_c is null or cc_agencia_nombre_c is null or cc_ciudad_nombre_c is null or cc_producto_descripcion_c is null or cc_nro_producto_c is null) limit 100') as $row) {
        $lead = (array) $row;
        //dump($lead);
        \App\Lead::completeLeadNames($lead);
        \App\Lead::completeCiudadNombre($lead);
        \App\Lead::completeAgenciaNombre($lead);
        \App\Lead::completeUsuarioNombre($lead);
        \App\Lead::completeProductoNombre($lead);
        dump($lead);
        (DB::update('update leads_cstm set'
            . ' cc_usuario_nombre_c=?,'
            . ' cc_agencia_nombre_c=?,'
            . ' cc_ciudad_nombre_c=?,'
            . ' cc_producto_descripcion_c=?,'
            . ' cc_nro_producto_c=?'
            . ' where id_c=?',
            [
                $lead['cc_usuario_nombre_c'],
                $lead['cc_agencia_nombre_c'],
                $lead['cc_ciudad_nombre_c'],
                $lead['cc_producto_descripcion_c'],
                $lead['cc_nro_producto_c'],
                $lead['id_c'],
            ]
        ));

    }
    return ["success" => true];
});
Route::get('/lead/{id}/historico', function ($id, \Illuminate\Http\Request $request) {
    if (empty($request->input('phone'))) {
        return ["success" => false, "data" => []];
    }
    $leads = \App\Lead::buscarHistorico(
        $id,
        $request->input('phone')
    );

    return ["success" => true, "data" => $leads];
});

Route::get('/lead/sync', function (\Illuminate\Http\Request $request) {
	$data = DB::select("select leads.status as cc_estado, leads.id as cc_nro_oportunidad, DATE_FORMAT(CONVERT_TZ(sci_fecha_asignacion_c,'+00:00','-04:00'), '%Y-%m-%d') as cc_fecha_asignacion_conctac, 
	    concat(first_name, ' ', last_name) as cc_persona, cc_usuario_c as cc_usuario_asignado, cc_usuario_nombre_c as cc_usuario_asignado_nombre,
	    (case status
	        when 'Converted' then 1
	        else 0
	        end
	    ) as cc_asociado,
		DATE_FORMAT(CONVERT_TZ(sci_fecha_asignacion_c,'+00:00','-04:00'), '%Y-%m-%d') as cc_fecha_asignacion_oficial,
	    (case status
	        when 'NoDesea' then DATE_FORMAT(CONVERT_TZ(date_modified,'+00:00','-04:00'), '%Y-%m-%d')
	        when 'NoCalifica' then DATE_FORMAT(CONVERT_TZ(date_modified,'+00:00','-04:00'), '%Y-%m-%d')
	        when 'Inubicable' then DATE_FORMAT(CONVERT_TZ(date_modified,'+00:00','-04:00'), '%Y-%m-%d')
	        when 'Anulado' then DATE_FORMAT(CONVERT_TZ(date_modified,'+00:00','-04:00'), '%Y-%m-%d')
	        else null
	        end
	    ) as cc_fecha_rechazado,
		DATE_FORMAT(CONVERT_TZ(fecha_conversion_c,'+00:00','-04:00'), '%Y-%m-%d') as cc_fecha_conversion
	from 
	    leads left join leads_cstm on (leads.id=leads_cstm.id_c)
	where
	    date_entered >= DATE_FORMAT(NOW() ,'%Y-01-01')");
	return response()->json(["success" => true, 'data' => $data]);
});

/*

select leads.status as cc_estado, leads.id as cc_nro_oportunidad, DATE_FORMAT(CONVERT_TZ(sci_fecha_asignacion_c,'+00:00','-04:00'), '%Y-%m-%d') as cc_fecha_asignacion_conctac, 
    concat(first_name, ' ', last_name) as cc_persona, cc_usuario_c as cc_usuario_asignado, cc_usuario_nombre_c as cc_usuario_asignado_nombre,
    (case status
        when 'Converted' then 1
        else 0
        end
    ) as cc_asociado,
	DATE_FORMAT(CONVERT_TZ(sci_fecha_asignacion_c,'+00:00','-04:00'), '%Y-%m-%d') as cc_fecha_asignacion_oficial,
    (case status
        when 'NoDesea' then DATE_FORMAT(CONVERT_TZ(date_modified,'+00:00','-04:00'), '%Y-%m-%d')
        when 'NoCalifica' then DATE_FORMAT(CONVERT_TZ(date_modified,'+00:00','-04:00'), '%Y-%m-%d')
        when 'Inubicable' then DATE_FORMAT(CONVERT_TZ(date_modified,'+00:00','-04:00'), '%Y-%m-%d')
        when 'Anulado' then DATE_FORMAT(CONVERT_TZ(date_modified,'+00:00','-04:00'), '%Y-%m-%d')
        else null
        end
    ) as cc_fecha_rechazado
from 
    leads left join leads_cstm on (leads.id=leads_cstm.id_c)
where
    date_entered >= DATE_FORMAT(NOW() ,'%Y-01-01')

cc_estado => el estado asociado al LEAD

cc_nro_oportunidad

cc_fecha_asignacion_conctac => la fecha en la cual el contact asigna el LEAD

cc_persona_id => Nombre completo del cliente

cc_usuario_asignado => El nombre completo (o código si lo tienes registrado) del JEFE DE AGENCIA (Creditos) u OFICIAL DE PLATAFORMA (Captaciones)

cc_asociado => 1 si el lead es CONVERTIDO y 0 si no lo es

cc_fecha_asignacion_oficial => fecha de la asignación al oficial de créditos

cc_fecha_rechazado => Fecha de rechazo del LEAD,

*/


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    //
});

function sci_check_request($request=null) {
     error_log($_SERVER['REQUEST_METHOD'] . ' ' . $_SERVER['REQUEST_URI']);
	 $body = '';
     if ($request && is_object($request) && in_array('getContent', get_class_methods($request))) {
		 $body = $request->getContent();
         error_log($body);
     }
     error_log('---------------------------------');
	 if (strtolower($_SERVER['REQUEST_METHOD'])!='get') {
		 $log = new \App\LeadsLog([
			 'method' => $_SERVER['REQUEST_METHOD'],
			 'url' => $_SERVER['REQUEST_URI'],
			 'content' => @urldecode($body),
//			 'from' => $_SERVER['REMOTE_HOST'],
		 ]);
		 $log->save();
	 }
}

