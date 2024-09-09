<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TodoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    //put values in session
    session()->put('foo', 'bar');
    Session::put('baz', 'qux');
    session(['hello' => 'world']);


    //get values from session
    $foo = session('foo');
    $baz = Session::get('baz');
    $hello = Session::get('hello');
    // dd($foo, $baz, $hello);
    return view('welcome');
});

Route::get('/set-cookie', function () {
    $userData = [
        'name' => 'John Doe',
        'email' => 'john@example.com'
    ];
    $cookie = cookie('user_data', serialize($userData));
    //get cookie data
    // $cookieData = unserialize($cookie->getValue());

    // dd( $cookieData);
    return response('Cookie has been set')->cookie($cookie);
});

Route::get('/get-cookie', function (Request $request) {
    dd(unserialize($request->cookie('user_data')));
});

Route::resource('todo', TodoController::class);
// Custom route to mark todo as complete
Route::put('todo/{id}/complete', [TodoController::class, 'complete'])->name('todo.complete');

// Custom route to reset the completed todos
Route::delete('todo/completed/reset', [TodoController::class, 'resetCompleted'])->name('todo.completed.reset');

#Alternative to the above code, you can use the following code to achieve the same result:
// Route::get('todo/{id}', [TodoController::class, 'show'])->name('todo.show');
// Route::get('todo', [TodoController::class, 'index'])->name('todo.index');
// Route::get('todo/create', [TodoController::class, 'create'])->name('todo.create');
// Route::post('todo', [TodoController::class, 'store'])->name('todo.store');
// Route::get('todo/{id}/edit', [TodoController::class, 'edit'])->name('todo.edit');
// Route::put('todo/{id}', [TodoController::class, 'update'])->name('todo.update');
// Route::delete('todo/{id}', [TodoController::class, 'destroy'])->name('todo.destroy');



Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
// check middleware 

Route::get('check-middleware', function () {
    return 'You are authorized to access this page';
})->middleware('idCheck');

require __DIR__ . '/auth.php';
