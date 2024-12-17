<?php

use App\goHoltz\API\Response as API;
use App\Http\Controllers\Items\Items as cItems;
use App\Http\Controllers\MercadoLivre\oAuth as cMercadoLivreOAuth;
use App\Http\Controllers\User\Auth as cAuth;
use App\Http\Controllers\User\User as cUser;
use Illuminate\Support\Facades\Route;

Route::any('/', function () {
    $response = API::dataStructure();
    return API::success($response, 'API is running');
})->name('root');

/**
 * User
 */
Route::group(['prefix' => 'user'], function () {
    Route::controller(cUser::class)->group(function () {
        Route::post('/', 'create')->name('user.create');
    });

    /**
     * Auth
     */
    Route::group(['prefix' => 'auth'], function () {
        Route::controller(cAuth::class)->group(function () {
            Route::post('/', 'login')->name('user.auth.login');
            Route::delete('/', 'logout')->middleware('accessToken')->name('user.auth.logout');
        });
    });
});

/**
 * Mercado Livre
 */
Route::group(['prefix' => 'mercadolivre'], function () {
    Route::controller(cMercadoLivreOAuth::class)->group(function () {
        /**
         * Mercado Livre oAuth
         */
        Route::group(['prefix' => 'oauth'], function () {
            Route::get('/', 'redirect')->name('ml.oauth.redirect');
            Route::match(['GET', 'POST'], '/callback', 'callback')->name('ml.oauth.callback');
        });

        Route::match(['GET', 'POST'], '/notifications', 'notifications')->name('ml.notifications');
    });
});

/**
 * Items
 */
Route::group(['prefix' => 'items'], function () {
    Route::controller(cItems::class)->middleware('accessToken')->group(function () {
        Route::get('/', 'listItems')->name('items.list');
        Route::post('/', 'registerItem')->name('items.register');
        Route::patch('/{id?}', 'updateItem')->name('items.update');
        Route::delete('/{id?}', 'deleteItem')->name('items.delete');
    });
});
