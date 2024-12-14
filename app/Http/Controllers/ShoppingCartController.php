<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\shopping_cart;
use App\Models\products;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShoppingCartController extends Controller
{
    public function index()
    {
        $user = Auth::user(); // Lấy người dùng đang đăng nhập

        $cartItems = shopping_cart::with(['product'])
            ->where('user_id', $user->id)
            ->get();
        $totalPrice = $cartItems->sum(function ($cartItem) {
            // Tính giá trị mỗi mục trong giỏ hàng
            return $cartItem->product->price * $cartItem->quantity;
        });
        return view('partials.home.shopping_cart', compact('cartItems', 'totalPrice'));
    }




    public function add(Request $request)
    {
        try {
            $product = products::findOrFail($request->id);
            $quantity = $request->quantity > 0 ? floor($request->quantity) : 1;
            $size = $request->size;

            $user = Auth::user(); // Lấy người dùng đang đăng nhập

            // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
            $cartItem = shopping_cart::where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->first();

            if ($cartItem) {
                // Nếu có, cập nhật số lượng
                $cartItem->quantity += $quantity;
                $cartItem->size = $size;
                $cartItem->save();
            } else {
                // Nếu chưa có, thêm mới sản phẩm vào giỏ
                shopping_cart::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'size' => $size,
                    'quantity' => $quantity
                ]);
            }

            $totalQuantity = shopping_cart::where('user_id', $user->id)->sum('quantity');

            return response()->json([
                'success' => true,
                'message' => 'Thêm sản phẩm vào giỏ hàng thành công!',
                'totalQuantity' => $totalQuantity

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thêm sản phẩm.'
            ]);
        }
    }
    public function updateCart($id, Request $request)
    {
        try {
            $quantity = $request->quantity ?: 1;
            $size = $request->size;

            $user = Auth::user();

            $cartItem = shopping_cart::where('user_id', $user->id)
                ->where('product_id', $id)
                ->first();


            if ($cartItem) {
                // Cập nhật giỏ hàng
                $cartItem->quantity = $quantity;
                $cartItem->size = $size;
                $cartItem->save();

                // Tính tổng số lượng và tổng giá trị giỏ hàng sau khi cập nhật
                $cartItems = shopping_cart::with('product')
                    ->where('user_id', $user->id)
                    ->get();

                $totalQuantity = $cartItems->sum('quantity'); // Tổng số lượng
                $totalPrice = $cartItems->sum(function ($cartItem) {
                    // Tính tổng giá trị của giỏ hàng
                    return $cartItem->product->price * $cartItem->quantity;
                });

                return response()->json([
                    'success' => true,
                    'message' => 'Cập nhật sản phẩm thành công!',
                    'totalItem' => number_format($cartItem->product->price * $cartItem->quantity, 0, ',', '.'),
                    'totalQuantity' => $totalQuantity,
                    'totalPrice' => number_format($totalPrice, 0, ',', '.')
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm không tồn tại trong giỏ hàng.' . $id . 'helo'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật sản phẩm.'
            ]);
        }
    }


    public function deleteCart($id)
    {
        try {
            $user = Auth::user();
            // $user = auth()->user();
            $cartItem = shopping_cart::where('user_id', $user->id)
                ->where('product_id', $id)
                ->first();

            if ($cartItem) {
                $cartItem->delete();

                $cartItems = shopping_cart::with('product')
                    ->where('user_id', $user->id)
                    ->get();

                $totalQuantity = $cartItems->sum('quantity'); // Tổng số lượng
                $totalPrice = $cartItems->sum(function ($cartItem) {
                    // Tính tổng giá trị của giỏ hàng
                    return $cartItem->product->price * $cartItem->quantity;
                });

                return response()->json([
                    'success' => true,
                    'message' => 'Xóa sản phẩm khỏi giỏ hàng thành công!',
                    'totalQuantity' => $totalQuantity,
                    'totalPrice' => number_format($totalPrice, 0, ',', '.')
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm không tồn tại trong giỏ hàng.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa sản phẩm.'
            ]);
        }
    }































    // public function add(Request $request, Cart $cart)
    // {
    //     try {
    //         $product = products::findOrFail($request->id);
    //         $quantity = $request->quantity > 0 ? floor($request->quantity) : 1;
    //         $size = $request->size;

    //         $cart->add($product, $quantity, $size);

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Thêm sản phẩm vào giỏ hàng thành công!',
    //             'totalQuantity' => $cart->getTotalQuantity()
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Có lỗi xảy ra khi thêm sản phẩm.'
    //         ]);
    //     }
    // }

    // public function deleteCart($id, Cart $cart)
    // {
    //     try {
    //         $cart->delete($id);

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Xóa sản phẩm thành công!',
    //             'totalQuantity' => $cart->getTotalQuantity(),
    //             'totalPrice' => number_format($cart->getTotalPrice(), 0, ',', '.')
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Có lỗi xảy ra khi xóa sản phẩm.'
    //         ]);
    //     }
    // }

    // public function updateCart($id, Cart $cart, Request $request)
    // {
    //     try {
    //         $quantity = $request->quantity ?: 1;
    //         $size = $request->size;

    //         $cart->update($id, $quantity, $size);

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Cập nhật sản phẩm thành công!',
    //             'totalItem' => number_format($cart->getTotalItem($id), 0, ',', '.'),
    //             'totalQuantity' => $cart->getTotalQuantity(),
    //             'totalPrice' => number_format($cart->getTotalPrice(), 0, ',', '.')
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Có lỗi xảy ra khi cập nhật sản phẩm.'
    //         ]);
    //     }
    // }
}
