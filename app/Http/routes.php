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
    $data_json = json_decode($all['data_json']);
    error_log(json_encode([$service, $code, $all]));
    error_log(json_encode($data_json));
    
    $lead = \App\Lead::fromUnbounce($data_json);
    $lead['crm_landing_code_c'] = $code;
    $results = \App\Lead::save($lead);

    error_log(print_r($results, true));
});

Route::get('/landing/{service}/{code}', function ($service, $code, \Illuminate\Http\Request $request) {
    error_log(json_encode([$service, $code, $request->all()]));

    $sugar = new \Asakusuma\SugarWrapper\Rest;

    $sugar->setUrl(env('SUGAR_URL').'/service/v2/rest.php');
    $sugar->setUsername(env('SUGAR_USER'));
    $sugar->setPassword(env('SUGAR_PASSWORD'));

    $sugar->connect();

    $results = $sugar->set("Leads", [
        'crm_fullname_c' => $request->all('nombre_y_apellido'),
        'crm_variant_c' => $request->input('variant'),
        'crm_phone_c' => $request->input('telefono'),
        'crm_city_c' => $request->input('ciudad'),
        'crm_landing_code_c' => $code,
    ]);

    error_log(print_r($results, true));
});

Route::post('/lead/{id}', function ($id, \Illuminate\Http\Request $request) {
    try {
        $json = $request->json();
        $data = [];
        foreach($json as $key => $value) {
            $data[$key] = $value;
        }

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
