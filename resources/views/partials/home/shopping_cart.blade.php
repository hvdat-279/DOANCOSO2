@extends('layouts.home')

@section('css')
<link rel="stylesheet" href="{{ asset('css/shopping_cart.css') }}">
@endsection
@section('content')
{{-- <div class="product-wrapper"> --}}
    <div class="cart-container">

        <h2 class="cart-title">Giỏ hàng của bạn</h2>
        <div class="cart-wrapper">

            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Hình ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Giá</th>
                        <th>Size</th>
                        <th>Số lượng</th>
                        <th>Tổng tiền</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cartItems as $item )
                    <tr data-id="{{ $item->product_id }}">

                        <td>
                            <img src="{{ $item->product->images->first()->img }}" alt="{{ $item->product->title }}"
                                class="product-image">
                        </td>
                        <td>{{ $item->product->title }}</td>
                        <td>{{ number_format($item->product->price, 0, ',', '.') }} VND</td>
                        <td>
                            <select name="size" id="" class="size-select">
                                <option value="{{ $item->size }}">{{ $item->size }}</option>
                                <option value="S">S</option>
                                <option value="M">M</option>
                                <option value="L">L</option>
                                <option value="XL">XL</option>
                            </select>
                        </td>
                        <td>
                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1"
                                class="quantity-input">
                        </td>
                        <td><span class="item-total" id="item-total"> {{
                                number_format($item->product->price * $item->quantity, 0, ',',
                                '.')
                                }}</span> VND</td>
                        <td>
                            <button type="submit" class="update-button">Cập nhật</button>
                            <button type="submit" class="delete-button"><i class="fa-solid fa-x"></i></button>
                        </td>
                    </tr>

                    @endforeach

                    {{-- @foreach($cart->list() as $item => $value)
                    <tr data-id="{{ $value['product_id'] }}">

                        <td>

                            <img src="{{ $value['image'] }}" alt="{{ $value['title'] }}" class="product-image">
                        </td>
                        <td>{{ $value['title'] }}</td>
                        <td>{{ number_format($value['price'], 0, ',', '.') }} VND</td>
                        <td>
                            <select name="size" id="" class="size-select">
                                <option value="{{ $value['size'] }}">{{ $value['size'] }}</option>
                                <option value="S">S</option>
                                <option value="M">M</option>
                                <option value="L">L</option>
                                <option value="XL">XL</option>
                            </select>
                        </td>
                        <td>
                            <input type="number" name="quantity" value="{{ $value['quantity'] }}" min="1"
                                class="quantity-input">
                        </td>
                        <td><span class="item-total" id="item-total"> {{
                                number_format($value['price'] * $value['quantity'], 0, ',',
                                '.')
                                }}</span> VND</td>
                        <td>
                            <button type="submit" class="update-button">Cập nhật</button>
                            <button type="submit" class="delete-button"><i class="fa-solid fa-x"></i></button>


                        </td>
                    </tr>

                    @endforeach --}}

                </tbody>
            </table>
            <div class="total-price">

                <h3>Tổng số lượng: <span id="total-quantity">{{ $totalQuantity }}</span> (sản phẩm)</h3>
                <h3>Tổng tiền: <span id="total_price">{{ number_format($totalPrice, 0, ',', '.') }}</span> VND</h3>

                <a href="{{ route('order.checkout') }}" class="checkout-button">Thanh toán</a>

            </div>


        </div>
    </div>



    {{--
</div> --}}
@endsection
@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
    // Cập nhật sản phẩm
    document.querySelectorAll('.update-button').forEach(button => {
        button.addEventListener('click', function () {
            const row = this.closest('tr');
            const productId = row.dataset.id;
            const size = row.querySelector('.size-select').value;
            const quantity = row.querySelector('.quantity-input').value;

            fetch(`/home/cart/update-cart/${productId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({  productId, size, quantity })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        $.toast({
                            heading: "Thông báo",
                            text: data.message, // Dùng thông báo từ server
                            showHideTransition: "slide",
                            position: "top-center",
                            icon: "success",
                        });
                        const updatedItemTotal = row.querySelector('.item-total');
                        if (updatedItemTotal) {
                        updatedItemTotal.textContent = data.totalItem;
                        }
                        const itemTotal =document.getElementById("item-total");
                        const totalQuantity =document.getElementById("total-quantity");
                        const totalPrice =document.getElementById("total_price");
                        const cartQuantity = document.getElementById("cart-quantity");
                        // if (itemTotal) {
                        //     itemTotal.textContent = data.totalItem;
                        // }
                        if (totalQuantity) {
                            totalQuantity.textContent = data.totalQuantity;
                            cartQuantity.textContent = data.totalQuantity;
                        }
                        if (totalPrice) {
                            totalPrice.textContent = data.totalPrice;
                        }
                    } else {
                        $.toast({
                            heading: "Thông báo",
                            text: data.message || "Có lỗi xảy ra!",
                            showHideTransition: "slide",
                            position: "top-center",
                            icon: "error",
                        });
                    }
                })
                .catch(err => console.error(err));
        });
    });

    // Xóa sản phẩm
    document.querySelectorAll('.delete-button').forEach(button => {
        button.addEventListener('click', function () {
            const row = this.closest('tr');
            const productId = row.dataset.id;
            const isConfirmed = window.confirm("Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?");

            if(isConfirmed){
                fetch(`/home/cart/delete-cart/${productId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            $.toast({
                                heading: "Thông báo",
                                text: data.message, // Dùng thông báo từ server
                                showHideTransition: "slide",
                                position: "top-center",
                                icon: "success",
                            });
                            // Xóa dòng sản phẩm
                            row.remove();
                            // Cập nhật tổng số lượng và tổng tiền của giỏ hàng
                            const totalQuantity =document.getElementById("total-quantity");
                            const totalPrice =document.getElementById("total_price");
                            const cartQuantity = document.getElementById("cart-quantity");
                            if (totalQuantity) {
                                totalQuantity.textContent = data.totalQuantity;
                                cartQuantity.textContent = data.totalQuantity;
                            }
                            if (totalPrice) {
                                totalPrice.textContent = data.totalPrice;
                            }
                        } else {
                            $.toast({
                            heading: "Thông báo",
                            text: data.message || "Có lỗi xảy ra!",
                            showHideTransition: "slide",
                            position: "top-center",
                            icon: "error",
                            });
                        }
                    })
                    .catch(err => console.error(err));
                }
            });
        });
    });

</script>
@endsection