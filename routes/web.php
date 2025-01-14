<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ControllerLoginAdmin;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ShoppingCartController;
use App\Http\Controllers\OrderController;

// Route::get('/', function () {
//     return view('welcome');
// });




Route::prefix('/home')->group(
    function () {

        Route::get('/login', [ControllerLoginAdmin::class, 'login'])->name('login');
        Route::post('/login', [ControllerLoginAdmin::class, 'postLogin']);

        Route::get('/register', [ControllerLoginAdmin::class, 'showRegister'])->name('register');
        Route::post('/register', [ControllerLoginAdmin::class, 'postRegister']);

        Route::get('/forgot-password', [ControllerLoginAdmin::class, 'showForgotPassword'])->name('forgot.password');
        Route::post('/forgot-password', [ControllerLoginAdmin::class, 'sendPasswordReset'])->name('password.email');

        Route::get('/reset-password/{token}', [ControllerLoginAdmin::class, 'showResetPassword'])->name('password.reset');
        Route::post('/reset-password', [ControllerLoginAdmin::class, 'resetPassword'])->name('password.update');

        Route::get('/logoutHome', [ControllerLoginAdmin::class, 'logout'])->name('home.logout');


        Route::get('/', [HomeController::class, 'index'])->name("home");

        Route::get('/product', [HomeController::class, 'showCategoryProducts'])->name('products');
        Route::get('/product_details', [HomeController::class, 'showProductDetail'])->name('product.details');

        Route::post('/chatbot', [ChatbotController::class, 'handleChat'])->name('chatbot');

        Route::prefix('/')->middleware('user')->group(function () {
            Route::prefix('cart')->group(
                function () {
                    Route::get('/', [ShoppingCartController::class, 'index'])->name('cart');
                    Route::post('/add-cart', [ShoppingCartController::class, 'add'])->name('cart.add');
                    Route::delete('/delete-cart/{id}', [ShoppingCartController::class, 'deleteCart'])->name('cart.delete');
                    Route::post('/update-cart/{id}', [ShoppingCartController::class, 'updateCart'])->name('cart.update');
                }
            );
            Route::prefix('order')->group(
                function () {
                    Route::get('/', [OrderController::class, 'checkout'])->name('order.checkout'); // đơn hàng khi nhấn thanh toán
                    // Route::get('/detail/{order}', [OrderController::class, 'detail'])->name('order.detail');
                    Route::post('/', [OrderController::class, 'post_checkout']); // sự kiên khi nhấn đặt hàng
                    Route::get('/{id}/detail', [OrderController::class, 'getOrderDetail']); // hiển thị chi tiết đơn hàng bên info
                    Route::post('/{id}/updateStatus', [OrderController::class, 'updateStatus']); // cập nhật các trạng thái
                }
            );
            Route::prefix('profile')->group(
                function () {
                    Route::get('/', [UserController::class, 'showProfile'])->name('profile');
                    Route::get('/edit', [UserController::class, 'editProfile'])->name('profile.edit');
                    Route::post('/edit', [UserController::class, 'updateProfile'])->name('profile.update');
                    Route::post('/update-image', [UserController::class, 'updateImage'])->name('profile.update.image');
                }
            );


            Route::post('/product/{id}/comment', [ProductsController::class, 'addComment'])->name('product.comment');
            Route::delete('/product/{productId}/comment/{commentId}', [ProductsController::class, 'deleteComment'])->name('product.comment.delete');
        });
    }
);

// admin
Route::get('/loginAdmin', [ControllerLoginAdmin::class, 'loginAdmin'])->name('loginAdmin');
Route::post('/loginAdmin', [ControllerLoginAdmin::class, 'postLoginAdmin'])->name('admin.login');
Route::get('/logout', [ControllerLoginAdmin::class, 'logoutAdmin'])->name('admin.logout');


Route::prefix('/admin')->middleware('admin')->group(function () {
    Route::get('/', function () {
        return view('home_admin');
    })->name("admin.home");


    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('categories.index');

        Route::get('/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/store', [CategoryController::class, 'store'])->name('categories.store');

        Route::get('/edit/{id}', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::post('/update/{id}', [CategoryController::class, 'update'])->name('categories.update');

        Route::get('/delete/{id}', [CategoryController::class, 'delete'])->name('categories.delete');
    });



    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('user.index');

        Route::get('/add', [UserController::class, 'add'])->name('user.add');
        Route::post('/create', [UserController::class, 'create'])->name('user.create');

        Route::get('/edit/{id}', [UserController::class, 'edit'])->name('user.edit');
        Route::post('/update/{id}', [UserController::class, 'update'])->name('user.update');

        Route::get('/delete/{id}', [UserController::class, 'delete'])->name('user.delete');
    });

    Route::prefix('product')->group(function () {
        Route::get('/', [ProductsController::class, 'index'])->name('product.index');

        Route::get('/add', [ProductsController::class, 'add'])->name('product.add');
        Route::post('/create', [ProductsController::class, 'create'])->name('product.create');

        Route::get('/edit/{id}', [ProductsController::class, 'edit'])->name('product.edit');
        Route::post('/update/{id}', [ProductsController::class, 'update'])->name('product.update');

        Route::get('/delete/{id}', [ProductsController::class, 'delete'])->name('product.delete');
    });
    Route::prefix('order')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('order.index');
        Route::get('/{id}/detail', [OrderController::class, 'getOrderDetail']);
        Route::post('/{id}/detailUpdate', [OrderController::class, 'updateOrder']);
    });
});

// Route::prefix('categories')->group(function () {
//     Route::get('/', [CategoryController::class, 'index'])->name('categories.index');

//     Route::get('/create', [CategoryController::class, 'create'])->name('categories.create');
//     Route::post('/store', [CategoryController::class, 'store'])->name('categories.store');

//     Route::get('/edit/{id}', [CategoryController::class, 'edit'])->name('categories.edit');
//     Route::post('/update/{id}', [CategoryController::class, 'update'])->name('categories.update');

//     Route::get('/delete/{id}', [CategoryController::class, 'delete'])->name('categories.delete');
// });




// Route::prefix('user')->group(function () {
//     Route::get('/', [UserController::class, 'index'])->name('user.index');

//     Route::get('/add', [UserController::class, 'add'])->name('user.add');
//     Route::post('/create', [UserController::class, 'create'])->name('user.create');

//     Route::get('/edit/{id}', [UserController::class, 'edit'])->name('user.edit');
//     Route::post('/update/{id}', [UserController::class, 'update'])->name('user.update');

//     Route::get('/delete/{id}', [UserController::class, 'delete'])->name('user.delete');
// });


// Route::prefix('product')->group(function () {
//     Route::get('/', [ProductsController::class, 'index'])->name('product.index');

//     Route::get('/add', [ProductsController::class, 'add'])->name('product.add');
//     Route::post('/create', [ProductsController::class, 'create'])->name('product.create');

//     Route::get('/edit/{id}', [ProductsController::class, 'edit'])->name('product.edit');
//     Route::post('/update/{id}', [ProductsController::class, 'update'])->name('product.update');

//     Route::get('/delete/{id}', [ProductsController::class, 'delete'])->name('product.delete');
// });






// home