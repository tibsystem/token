<?php
use App\Http\Controllers\AuthController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\InvestorController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\TransacaoFinanceiraController;
use App\Http\Controllers\P2PListingController;
use App\Http\Controllers\P2PTransactionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/register', [AuthController::class, 'register']);
Route::post('investors', [InvestorController::class, 'store']);
Route::middleware(['auth:api'])->group(function() {
    Route::get('investors', [InvestorController::class, 'index']);
    Route::get('investors/{id}', [InvestorController::class, 'show']);
    Route::put('investors/{id}', [InvestorController::class, 'update']);
    Route::delete('investors/{id}', [InvestorController::class, 'destroy']);
    Route::get('user/profile', [UserController::class, 'profile']);
    Route::get('wallet', [WalletController::class, 'show']);
    Route::post('wallet/add-funds', [WalletController::class, 'addFunds']);
    Route::post('wallet/withdraw', [WalletController::class, 'withdraw']);
    Route::resource('properties', PropertyController::class);
    Route::get('properties/{id}/tokens', [PropertyController::class, 'tokens']);
    Route::post('investments/purchase', [InvestmentController::class, 'purchase']);
    Route::get('investments/history', [InvestmentController::class, 'history']);
    Route::get('p2p/listings', [P2PListingController::class, 'index']);
    Route::post('p2p/listings', [P2PListingController::class, 'store']);
    Route::delete('p2p/listings/{id}', [P2PListingController::class, 'destroy']);
    Route::get('p2p/transactions', [P2PTransactionController::class, 'index']);
    Route::post('p2p/transactions', [P2PTransactionController::class, 'store']);
    Route::resource('support-tickets', SupportTicketController::class);
    Route::resource('transacoes-financeiras', TransacaoFinanceiraController::class);
    // ...outros endpoints
});
