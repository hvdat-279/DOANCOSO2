@extends('layouts.home')

@section('css')
<link rel="stylesheet" href="{{ asset('css/shopping_cart.css') }}">
@endsection
@section('content')
{{-- <div class="product-wrapper"> --}}
    <div class="order-container">


        <div class="order_item">
            <h2 class="order-title">Giỏ hàng của bạn</h2>
            <div class="order-wrapper">

                <table class="order-table">
                    <thead>
                        <tr>
                            <th>Hình ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Giá</th>
                            <th>Size</th>
                            <th>Số lượng</th>


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
                                <label for="" name="size" id="" class="size-select">{{ $item->size }}</label>

                            </td>
                            <td>

                                <label for="" name="quantity" class="quantity-input">{{ $item->quantity }}</label>
                            </td>

                        </tr>

                        @endforeach

                    </tbody>
                </table>
                <div style="text-align: right; padding: 10px;">
                    <p style="font-size: 32px; font-weight: bold; ">Tổng tiền</p>
                    <p style="font-size: 20px; color:rgb(255, 116, 92)">{{ number_format($totalPrice, 0, ',', '.')}} VND
                    </p>
                </div>



            </div>
        </div>
        <div class="order_info">
            <h2 class="order-title">Thông tin khách hàng</h2>
            <form method="POST" action="">
                @csrf

                <!-- Họ và tên -->
                <div class="order-form-group">
                    <label for="name">Họ và tên</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}">
                    @error('name')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email -->
                <div class="order-form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}">
                    @error('email')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Số điện thoại -->
                <div class="order-form-group">
                    <label for="phone">Số điện thoại</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                    @error('phone')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Địa chỉ -->
                <div class="order-form-group">
                    <label for="address">Địa chỉ</label>
                    <input type="text" id="address" name="address" value="{{ old('address', $user->address) }}">
                    @error('address')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>


                <!-- Nút cập nhật -->
                <button type="submit" class="order-submit-btn">Đặt hàng</button>
                <a href="" class="order-back-btn">Quay lại trang thông tin</a>
            </form>
        </div>
    </div>




    {{--
</div> --}}
@endsection
@section('script')

@endsection