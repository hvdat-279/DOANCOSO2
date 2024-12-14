@extends('layouts.home')

@section('css')
<link rel="stylesheet" href="{{ asset('css/shopping_cart.css') }}">
@endsection
@section('content')
{{-- <div class="product-wrapper"> --}}

    <div class="profile-container">
        <div class="profile-card">
            <div class="profile-card-header">
                <h3>Thông Tin Người Dùng</h3>
            </div>
            <div class="profile-card-picture">
                @if(auth()->user()->img)
                <img src="{{ asset('storage/' . auth()->user()->img) }}" alt="Profile Picture" id="profile-img">
                @else
                <img src="{{ asset('image/picture_info.jpg') }}" alt="Default Picture">
                @endif
                <button class="camera-button" onclick="document.getElementById('uploadImageInput').click()">
                    <i class="fa-solid fa-camera"></i>
                </button>
                <form id="uploadImageForm" method="POST" enctype="multipart/form-data" action="">
                    @csrf
                    <input type="file" id="uploadImageInput" name="img" style="display: none;" onchange="uploadImage()">
                </form>
            </div>
            <div class="profile-card-body">
                <!-- Hiển thị thông tin người dùng -->
                <div class="card-body-left">
                    <div class="profile-form-group">
                        <label for="name">Họ và tên</label>
                        <p>{{ $user->name }}</p>
                    </div>

                    <div class="profile-form-group">
                        <label for="email">Email</label>
                        <p>{{ $user->email }}</p>
                    </div>
                </div>
                <div class="card-body-right">
                    <div class="profile-form-group">
                        <label for="phone">Số điện thoại</label>
                        <p>{{ $user->phone }}</p>
                    </div>

                    <div class="profile-form-group">
                        <label for="address">Địa chỉ</label>
                        <p>{{ $user->address }}</p>
                    </div>
                </div>

            </div>
            <!-- Thêm nút sửa thông tin người dùng -->
            <a href="{{ route('profile.edit') }}" class="profile-btn">Chỉnh sửa thông tin</a>
        </div>
        <div class="profile-order">
            <table>
                <thead>
                    <tr>
                        <th>Mã đơn hàng</th>
                        <th>Ngày đặt</th>
                        <th>Trạng thái</th>
                        <th>Tổng tiền</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($user->orders as $item)

                    <tr>
                        <td>{{ $loop->index + 1 }}</td>
                        <td>{{ $item->created_at->format('d/m/Y') }}</td>
                        <td>
                            @if($item->status == 0)
                            <span style="color: rgb(212, 191, 0)">Chờ duyệt...</span>
                            @elseif($item->status == 1)
                            <span style="color: rgb(54, 81, 255)">Đang giao hàng</span>
                            @elseif($item->status == 2)
                            <span style="color: rgb(22, 216, 0)">Đã nhận hàng</span>
                            @else
                            <span style="color: rgb(202, 28, 28)">Đã hủy hàng</span>
                            @endif
                        </td>
                        <td>{{ number_format($item->getTotalPrice(), 0, ',','.') }} VND</td>
                        <td><button class="btn btn-showOrder" data-id="{{ $item->id }}">Xem chi tiết</button>

                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

    {{--
</div> --}}

