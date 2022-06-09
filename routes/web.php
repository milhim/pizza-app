<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Auth::routes();


Route::get('/email/verify', function () {
    return view('auth.verify');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
 
    return redirect('/home');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
 
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.resend');

//pizza routes
Route::get('/pizza/{pizza:name}', [OrderController::class, 'index'])->name('pizza');
Route::get('/pizza/{pizza:name}', [PizzaHomeController::class, 'index'])->name('pizza');
Route::get('/pizza/{pizza:name}/order', [PizzaHomeController::class, 'orderIndex'])->name('pizza.order');

Route::group(['middleware'=>['auth','verified']],function(){

    //profile routes
    Route::get('/users/{user:firstname}/profile', [ProfileController::class, 'index'])->name('users.profile');
    Route::get('/users/{user:firstname}/profile/edit', [ProfileController::class, 'edit'])->name('users.profile.edit');
    Route::put('/users/{user:firstname}/profile/edit', [ProfileController::class, 'updata'])->name('users.profile.edit');
    //orders routes
    Route::post('/pizza/{pizza:name}/order', [OrderController::class, 'palceOrder'])->name('pizza.order');
    Route::get('/users/{user:firstname}/profile/orders', [OrderController::class, 'orders'])->name('users.profile.orders');
    Route::get('/users/{user:firstname}/profile/orders/{order}', [OrderController::class, 'ordersDetails'])->name('users.profile.orders.details');
    Route::delete('/orders/{order}/delete', [OrderController::class, 'deleteOrder'])->name('orders.delete');
    Route::get('/orders/{order}/update', [OrderController::class, 'updateOrderPage'])->name('orders.update');
    Route::put('/orders/{order}/update', [OrderController::class, 'updateOrder'])->name('orders.update');

});




Route::group([
'middleware'=>'is_admin',
'prefix'=>'admin'
],function(){

//admin route here
Route::get('dashboard',[AdminController::class, 'dashboard'])->name('dashboard');
Route::get('users',[AdminController::class, 'showUsers'])->name('admin.users');
Route::get('orders',[AdminController::class, 'showOrders'])->name('admin.orders');
//pizza CRUD
Route::get('pizza/add',[PizzaController::class, 'addPizzaPage'])->name('admin.add.pizza');
Route::post('pizza/add',[PizzaController::class, 'addPizza'])->name('admin.add.pizza');
Route::get('pizza/{pizza}/update',[PizzaController::class, 'updatePizzaPage'])->name('admin.update.pizza');
Route::put('pizza/{pizza}/update',[PizzaController::class, 'updatePizza'])->name('admin.update.pizza');
Route::delete('pizza/{pizza}/delete',[PizzaController::class, 'deletePizza'])->name('admin.delete.pizza');
//Users CRUD
Route::get('users/{user}',[UserController::class, 'userDetails'])->name('admin.user');
Route::get('users/{user}/update',[UserController::class, 'updateUserPage'])->name('admin.update.user');
Route::put('users/{user}/update',[UserController::class, 'updateUser'])->name('admin.update.user');
Route::delete('users/{user}/delete',[UserController::class, 'deleteUser'])->name('admin.delete.user');
//Orders CRUD
Route::get('users/{user}/{order}',[OrderOrderController::class, 'orderDetiles'])->name('admin.order.details');
Route::get('users/{user}/{order}/update',[OrderOrderController::class, 'orderUpdatePage'])->name('admin.order.update');
Route::put('users/{user}/{order}/update',[OrderOrderController::class, 'orderUpdate'])->name('admin.order.update');
Route::delete('users/{user}/{order}/delete',[OrderOrderController::class, 'orderDelete'])->name('admin.order.delete');


});
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->middleware(['auth', 'verified'])->name('home');