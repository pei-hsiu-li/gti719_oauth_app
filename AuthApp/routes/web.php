<?php

use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

\Illuminate\Support\Facades\Auth::routes();
//Partie 1
/* 
Route::get('/auth/{provider}/redirect', function ($provider) {
    return Socialite::driver($provider)->redirect();
})->where('provider', 'github|google');

Route::get('/auth/{provider}/callback', function ($provider) {
    $user = Socialite::driver($provider)->user();

    $user = \App\Models\User::where([
        'provider' => $provider,
        'provider_id' => $user->getId()
    ])->first();

    if(!$user) {
        $validator = \Illuminate\Support\Facades\Validator::make(
            ['email' => $user->getEmail()],
            ['email' => ['unique:user,email']],
            ['email.unique' => 'Failed']
        );
        if ($validator->fails()){
            return redirect('/login')->withErrors($validator);
        }
    }
    $user = \App\Models\User::create([
        'name' => $user->getName(),
        'email' => $user->getEmail(),
        'provider' => $provider,
        'provider_id' => $user->getId(),
        'email_verified_at' => now()
    ]);
    \Illuminate\Support\Facades\Auth::login($user);
    return redirect('/');
})->where('provider', 'github|google');
 */

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


//Partie 2
Route::get('/login', function(Request $request){
    $request->session()->put("state", $state = Str::random(40));
    $query = http_build_query([
        "client_id" => "96c59a71-1182-4318-bfd7-1326fdeb4b92",
        "redirect_uri" => "http://127.0.0.1:8080/callback",
        "response_type" => "code",
        "scope" => "",
        "state" => $state
    ]);
    return redirect("http://127.0.0.1:8000/oauth/authorize?" . $query);
});

Route::get('/callback', function(Request $request){
    $state = $request->session()->pull('state');

    $response = Http::asForm()->post(
        "http://127.0.0.1:8000/oauth/token",
        [
            "grant_type" => "authorization_code",
            "client_id" => "96c59a71-1182-4318-bfd7-1326fdeb4b92",
            "client_secret" => "U51iAdyIGWZVxfFRSIA5gIfkDv3a9VGRlaBbwh9L",
            "redirect_uri" => "http://127.0.0.1:8080/callback",
            "code" => $request->code
    ]);
    return $response->json();
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
