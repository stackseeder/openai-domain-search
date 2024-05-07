<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrganizationFinderController;

Route::get('/', [OrganizationFinderController::class, 'index']);
Route::post('/', [OrganizationFinderController::class, 'show'])->name('organization.show');
//Route::get('/', function () {
//    $organization = Http::withToken(config('services.openai.secret'))
//        ->post('https://api.openai.com/v1/chat/completions',
//            [
//                "model"    => "gpt-3.5-turbo",
//                "messages" => [
//                    [
//                        "role"    => "system",
//                        "content" => "You are an organization finder assistant, skilled in find the right organization name by domain, just give you a domain or an URL then you will provide the exact domain own organization name, if you don't know the answer then just return Unknow text. For example give you domain 'dn.no' then the answer is `Dagens Næringsliv`"
//                    ],
//                    [
//                        "role"    => "user",
//                        "content" => "dn.no"
//                    ]
//                ]
//            ])->json('choices.0.message.content');
//
//    return view('display', ['organization' => $organization]);
//});
