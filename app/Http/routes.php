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
        $request->input('offset', 0)
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

Route::post('/rest/adicionarCliente', function (\Illuminate\Http\Request $request) {
    sci_check_request($request);
    //$json -> sci
    $json = json_decode($request->input("json"));
    //$json1 -> sugar
    $json1 = json_decode($request->input("json"));
        $json1->crm_enviado_a_sci_c = 1;
        $json1->status = 'Assigned';
        $results = \App\Lead::save(App\Lead::fromCC($json1));
    $json->cc_nro_oportunidad = $results['id'];
    unset($json->cc_ciudad_nombre);
    unset($json->cc_agencia_nombre);
    unset($json->cc_usuario_nombre);
    return response()->json((new \App\FRest\AdicionaCliente($json))->call());
});

Route::get('/lead/{id}/duplicados', function ($id, \Illuminate\Http\Request $request) {
    if (empty($request->input('phone'))) {
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
     if ($request && is_object($request) && in_array('getContent', get_class_methods($request))) {
         error_log($request->getContent());
     }
     error_log('---------------------------------');
}
