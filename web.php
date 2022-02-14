<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LeadContractController;
use App\Http\Controllers\LeadStatusController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TaxController;
use Illuminate\Support\Facades\URL;

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

Auth::routes([
    'register' => false
]);
Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware' => ['auth', 'user']], function() {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/user', [UserController::class, 'index'])->name('user');    
    Route::get('/account', [AccountController::class, 'index'])->name('account');
    Route::post('/personalize', [AccountController::class, 'personalize']);    
    Route::get('/user/{id}',[AccountController::class, 'user'])->name('user_edit');

    //reminders
    Route::get('/newreminder', [ReminderController::class, 'newReminder'])->name('new_reminder');
    Route::post('/newreminder', [ReminderController::class, 'addReminder']);
    Route::get('/reminders', [ReminderController::class, 'index'])->name('reminders_list'); ;
    Route::get('/reminders/{reminderstatus}', [ReminderController::class, 'remindersByStatus']);
    Route::get('/reminder/{id}', [ReminderController::class, 'reminder']);
    Route::post('/reminder/upreminder', [ReminderController::class, 'upReminder']);
    Route::post('/reminderstatus', [ReminderController::class, 'reminderStatus']);
    Route::post('/delreminder', [ReminderController::class, 'delReminder']);

    //leads
    Route::get('/leads', [LeadController::class, 'index'])->name('leads_list');    
    Route::get('/leads/{statusfilter}', [LeadController::class, 'leadsByStatus']);    
    Route::post('/lead/uplead',[LeadController::class, 'upLead']);    
    Route::get('/newlead', [LeadController::class, 'newLeadIndex'])->name('new_lead');
    Route::post('/newlead',[LeadController::class, 'newLead']); 
    Route::get('/lead/{id}',[LeadController::class, 'lead']);

    //search    
    Route::get('/search', [SearchController::class,'index'])->name('search');
    Route::post('/search', [SearchController::class,'search']);    
});

Route::group(['middleware' => ['auth', 'admin']], function() {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings')->middleware(['auth', 'verified','password.confirm']);

    //Stats
    Route::get('/stats', [StatsController::class, 'index'])->name('stats');

    //exports
    Route::get('export/{model}', [StatsController::class, 'export']);

    //users
    Route::post('/newuser', [AdminController::class, 'newUser']);
    Route::get('/users', [UsersController::class, 'index'])->name('users_list');
    Route::post('/updaterole', [UsersController::class, 'updateRole']);
    Route::post('/updateuser', [AccountController::class, 'upuser']);
        
    //contracts
    Route::get('/newcontract', [LeadContractController::class, 'newLeadContractIndex'])->name('new_leadcontract');
    Route::post('/newcontract', [LeadContractController::class, 'newLeadContract']);    
    Route::get('/contracts', [LeadContractController::class, 'index'])->name('leadcontracts_list');
    Route::get('/contract/{id}',[LeadContractController::class, 'leadContract'])->name('leadcontract_edit');
    Route::post('/contract/upcontract',[LeadContractController::class, 'upContract']);
    Route::post('/delcontract',[LeadContractController::class, 'delContract']);

    //service
    Route::get('/service/{id}', [ServiceController::class, 'service'])->name('service_edit');
    Route::post('/upservice', [ServiceController::class, 'upService']);
    Route::post('/newservice', [ServiceController::class, 'newService']);

    //tax
    Route::get('/tax/{id}', [TaxController::class, 'tax'])->name('tax_edit');
    Route::post('/uptax', [TaxController::class, 'upTax']);
    Route::post('/newtax', [TaxController::class, 'newTax']);

    //leadstatus
    Route::get('/leadstatus/{id}', [LeadStatusController::class, 'leadstatus'])->name('leadstatus_edit');
    Route::post('/upleadstatus', [LeadStatusController::class, 'upLeadStatus']);
    Route::post('/newleadstatus', [LeadStatusController::class, 'newLeadStatus']);

    //currency
    Route::post('/upcurrency', [SettingsController::class, 'currencyChange']); 
    Route::post('/dellead',[LeadController::class, 'delLead']);
});


//URL::forceScheme('https');
//FORCE TO USE HTTPS