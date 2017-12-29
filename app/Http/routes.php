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
    $all = $request->all();
    error_log(json_encode($all));
    $data_json = json_decode($all['data_json']);
    error_log(json_encode([$service, $code, $all]));
    error_log(json_encode($data_json));
    if ($service!='unbounce') {
        return;
    }
    $lead = \App\Lead::fromUnbounce($data_json);
    \App\Lead::completeLeadNames($lead);
    $lead['crm_landing_code_c'] = $code;
    $results = \App\Lead::save($lead);

    error_log(print_r($results, true));
});

Route::post('/lead/{id}', function ($id, \Illuminate\Http\Request $request) {
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

    $leads = \App\Lead::findFromLanding($request->input('query'));

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
    //$json -> sci
    $json = json_decode($request->input("json"));
    //$json1 -> sugar
    $json1 = json_decode($request->input("json"));
        $json1->crm_enviado_a_sci_c = 1;
        $json1->status = 'Assigned';
        $results = \App\Lead::save(App\Lead::fromCC($json1));
    $json->cc_nro_oportunidad = $results['id'];
    return response()->json((new \App\FRest\AdicionaCliente($json))->call());
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
