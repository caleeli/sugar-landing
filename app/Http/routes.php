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
    error_log(json_encode([$service, $code, $request->all()]));

    $sugar = new \Asakusuma\SugarWrapper\Rest;

    $sugar->setUrl(env('SUGAR_URL').'/service/v2/rest.php');
    $sugar->setUsername(env('SUGAR_USER'));
    $sugar->setPassword(env('SUGAR_PASSWORD'));

    $sugar->connect();
    error_log(var_export($request->input('json_data'), true));
    
    $data = json_decode($request->input('json_data'));
    $lead = [
        'crm_fullname_c' => $data->nombre_y_apellido,
        'crm_variant_c' => $data->variant,
        'crm_phone_c' => $data->telefono,
        'crm_city_c' => $data->ciudad,
        'crm_landing_code_c' => $code,
    ];

    $results = $sugar->set("Leads", $lead);

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
