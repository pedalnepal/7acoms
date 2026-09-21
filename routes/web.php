<?php
use Illuminate\Support\Facades\Route;

Route::prefix('admin/dashboard')
    ->middleware(['auth:web'])
    ->group(function () {


    Route::group(['namespace' => 'App\Http\Controllers\Admin'], function () {

        Route::get('/', 'DashboardController@index')->name('dashboard');
        Route::resources([
            'user'                  =>  'UserController',
            'page'                  =>  'PageController',
            'slider'                =>  'SliderController',
            'team-category'     =>  'TeamCategoryController',
            'team'              =>  'TeamController',
        ]);


        Route::post('page/{id}/restore', ['uses'=>'PageController@restore', 'as'=>'page.restore']);

        Route::post('slider/{id}/restore', ['uses'=>'SliderController@restore', 'as'=>'slider.restore']);
        Route::post('slider/ajax', ['uses'=>'SliderController@ajax', 'as'=>'slider.ajax']);

        Route::get('menu/create/{id}', ['uses'=>'MenuController@create', 'as'=>'menu.new']);
        Route::post('menu/ajax', ['uses'=>'MenuController@ajax', 'as'=>'menu.ajax']);
        Route::post('menu/{id}', ['uses'=>'MenuController@store', 'as'=>'menu.store']);
        Route::resource('menu', 'MenuController')->except([
            'create', 'store'
        ]);


        Route::post('team-category/{id}/restore', ['uses'=>'TeamCategoryController@restore', 'as'=>'cteam.restore']);
        Route::post('team-category/ajax', ['uses'=>'TeamCategoryController@ajax', 'as'=>'cteam.ajax']);

        Route::post('team/{id}/restore', ['uses'=>'TeamController@restore', 'as'=>'team.restore']);
        Route::post('team/ajax', ['uses'=>'TeamController@ajax', 'as'=>'team.ajax']);

        // Registrations
        Route::get('registration', ['uses'=>'RegistrationController@index', 'as'=>'registration.index']);
        Route::get('registration/{id}', ['uses'=>'RegistrationController@show', 'as'=>'registration.admin.show']);
        Route::get('registration/{id}/download/{type}', ['uses'=>'RegistrationController@download', 'as'=>'registration.download']);
        Route::post('registration/{id}/restore', ['uses'=>'RegistrationController@restore', 'as'=>'registration.restore']);
        Route::post('registration/{id}/payment-status', ['uses'=>'RegistrationController@updatePaymentStatus', 'as'=>'registration.payment_status']);
        Route::delete('registration/{id}', ['uses'=>'RegistrationController@destroy', 'as'=>'registration.destroy']);

        // Abstract submissions
        Route::get('abstract', ['uses'=>'AbstractController@index', 'as'=>'abstract.index']);
        Route::get('abstract/{id}', ['uses'=>'AbstractController@show', 'as'=>'abstract.show']);
        Route::get('abstract/{id}/download', ['uses'=>'AbstractController@download', 'as'=>'abstract.download']);
        Route::post('abstract/{id}/restore', ['uses'=>'AbstractController@restore', 'as'=>'abstract.restore']);
        Route::delete('abstract/{id}', ['uses'=>'AbstractController@destroy', 'as'=>'abstract.destroy']);

        Route::get('setting', ['uses'=>'SettingController@index', 'as'=>'setting.index']);
        Route::post('setting', ['uses'=>'SettingController@store', 'as'=>'setting.store']);
        Route::get('setting/home', ['uses'=>'SettingController@home', 'as'=>'setting.home']);
        Route::post('setting/home', ['uses'=>'SettingController@home_store', 'as'=>'setting.home_store']);

        Route::get('media', ['uses'=>'MediaController@index', 'as'=>'media.index']);
        Route::post('media', ['uses'=>'MediaController@action', 'as'=>'media.action']);
        Route::post('media/ajax', ['uses'=>'MediaController@ajax', 'as'=>'media.ajax']);
        Route::delete('media/{id}', ['uses'=>'MediaController@destroy', 'as'=>'media.destroy']);
        Route::post('media/{id}/restore', ['uses'=>'MediaController@restore', 'as'=>'media.restore']);

    });
});

require __DIR__.'/auth.php';


Route::group(['namespace'=>'App\Http\Controllers\Front'], function () {
    Route::get('/', 'HomeController@index')->name('home');


    Route::get('contact-us', 'FrontController@contactUs')->name('contact.us');
    Route::get('about-acoms', 'FrontController@aboutACOMS')->name('about.acoms');
    Route::get('about-naoms', 'FrontController@aboutNAOMS')->name('about.naoms');
    Route::get('registration-details', 'FrontController@registrationDetails')->name('registration.details');
    Route::get('abstract-submission', 'FrontController@abstractSubmit')->name('abstract.submission');
    Route::post('abstract-submission', 'FrontController@abstractStore')
        ->middleware('throttle:6,1')
        ->name('abstract.store');
    Route::get('venue-details', 'FrontController@venueDetails')->name('venue.details');
    Route::get('accommodation', 'FrontController@accommodation')->name('accommodation');
    Route::get('travel-info', 'FrontController@travelInfo')->name('travel.info');
    Route::get('organizing-committee', 'FrontController@organizingCommittee')->name('organizing.committee');
    Route::get('registration-form', 'FrontController@registrationForm')->name('registration.form');
    Route::post('registration-form', 'FrontController@registrationStore')
        ->middleware('throttle:6,1')
        ->name('registration.store');

    // Registration payment (Cybersource Unified Checkout). The reference is an
    // unguessable UUID, so the delegate can return to their own payment without
    // signing in — and cannot reach anyone else's.
    Route::get('registration-payment/{reference}', 'PaymentController@show')->name('registration.payment');
    Route::post('registration-payment/{reference}', 'PaymentController@process')->name('registration.payment.process');
    Route::get('registration-payment/{reference}/complete', 'PaymentController@complete')->name('registration.payment.complete');

    Route::get('sitemap.xml', 'SitemapController@index')->name('sitemap');

    // Catch-all — must stay LAST so the specific routes above are reachable.
    Route::get('{permalink}', 'FrontController@pageDetail')->name('page.detail');

});
