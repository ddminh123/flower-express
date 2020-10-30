<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
use App\Models\Webhook;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('webhook-invoice', function (){
    $data = \request()->all();
    $webhook = Webhook::query()->create([
        'data' => json_encode($data),
        'type' => 'invoice'
    ]);
    $webhookId = $webhook->id;
    dispatch(new \App\Jobs\WebhookInvoice($webhookId))->delay(now()->addSeconds(5))->onQueue('webhook');
    return 'ok';
});

Route::post('webhook-customer', function (){
    $data = \request()->all();
    $webhook = Webhook::query()->create([
        'data' => json_encode($data),
        'type' => 'customer'
    ]);
    $webhookId = $webhook->id;
    dispatch(new \App\Jobs\WebhookCustomer($webhookId))->delay(now()->addSeconds(5))->onQueue('webhook');
    return 'ok';
});

Route::post('webhook-product', function (){
    $data = \request()->all();
    $webhook = Webhook::query()->create([
        'data' => json_encode($data),
        'type' => 'product'
    ]);
    $webhookId = $webhook->id;
    dispatch(new \App\Jobs\WebhookProduct($webhookId))->delay(now()->addSeconds(5))->onQueue('webhook');
    return 'ok';
});
