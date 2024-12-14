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

    <!-- Modal chi tiết đơn hàng -->
    <div id="orderDetailModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chi tiết đơn hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="order-detail-content">
                    <p>Đang tải chi tiết đơn hàng...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalContent = document.getElementById('order-detail-content');

        // Xử lý khi nhấn vào nút "Xem chi tiết"
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
                        // Load nội dung vào modal
                        modalContent.innerHTML = `
                        <div>
                            <!-- Hàng đầu tiên: Nút Duyệt đơn và Hủy đơn -->
                            <div class="d-flex justify-content-between mb-3">
                                <button class="btn btn-success" id="approveOrder" data-id="${data.order.id}">Duyệt đơn</button>
                                <button class="btn btn-danger" id="cancelOrder" data-id="${data.order.id}">Hủy đơn</button>
                            </div>
                        
                            <!-- Hàng thứ hai: Thông tin người đặt và người nhận -->
                            <div class="row mb-4">
                                <!-- Thông tin người đặt -->
                                <div class="col-md-6">
                                    <h5>Thông tin người đặt</h5>
                                    <p><strong>Họ và tên:</strong> ${data.user.name}</p>
                                    <p><strong>Email:</strong> ${data.user.email}</p>
                                    <p><strong>Số điện thoại:</strong> ${data.user.phone}</p>
                                    <p><strong>Địa chỉ:</strong> ${data.user.address}</p>
                                </div>
                                <!-- Thông tin người nhận -->
                                <div class="col-md-6">
                                    <h5>Thông tin đơn hàng</h5>
                                    <p><strong>Mã đơn hàng:</strong> ${data.order.id}</p>
                                    <p><strong>Ngày đặt:</strong> ${data.order.created_at}</p>
                                    <p><strong>Trạng thái:</strong> ${data.order.status_text}</p>
                                    <p><strong>Tổng tiền:</strong> ${data.order.total_price} VND</p>
                                </div>
                            </div>
                        
                            <!-- Hàng thứ ba: Bảng sản phẩm (cuộn được nếu quá dài) -->
                            <div>
                                <h5>Chi tiết sản phẩm</h5>
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
                                        <tbody>
                                            ${data.order.items.map(item => `
                                            <tr>
                                                <td>${item.name}</td>
                                                <td>${item.quantity}</td>
                                                <td>${item.price} VND</td>
                                                <td>${(item.quantity * parseFloat(item.price.replace(/\./g, '').replace(',',
                                                    '.'))).toLocaleString('vi-VN')} VND</td>
                                            </tr>
                                            `).join('')}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        `;

                         // Gắn sự kiện cho nút "Duyệt đơn"
                        document.getElementById('approveOrder').addEventListener('click', function () {
                            const orderId = this.getAttribute('data-id');

                            // Gửi AJAX request duyệt đơn
                            fetch(`/admin/order/${orderId}/detailUpdate`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({ status: '1' })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                  
                                   $.toast({
                                        heading: "Thông báo",
                                        text: data.message,
                                        showHideTransition: "slide",
                                        position: "top-right",
                                        icon: "success",
                                    });
                                    
                                } else {
                                    modalContent.innerHTML = `<p>Không thể duyệt đơn hàng.</p>`;
                                }
                            })
                            .catch(error => {
                                console.error('Lỗi:', error);
                                modalContent.innerHTML = `<p>Đã xảy ra lỗi khi duyệt đơn hàng.</p>`;
                            });
                        });

                        // Gắn sự kiện cho nút "Hủy đơn"
                        document.getElementById('cancelOrder').addEventListener('click', function () {
                            const orderId = this.getAttribute('data-id');

                            // Gửi AJAX request hủy đơn
                            fetch(`/admin/order/${orderId}/cancel`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({ status: 'canceled' })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    $.toast({
                                        heading: "Thông báo",
                                        text: data.message,
                                        showHideTransition: "slide",
                                        position: "top-right",
                                        icon: "success",
                                    });
                                } else {
                                    modalContent.innerHTML = `<p>Không thể hủy đơn hàng.</p>`;
                                }
                            })
                            .catch(error => {
                                console.error('Lỗi:', error);
                                modalContent.innerHTML = `<p>Đã xảy ra lỗi khi hủy đơn hàng.</p>`;
                            });
                        });

                    } else {
                        modalContent.innerHTML = `<p>Không thể tải chi tiết đơn hàng.</p>`;
                    }
                })
                .catch(error => {
                    console.error('Lỗi:', error);
                    modalContent.innerHTML = `<p>Đã xảy ra lỗi khi tải chi tiết đơn hàng.</p>`;
                });

                // Hiển thị modal
                const modal = new bootstrap.Modal(document.getElementById('orderDetailModal'));
                modal.show();
            });
        });
        // document.querySelectorAll('#approveOrder').forEach(button => {
        //     button.addEventListener('click', function () {
        //         const orderId = this.getAttribute('data-id');

        //         // Gửi AJAX request lấy thông tin chi tiết đơn hàng
        //         fetch(`/admin/order/${orderId}/detailUpdate`, {
        //             method: 'GET',
        //             headers: {
        //                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        //                 'Accept': 'application/json'
        //             }
        //         })
        //         .then(response => response.json())
        //         .then(data => {
        //             if (data.success) {
        //                 $.toast({
        //                     heading: "Thông báo",
        //                     text: data.message,
        //                     showHideTransition: "slide",
        //                     position: "top-right",
        //                     icon: "success",
        //                 });
        //             } else {
        //                 modalContent.innerHTML = `<p>Không thể tải chi tiết đơn hàng.</p>`;
        //             }
        //         })
        //         .catch(error => {
        //             console.error('Lỗi:', error);
        //             modalContent.innerHTML = `<p>Đã xảy ra lỗi khi tải chi tiết đơn hàng.</p>`;
        //         });

        //         // Hiển thị modal
        //         const modal = new bootstrap.Modal(document.getElementById('orderDetailModal'));
        //         modal.show();
        //     });
        // });
        


    });
</script>
@endsection