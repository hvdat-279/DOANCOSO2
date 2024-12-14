<!-- Main Sidebar Container phần bên trái -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    {{-- <a href="index3.html" class="brand-link">
        <img src="{{ asset('/image/logo.png') }}" alt="DSport Logo" class="brand-image img-circle elevation-3"
            style="opacity: .8">
        <span class="brand-text font-weight-light">DSport</span>
    </a> --}}
    <a href="#" class="brand-link d-flex align-items-center p-2">
        <img src="{{ asset('/image/logo.png') }}" alt="DSport Logo" class="brand-image shadow-sm me-2"
            style="width: 40px; height: 40px; opacity: .8;">
        <span class="brand-text font-weight-light fw-bold">DSport</span>
    </a>
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <p class="text-white">Xin chào!</p>
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ Auth::user()->name }}</a>
            </div>
        </div>


        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->

                <li class="nav-item">
                    <a href="{{ route('admin.home') }}" class="nav-link">
                        <i class=" nav-icon fa-solid fa-gauge"></i>
                        <p>
                            Bảng điều khiển
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('user.index') }}" class="nav-link">
                        <i class="nav-icon fa-solid fa-users"></i>
                        <p>
                            Khách hàng
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('categories.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-th"></i>
                        <p>
                            Danh mục sản phẩm
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('product.index') }}" class="nav-link">
                        <i class="nav-icon fa-solid fa-shirt"></i>
                        <p>
                            Sản phẩm
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fa-solid fa-truck-fast"></i>
                        <p>
                            Đơn hàng
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('order.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Chưa Duyệt</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('order.index') }}?status=1" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                {{-- <i class="fa-solid fa-check nav-icon"></i> --}}
                                <p>Đã Duyệt</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('order.index') }}?status=2" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                {{-- <i class="fa-solid fa-truck-fast nav-icon"></i> --}}
                                <p>Giao Hàng Thành Công</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('order.index') }}?status=3" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Đơn Hàng Đã Hủy</p>
                            </a>
                        </li>

                    </ul>
                </li>

            </ul>
        </nav>
    </div>
</aside>