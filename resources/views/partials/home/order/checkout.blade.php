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
                <!-- Thanh toán -->
                <div class="order-form-group payment">
                    <label for="payment-method" class="label_payment">Chọn phương thức thanh toán</label>
                    <div>
                        <input type="radio" id="bank_transfer" name="payment_method" value="bank_transfer">
                        <label for="bank_transfer">Chuyển khoản ngân hàng</label>
                    </div>
                    <div>
                        <input type="radio" id="cod" name="payment_method" value="cod">
                        <label for="cod">Thanh toán khi nhận hàng</label>
                    </div>
                </div>
                <!-- QR thanh toán -->
                <div id="qr-code-container" style="display:none; text-align: center;">
                    <p style="font-size: 20px;">Thanh toán qua chuyển khoản ngân hàng</p>
                    <img id="qr-code" src="" alt="QR Code">
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
<script>
    document.querySelectorAll('input[name="payment_method"]').forEach((radio) => {
        radio.addEventListener('change', function () {
            const selectedMethod = this.value;
            const qrContainer = document.getElementById('qr-code-container');
            const qrCodeElement = document.getElementById('qr-code');

            if (selectedMethod === 'bank_transfer') {
                // VietQR format
                const bankBin = '546034'; // Bank code (e.g., VCB)
                const accountNo = '0337626701'; // Account number
                const accountName = 'HOANG VAN DAT'; // Account holder's name
                const template = 'print'; // Full bank details template
                const amount = "{{ number_format($totalPrice, 0, ',', '') }}"; // Total amount as a string without commas or dots
                const orderDescription = 'Thanh toán đơn hàng'; // Order description or any additional information

                // Create the VietQR URL
                const vietQrUrl = `https://img.vietqr.io/image/${bankBin}-${accountNo}-${template}.png?amount=${amount}&addInfo=${orderDescription}&accountName=${encodeURIComponent(accountName)}`;

                // Display the QR code and hide other payment options
                qrCodeElement.src = vietQrUrl;
                qrContainer.style.display = 'block';
            } else {
                // Hide QR code and reset other payment options
                qrContainer.style.display = 'none';
            }
        });
    });

</script>
@endsection