<div id="orderDetailModal" class="custom-modal">
    <div class="custom-modal-content">
        <span class="close-modal" onclick="closeModal()">&times;</span>
        <div id="order-detail-content">
            <!-- Nội dung chi tiết đơn hàng sẽ được load vào đây -->
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    function submitImageForm() {
        document.getElementById('uploadImageForm').submit();
    }
    function uploadImage() {
        let formData = new FormData();
        let fileInput = document.getElementById('uploadImageInput');
        let file = fileInput.files[0];
        
        if (!file) return; // Nếu không chọn file, thoát
        
        formData.append('img', file);
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');  
        
        // Gửi AJAX request
        fetch('{{ route("profile.update.image") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken, // Đảm bảo header có CSRF token
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Cập nhật ảnh đại diện mới
                document.getElementById('profile-img').src = data.new_image_url;
                $.toast({
                    heading: 'Thông báo',
                    text: "Cập nhật ảnh thành công!",
                    showHideTransition: 'slide',
                    position: 'top-right',
                    icon: 'success'
                })

            } else {
                $.toast({
                    heading: 'Thông báo',
                    text: 'Có lỗi xảy ra: ' + (data.message || 'Không xác định'),
                    showHideTransition: 'slide',
                    position: 'top-right',
                    icon: 'error'
                })

            }
        })
        .catch(error => {
            console.error('Lỗi:', error);
            alert('Có lỗi xảy ra. Vui lòng thử lại!');
        });
    }
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('orderDetailModal');
        const modalContent = document.getElementById('order-detail-content');

        // Xử lý khi nhấn vào nút "Xem chi tiết"
        document.querySelectorAll('.btn-showOrder').forEach(button => {
            button.addEventListener('click', function () {
                const orderId = this.getAttribute('data-id');

                fetch(`/home/order/${orderId}/detail`, {
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
                        <div class="modal-content-container">
                            <!-- Chi tiết đơn hàng -->
                            <div class="order-info scrollable">
                                <h4>Chi tiết đơn hàng</h4>
                                <p><strong>Mã đơn hàng:</strong> ${data.order.id}</p>
                                <p><strong>Ngày đặt:</strong> ${data.order.created_at}</p>
                                <p><strong>Trạng thái:</strong> ${data.order.status_text}</p>
                                <p><strong>Tổng tiền:</strong> ${data.order.total_price} VND</p>
                                <h5>Sản phẩm:</h5>
                        
                                <div class="table-scrollable">
                                    <table class="product-table">
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
                            <!-- Thông tin khách hàng -->
                            <div class="customer-info">
                                <h4>Thông tin khách hàng</h4>
                                <p><strong>Họ và tên:</strong> ${data.user.name}</p>
                                <p><strong>Email:</strong> ${data.user.email}</p>
                                <p><strong>Số điện thoại:</strong> ${data.user.phone}</p>
                                <p><strong>Địa chỉ:</strong> ${data.user.address}</p>
                                <div class="action-buttons">
                                    ${data.order.status == 0 ? `
                                    <button class="btn btn-danger" onclick="updateOrderStatus(${data.order.id}, 3)">Hủy hàng</button>
                                    ` : ''}
                                    ${data.order.status == 1 ? `
                                    <button class="btn btn-success" onclick="updateOrderStatus(${data.order.id}, 2)">Đã nhận hàng</button>
                                    ` : ''}
                                </div>
                            </div>

                            
                        </div>
                        `;
                        // Hiển thị modal
                        modal.style.display = 'block';
                    } else {
                        modalContent.innerHTML = `<p>Không thể tải chi tiết đơn hàng.</p>`;
                        modal.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Lỗi:', error);
                    modalContent.innerHTML = `<p>Đã xảy ra lỗi khi tải chi tiết đơn hàng.</p>`;
                    modal.style.display = 'block';
                });
            });
        });

        // Đóng modal khi click vào dấu 'X' hoặc bên ngoài modal
        window.onclick = function (event) {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        };

        // Hàm cập nhật trạng thái đơn hàng
        window.updateOrderStatus = function (orderId, status) {
            fetch(`/home/order/${orderId}/updateStatus`, {
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
                    alert('Cập nhật trạng thái thành công.');
                    location.reload(); // Reload để cập nhật danh sách
                } else {
                    alert('Cập nhật trạng thái thất bại.');
                }
            })
            .catch(error => {
                console.error('Lỗi:', error);
                alert('Đã xảy ra lỗi. Vui lòng thử lại.');
            });
        };
    });

    // Hàm đóng modal
    function closeModal() {
        document.getElementById('orderDetailModal').style.display = 'none';
    }


</script>
@endsection