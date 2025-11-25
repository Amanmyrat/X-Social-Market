<?php

namespace App\Http\Controllers\Api;

use App\Models\MarketProduct;
use App\Models\Purchase;
use App\Models\Transaction;
use App\Models\Notification;
use App\Http\Resources\MarketProductResource;
use App\Http\Resources\PurchaseResource;
use App\Enum\NotificationType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller;

class MarketController extends Controller
{
    /**
     * Get all available products in the market
     *
     * @return JsonResponse
     */
    public function getProducts(): JsonResponse
    {
        try {
            $products = MarketProduct::available()
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Products retrieved successfully',
                'data' => MarketProductResource::collection($products)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Purchase a product
     *
     * @param int $productId
     * @param Request $request
     * @return JsonResponse
     */
    public function purchaseProduct(int $productId, Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            DB::beginTransaction();

            $product = MarketProduct::find($productId);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            if (!$product->isAvailable()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product is not available for purchase',
                    'data' => [
                        'is_active' => $product->is_active,
                        'stock' => $product->stock
                    ]
                ], 400);
            }

            $existingPurchase = Purchase::where('user_id', $user->id)
                ->where('product_id', $productId)
                ->exists();

            if ($existingPurchase) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already purchased this product'
                ], 400);
            }

            if ($user->balance_tnt < $product->price_tnt) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient TNT balance',
                    'data' => [
                        'required' => $product->price_tnt,
                        'current_balance' => $user->balance_tnt
                    ]
                ], 400);
            }

            $balanceBefore = $user->balance_tnt;
            $balanceAfter = $balanceBefore - $product->price_tnt;

            $user->balance_tnt = $balanceAfter;
            $user->save();

            $product->stock -= 1;
            $product->save();

            $purchase = Purchase::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'price_tnt' => $product->price_tnt,
                'status' => Purchase::STATUS_PENDING,
            ]);

            $transaction = Transaction::create([
                'user_id' => $user->id,
                'type' => 'spend',
                'source' => 'market_purchase',
                'amount' => $product->price_tnt,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'description' => "Purchased: {$product->name}",
                'metadata' => [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'purchase_id' => $purchase->id,
                ],
            ]);

            Notification::create([
                'recipient_id' => $user->id,
                'type' => NotificationType::MARKET_PURCHASE,
                'title' => 'Purchase Successful',
                'message' => "You have successfully purchased {$product->name} for {$product->price_tnt} TNT",
                'data' => [
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price_tnt' => $product->price_tnt,
                ],
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product purchased successfully',
                'data' => [
                    'purchase' => new PurchaseResource($purchase->load('product')),
                    'transaction' => $transaction,
                    'new_balance' => $balanceAfter,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error processing purchase',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's purchase history
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getUserPurchases(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $purchases = Purchase::where('user_id', $user->id)
                ->with('product')
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'message' => 'Purchases retrieved successfully',
                'data' => [
                    'data' => PurchaseResource::collection($purchases),
                    'pagination' => [
                        'total' => $purchases->total(),
                        'per_page' => $purchases->perPage(),
                        'current_page' => $purchases->currentPage(),
                        'last_page' => $purchases->lastPage(),
                        'from' => $purchases->firstItem(),
                        'to' => $purchases->lastItem(),
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving purchases',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

