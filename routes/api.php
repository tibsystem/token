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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/register', [AuthController::class, 'register']);
Route::post('/auth/investor-login', [AuthController::class, 'loginInvestidor']);

Route::post('investors', [InvestorController::class, 'store']);
Route::get('polygon/balance/{address}', [PolygonController::class, 'balance']);

// Rotas de acesso do investidor autenticado
Route::middleware(['auth:investor'])->group(function () {
    /**
     * @OA\Get(
     *     path="/api/me/investimentos",
     *     tags={"Investments"},
     *     security={{"sanctum":{}}},
     *     summary="Listar investimentos do usuário autenticado",
     *
     *     @OA\Response(response=200, description="Sucesso")
     * )
     */
    Route::get('/me/investimentos', function (Request $request) {
        return $request->user()->investments;
    });

    /**
     * @OA\Get(
     *     path="/api/imoveis",
     *     tags={"Properties"},
     *     security={{"sanctum":{}}},
     *     summary="Listar imóveis (atalho)",
     *
     *     @OA\Response(response=200, description="Sucesso")
     * )
     */
    Route::get('/imoveis', [PropertyController::class, 'index']);

    /**
     * @OA\Get(
     *     path="/api/p2p/ofertas",
     *     tags={"P2P Listings"},
     *     security={{"sanctum":{}}},
     *     summary="Listar ofertas P2P (atalho)",
     *
     *     @OA\Response(response=200, description="Sucesso")
     * )
     */
    Route::get('/p2p/ofertas', [P2PListingController::class, 'index']);
});

// Rotas administrativas protegidas por auth:api e verificação de administrador
Route::middleware(['auth:api', 'isAdmin'])->group(function () {
    Route::get('investors', [InvestorController::class, 'index']);
    Route::get('investors/{id}', [InvestorController::class, 'show']);
    Route::put('investors/{id}', [InvestorController::class, 'update']);
    Route::delete('investors/{id}', [InvestorController::class, 'destroy']);
    Route::resource('properties', PropertyController::class);
    Route::get('properties/{id}/tokens', [PropertyController::class, 'tokens']);
    Route::post('properties/{id}/tokenize', [PropertyController::class, 'tokenize']);
    Route::get('user/profile', [UserController::class, 'profile']);
    Route::get('admin/imoveis/{id}/financeiro', [PropertyFinanceController::class, 'report']);
    Route::post('admin/imoveis/{id}/buyback', [BuybackController::class, 'buyback']);

    // Configurações da plataforma
    Route::get('platform-settings', [PlatformSettingsController::class, 'show']);
    Route::put('platform-settings', [PlatformSettingsController::class, 'update']);
});

// Funcionalidades disponíveis para investidores autenticados
Route::middleware(['auth:investor'])->group(function () {
    Route::get('user/profile', [UserController::class, 'profile']);
    Route::get('wallet', [WalletController::class, 'show']);
    Route::post('wallet/add-funds', [WalletController::class, 'addFunds']);
    Route::post('wallet/withdraw', [WalletController::class, 'withdraw']);
    Route::get('properties', [PropertyController::class, 'index']);
    Route::get('properties/{id}', [PropertyController::class, 'show']);
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
