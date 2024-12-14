<?php

namespace App\Http\Controllers;

use App\Mail\OrderMail;
use App\Models\order;
use App\Models\shopping_cart;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    //for user
    public function checkout()
    {
        $user = Auth::user();
        $cartItems = shopping_cart::with(['product'])
            ->where('user_id', $user->id)
            ->get();
        $totalPrice = $cartItems->sum(function ($cartItem) {
            // Tính giá trị mỗi mục trong giỏ hàng
            return $cartItem->product->price * $cartItem->quantity;
        });
        return view('partials.home.order.checkout', compact('user', 'cartItems', 'totalPrice'));
    }
    // public function detail(order $order)
    // {
    //     $user = Auth::user();

    //     return view('partials.home.order.detail', compact('user', 'order'));
    // }
    public function getOrderDetail($id)
    {
        $order = Order::with('details')->find($id);

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy đơn hàng.']);
        }

        $statusTexts = [
            0 => 'Chờ duyệt...',
            1 => 'Đang giao hàng',
            2 => 'Đã nhận hàng',
            3 => 'Đã hủy hàng'
        ];

        return response()->json([
            'success' => true,
            'order' => [
                'id' => $order->id,
                'created_at' => $order->created_at->format('d/m/Y'),
                'status' => $order->status,
                'status_text' => $statusTexts[$order->status] ?? 'Không xác định',
                'total_price' => number_format($order->getTotalPrice(), 0, ',', '.'),
                'items' => $order->details->map(function ($item) {
                    return [
                        'name' => $item->product->title,
                        'quantity' => $item->quantity,
                        'size' => $item->size,
                        'price' => number_format($item->product->price, 0, ',', '.')
                    ];
                })->toArray()
            ],
            'user' => [
                'name' => $order->user->name ?? 'Không xác định',
                'email' => $order->user->email ?? 'Không xác định',
                'phone' => $order->user->phone ?? 'Không xác định',
                'address' => $order->user->address ?? 'Không xác định',
            ]
        ]);
    }
    public function updateOrder(Request $request, $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Đơn hàng không tồn tại.'], 404);
        }

        $request->validate([
            'status' => 'required|integer|in:0,1,3', // Chỉ cho phép cập nhật trạng thái "Đang giao hàng" (1) hoặc "Đã hủy" (3)
        ]);

        $order->status = $request->status;
        $order->save();

        return response()->json(['success' => true, 'message' => 'Cập nhật trạng thái đơn hàng thành công.']);
    }
    public function updateStatus(Request $request, $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Đơn hàng không tồn tại.'], 404);
        }
        $validated = $request->validate([
            'status' => 'required|integer'
        ]);

        $order->status = $validated['status'];
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái đơn hàng thành công.'
        ]);
    }

    public function post_checkout(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'address' => 'required',
            'payment_method' => 'required'
        ]);

        $data = $request->only('name', 'email', 'phone', 'address');
        $data['user_id'] = $user->id;
        $data['payment_method'] = $request->payment_method == 'bank_transfer' ? 'Chuyển khoản' : 'Tiền mặt';

        if ($order = order::create($data)) {
            foreach ($user->carts as $cart) {
                $data1 = [
                    'order_id' => $order->id,
                    'product_id' => $cart->product_id,
                    'size' => $cart->size,
                    'quantity' => $cart->quantity
                ];
                OrderDetail::create($data1);
            }
        }
        $user->carts->delete();
        Mail::to($user->email)->send(new OrderMail($order));

        return redirect()->route('home')->with('success', 'Đặt hàng thành công!');
    }

    //for admin
    public function index()
    {
        $status = request('status', 0);
        $orders = order::orderBy('id', 'DESC')->where('status', $status)->paginate();
        // $orders = order::orderBy('id', 'DESC')->paginate();
        return view('partials.admin.order.index', compact('orders'));
    }
}
