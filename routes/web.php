<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\AuthController as UserAuthController;
use App\Http\Controllers\User\DashboardController;

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



Route::prefix('user')->name('user.')->group(function () {

    // Guest routes
    Route::middleware('guest:customer')->group(function () {
        Route::get('login', [UserAuthController::class, 'showLogin'])->name('login');
        Route::post('login', [UserAuthController::class, 'login'])->name('login.submit');

        Route::get('forgot-password', [UserAuthController::class, 'showForgotPassword'])->name('password.request');
        Route::post('forgot-password', [UserAuthController::class, 'sendResetLink'])->name('password.email');

        Route::get('reset-password/{token}', [UserAuthController::class, 'showResetPassword'])->name('password.reset');
        Route::post('reset-password', [UserAuthController::class, 'resetPassword'])->name('password.update');

        Route::get('register', [UserAuthController::class, 'showRegister'])->name('register');
        Route::post('register', [UserAuthController::class, 'register'])->name('register.submit');
    });

 
    // Authenticated routes
    Route::middleware('auth:customer')->group(function () {

        Route::get('email/verify', [UserAuthController::class, 'verificationNotice'])->name('verification.notice');

        Route::get('email/verify/{id}/{hash}', [UserAuthController::class, 'verifyEmail'])
            ->middleware(['signed', 'throttle:6,1'])
            ->name('verification.verify');

        Route::post('email/verification-notification', [UserAuthController::class, 'resendVerification'])
            ->middleware('throttle:6,1')
            ->name('verification.send');

        Route::post('logout', [UserAuthController::class, 'logout'])->name('logout');

        Route::middleware('verified:user.verification.notice')->group(function () {

            Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
            Route::get('orders', [DashboardController::class, 'orders'])->name('orders');
            Route::get('orders/{id}', [DashboardController::class, 'showOrder'])->name('orders.show');
            Route::get('profile', [DashboardController::class, 'profile'])->name('profile');
            Route::put('profile', [DashboardController::class, 'updateProfile'])->name('profile.update');
            Route::put('profile/password', [DashboardController::class, 'updatePassword'])->name('profile.password');

        });

    });
});
