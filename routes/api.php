<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\InvestorController;
use App\Http\Controllers\P2PListingController;
use App\Http\Controllers\P2PTransactionController;
use App\Http\Controllers\PolygonController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\PropertyFinanceController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\TransacaoFinanceiraController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\BuybackController;
use App\Http\Controllers\PlatformSettingsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Rotas públicas
Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/investor-login', [AuthController::class, 'loginInvestidor']);
Route::get('polygon/balance/{address}', [PolygonController::class, 'balance']);
Route::post('register_investors', [InvestorController::class, 'store']);

// Dados do usuário autenticado
Route::middleware('auth:api')->get('/user', fn(Request $request) => $request->user());

// Rotas de Admin (com prefixo e middleware)
Route::prefix('admin')->middleware(['auth:api', 'isAdmin'])->group(function () {
    // Investors
    Route::get('investors', [InvestorController::class, 'index']);
    Route::get('investors/{id}', [InvestorController::class, 'show']);
    Route::put('investors/{id}', [InvestorController::class, 'update']);
    Route::delete('investors/{id}', [InvestorController::class, 'destroy']);

    // Properties
    Route::get('properties', [PropertyController::class, 'index']);
    Route::post('properties', [PropertyController::class, 'store']);
    Route::get('properties/{id}', [PropertyController::class, 'show']);
    Route::get('properties/{id}/tokens', [PropertyController::class, 'tokens']);
    Route::post('properties/{id}/tokenize', [PropertyController::class, 'tokenize']);

    // Imóveis 
    Route::get('imoveis/{id}/financeiro', [PropertyFinanceController::class, 'report']);
    Route::post('imoveis/{id}/buyback', [BuybackController::class, 'buyback']);

    // Configurações e transações
    Route::put('platform-settings', [PlatformSettingsController::class, 'update']);
    Route::get('platform-settings', [PlatformSettingsController::class, 'show']);
    Route::get('transacoes-financeiras', [TransacaoFinanceiraController::class, 'lista']);

    // Perfil
    Route::get('user/profile', [UserController::class, 'profile']);

    // Perfil e carteira
    Route::get('wallet/{id}', [WalletController::class, 'show']);
});

// Rotas de investidor autenticado
Route::prefix('investor')->middleware(['auth:investor'])->group(function () {
    // Perfil e carteira
    Route::get('user/profile', [UserController::class, 'profile']);
    Route::get('wallet/{id}', [WalletController::class, 'show']);
    Route::post('wallet/add-funds', [WalletController::class, 'addFunds']);
    Route::post('wallet/withdraw', [WalletController::class, 'withdraw']);
 
    // Investimentos
    Route::get('investments/{id}', [InvestmentController::class, 'show']);
    Route::get('investments', [InvestmentController::class, 'index']);
    Route::post('investments/purchase', [InvestmentController::class, 'purchase']);
    Route::get('investments/history', [InvestmentController::class, 'history']);
    Route::get('me/investimentos', fn(Request $request) => $request->user()->investments);

    // Imóveis
    Route::get('properties', [PropertyController::class, 'index']);
    Route::get('properties/{id}', [PropertyController::class, 'show']);
    Route::get('properties/{id}/tokens', [PropertyController::class, 'tokens']);

    // P2P
    Route::get('p2p/listings', [P2PListingController::class, 'index']);
    Route::post('p2p/listings', [P2PListingController::class, 'store']);
    Route::delete('p2p/listings/{id}', [P2PListingController::class, 'destroy']);
    Route::get('p2p/transactions', [P2PTransactionController::class, 'index']);
    Route::post('p2p/transactions', [P2PTransactionController::class, 'store']);

    // Suporte
    Route::resource('support-tickets', SupportTicketController::class);

    // Transações financeiras
    Route::get('transacoes-financeiras', [TransacaoFinanceiraController::class, 'index']);
    Route::get('transacoes-financeiras-lista/{id}', [TransacaoFinanceiraController::class, 'showinvest']);

    // Configurações e transações
    Route::put('platform-settings', [PlatformSettingsController::class, 'update']);
    Route::get('platform-settings', [PlatformSettingsController::class, 'show']);
    Route::get('transacoes-financeiras', [TransacaoFinanceiraController::class, 'lista']);
});
