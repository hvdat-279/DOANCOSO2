<!DOCTYPE html>
<html>

<head>
    <title>Thông báo</title>
</head>

<body>
    <h2>Hi {{ $order->user->name }}</h2>

    <p>Cảm ơn quý khách đã tin tưởng và lựa chọn sản phẩm của chúng tôi. Đơn hàng của quý khách đã được xử lý
        thành công, nó sẽ được giao trong thời gian ngắn nhất. Chúc quý khách một ngày tốt lành!</p>
    <h3>Đơn hàng của bạn</h3>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>STT</th>
            <th>Tên sản phẩm</th>
            <th>Size</th>
            <th>Số lượng</th>
            <th>Giá</th>
        </tr>
        <?php $total=0; ?>
        @foreach ($order->details as $item)
        <tr>
            <td>{{ $loop->index + 1 }}</td>
            <td>{{ $item->product->title }}</td>
            <td>{{ $item->size }}</td>
            <td>{{ $item->quantity }}</td>
            <td>{{ number_format($item->product->price * $item->quantity, 0, ',', '.') }} VND</td>
        </tr>
        <?php $total += $item->product->price * $item->quantity ?>
        @endforeach
        <tr>
            <td colspan="4">Tổng tiền</td>
            <td>{{number_format($total, 0, ',', '.') }} VND</td>
        </tr>



    </table>
</body>

</html>