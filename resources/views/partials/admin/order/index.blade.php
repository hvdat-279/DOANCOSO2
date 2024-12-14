@extends('layouts.admin')

@section('title')
<title>Danh sách đơn hàng</title>
@endsection

@section('content')
<div class="content-wrapper">
    @include('partials.admin.content_header', ['name' => 'Đơn hàng', 'key' => 'Danh sách', 'href' =>
    route('order.index')])

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <table class="table">
                        <thead class="">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Mã đơn hàng</th>
                                <th scope="col">Ngày đặt</th>
                                <th scope="col">Trạng thái</th>
                                <th scope="col">Tổng tiền</th>
                                <th scope="col">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ $order->id }}</td>
                                <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                <td>
                                    @if($order->status == 0)
                                    <span class="badge bg-warning text-dark">Chờ duyệt</span>
                                    @elseif($order->status == 1)
                                    <span class="badge bg-primary">Đang giao hàng</span>
                                    @elseif($order->status == 2)
                                    <span class="badge bg-success">Đã nhận hàng</span>
                                    @else
                                    <span class="badge bg-danger">Đã hủy</span>
                                    @endif
                                </td>
                                <td>{{ number_format($order->getTotalPrice(), 0, ',', '.') }} đ</td>
                                <td>
                                    <button class="btn btn-info btn-sm btn-showOrder" data-id="{{ $order->id }}">
                                        Xem chi tiết
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-md-12">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Chi tiết đơn hàng -->
    <div id="orderDetailModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chi tiết đơn hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="customer-info" class="mb-4">
                        <h4>Thông tin khách hàng</h4>
                        <p><strong>Họ và tên:</strong> <span id="customer-name"></span></p>
                        <p><strong>Email:</strong> <span id="customer-email"></span></p>
                        <p><strong>Số điện thoại:</strong> <span id="customer-phone"></span></p>
                        <p><strong>Địa chỉ:</strong> <span id="customer-address"></span></p>
                    </div>

                    <div>
                        <h5>Danh sách sản phẩm</h5>
                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Tên sản phẩm</th>
                                        <th>Số lượng</th>
                                        <th>Đơn giá</th>
                                        <th>Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody id="product-list">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="approve-order" class="btn btn-success">Duyệt đơn</button>
                    <button id="cancel-order" class="btn btn-danger">Hủy đơn</button>
                    <button id="restore-order" class="btn btn-secondary" style="display: none;">Khôi phục</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.btn-showOrder').forEach(button => {
            button.addEventListener('click', function () {
                const orderId = this.getAttribute('data-id');

                // Gửi AJAX request lấy thông tin chi tiết đơn hàng
                fetch(`/admin/order/${orderId}/detail`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Hiển thị thông tin khách hàng
                        document.getElementById('customer-name').textContent = data.user.name;
                        document.getElementById('customer-email').textContent = data.user.email;
                        document.getElementById('customer-phone').textContent = data.user.phone;
                        document.getElementById('customer-address').textContent = data.user.address;

                        // Hiển thị danh sách sản phẩm
                        const productList = document.getElementById('product-list');
                        productList.innerHTML = '';
                        data.order.items.forEach(item => {
                            const row = `
                                <tr>
                                    <td>${item.name}</td>
                                    <td>${item.quantity}</td>
                                    <td>${item.price} đ</td>
                                    <td>${(item.quantity * item.price.replace(/\./g, '')).toLocaleString('vi-VN')} đ</td>
                                </tr>
                            `;
                            productList.insertAdjacentHTML('beforeend', row);
                        });

                        const cancelOrderBtn = document.getElementById('cancel-order');
                        const approveOrderBtn = document.getElementById('approve-order');
                        const restoreOrderBtn = document.getElementById('restore-order');

                        if (data.order.status == 0) { // "Chờ duyệt"
                            cancelOrderBtn.style.display = 'inline-block';
                            approveOrderBtn.style.display = 'inline-block';
                            restoreOrderBtn.style.display = 'none';
                            cancelOrderBtn.onclick = () => updateOrderStatus(orderId, 3); 
                        } else if (data.order.status == 3) { //  "Đã hủy"
                            cancelOrderBtn.style.display = 'none';
                            approveOrderBtn.style.display = 'none';
                            restoreOrderBtn.style.display = 'inline-block';
                            restoreOrderBtn.onclick = () => updateOrderStatus(orderId, 0);
                        } else { 
                            cancelOrderBtn.style.display = 'none';
                            restoreOrderBtn.style.display = 'none';
                            approveOrderBtn.style.display = 'none';
                        }

                        // Hiển thị modal
                        const modal = new bootstrap.Modal(document.getElementById('orderDetailModal'));
                        modal.show();
                    } else {
                        alert('Không thể tải chi tiết đơn hàng.');
                    }
                })
                .catch(error => {
                    console.error('Lỗi:', error);
                    alert('Đã xảy ra lỗi khi tải chi tiết đơn hàng.');
                });
            });
        });

        function updateOrderStatus(orderId, status) {
            fetch(`/admin/order/${orderId}/detailUpdate`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ status })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Cập nhật trạng thái đơn hàng thành công.');
                    location.reload(); 
                } else {
                    alert('Cập nhật trạng thái đơn hàng thất bại.');
                }
            })
            .catch(error => {
                console.error('Lỗi:', error);
                alert('Đã xảy ra lỗi khi cập nhật trạng thái đơn hàng.');
            });
        }
    });
</script>
@endsection