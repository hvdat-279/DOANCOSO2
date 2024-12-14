@extends('layouts.home')


@section('content')
<div class="product-container">
    <div class="product_details">
        <!-- Swiper -->
        <div class="swiper mySwiper2">
            <div class="swiper-wrapper">
                @if($productDetail->images->isNotEmpty())
                @foreach ($productDetail->images as $item)
                <div class="swiper-slide"><img src="{{ $item }}" alt="big">
                </div>
                @endforeach
                @else
                <img class="default-image" src="{{ asset('image/picture_info.jpg') }}" alt="Ảnh sản phẩm không có sẵn">
                @endif
                {{-- <div class="swiper-slide"><img
                        src="https://bulbal.vn/wp-content/uploads/2024/09/BO-QUAN-AO-BONG-DA-BULBAL-HUNTER-2-TRANG-1-scaled.jpg"
                        alt=""></div>
                <div class="swiper-slide">Slide 2</div>
                <div class="swiper-slide">Slide 3</div>
                <div class="swiper-slide">Slide 4</div>
                <div class="swiper-slide">Slide 5</div> --}}
                <!-- Thêm các slide khác nếu cần -->
            </div>
            <!-- Nút điều hướng -->
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>

        <!-- Swiper thu nhỏ -->
        <div class="swiper mySwiper">
            <div class="swiper-wrapper">
                @if($productDetail->images->isNotEmpty())
                @foreach ($productDetail->images as $item)
                <div class="swiper-slide"><img src="{{ $item }}" alt="small">
                </div>
                @endforeach
                @else
                <img class="default-image" src="{{ asset('image/picture_info.jpg') }}" alt="Ảnh sản phẩm không có sẵn">
                @endif
                {{-- <div class="swiper-slide"><img
                        src="https://bulbal.vn/wp-content/uploads/2024/09/BO-QUAN-AO-BONG-DA-BULBAL-HUNTER-2-TRANG-1-scaled.jpg"
                        alt=""></div>
                <div class="swiper-slide">Thumb 2</div>
                <div class="swiper-slide">Thumb 3</div>
                <div class="swiper-slide">Thumb 4</div>
                <div class="swiper-slide">Thumb 5</div> --}}
                <!-- Thêm các slide khác nếu cần -->
            </div>
        </div>
    </div>

    <div class="info">
        <div class="product-info">
            <h1 class="product-title">{{ $productDetail->title }}</h1>
            <p class="product-price">{{ number_format($productDetail->price, 0, ',', '.') }}₫</p>
            <p class="product-description">
                {{ $productDetail->description }}
            </p>
            <form class="product-form" method="POST" action="{{ route('cart.add') }}">
                @csrf
                <input type="hidden" name="id" value="{{ $productDetail->id }}">
                <div class="size-selector">
                    <label for="size">Size:</label>
                    <select name="size" id="size" required>
                        <option value="" disabled selected>Chọn một tuỳ chọn</option>
                        <option value="S">S</option>
                        <option value="M">M</option>
                        <option value="L">L</option>
                        <option value="XL">XL</option>
                    </select>
                </div>
                <div class="quantity-selector">
                    <label for="quantity">Số lượng:</label>
                    <input type="number" name="quantity" id="quantity" value="1" min="1" required>
                </div>
                <button type="submit" class="add-to-cart-btn">Thêm vào giỏ hàng</button>
            </form>
        </div>
    </div>
</div>

<!-- Mô tả dưới -->
<div class="description">
    <!-- Tab điều hướng -->
    <div class="tab-container">
        <button class="tab-button active" data-tab="description">Mô tả</button>
        <button class="tab-button" data-tab="comments">Đánh giá</button>
    </div>

    <!-- Nội dung tab -->
    <div class="tab-content">
        <!-- Mô tả sản phẩm -->
        <div id="description" class="tab-panel active">
            <h3>Mô tả sản phẩm</h3>
            <p>This is the full description of the product. You can add a more detailed explanation about the product
                features and benefits here.</p>
        </div>
        <!-- Đánh giá sản phẩm -->
        <div id="comments" class="tab-panel">
            <h3>Đánh giá sản phẩm</h3>
            <!-- Form thêm đánh giá -->
            <form method="POST" action="{{ route('product.comment', $productDetail->id) }}">
                @csrf
                <textarea name="comment" rows="4" placeholder="Nhập đánh giá của bạn..." required></textarea>
                <button type="submit" class="add-comment-btn">Gửi đánh giá</button>
            </form>
            <!-- Hiển thị đánh giá -->
            <div class="comment-list">
                @if($productDetail->comments->isNotEmpty())
                @foreach($productDetail->comments as $comment)
                <div class="comment-item" data-comment-id="{{ $comment->id }}">
                    <div>
                        <small>{{ $comment->created_at->format('d/m/Y H:i') }}</small>
                        <p><strong>{{ $comment->user->name }}</strong>: {{ $comment->content }}</p>
                    </div>
                    <div>
                        @if(Auth::id() === $comment->user_id || Auth::user()->isAdmin())
                        <button class="delete-comment-btn">Xóa</button>
                        @endif
                    </div>
                </div>
                @endforeach
                @else
                <p class="default-comment">Chưa có đánh giá nào.</p>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection


@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabPanels = document.querySelectorAll('.tab-panel');

        tabButtons.forEach(button => {
            button.addEventListener('click', function () {
                // Xóa class "active" khỏi tất cả các button và panel
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabPanels.forEach(panel => panel.classList.remove('active'));

                // Thêm class "active" cho button và panel được chọn
                const tabId = this.getAttribute('data-tab');
                this.classList.add('active');
                document.getElementById(tabId).classList.add('active');
            });
        });

        const commentForm = document.querySelector('form[action*="comment"]');
        if (commentForm) {
            commentForm.addEventListener('submit', function (e) {
                e.preventDefault(); // Ngăn chặn hành động mặc định của form
                const formData = new FormData(this);
                const url = this.action;

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').contentd
                    },
                    body: formData
                }) .then(response => {
                   
                        return response.json();
                    
                })
                .then(data => {
                    if (data.success) {
                        // Thêm bình luận mới vào danh sách
                        const commentList = document.querySelector('.comment-list');
                        const newComment = document.createElement('div');
                        newComment.classList.add('comment-item');
                        newComment.setAttribute('data-comment-id', data.comment.id); // thêm id mới
                        newComment.innerHTML = `
                            <div> 
                                <small>${data.comment.created_at}</small>
                                <p><strong>${data.comment.user_name}</strong>: ${data.comment.content}</p>
                            </div>
                            <div> 
                                <button class="delete-comment-btn">Xóa</button>
                                
                            </div>
                        `;
                        commentList.prepend(newComment);
                        
                       
                        const noCommentsMessage = document.querySelector('.comment-list .default-comment');
                        if (noCommentsMessage) {
                        noCommentsMessage.remove();
                        }
                        // Xóa nội dung trong textarea
                        commentForm.reset();
                    } else {
                        alert('Có lỗi xảy ra. Vui lòng thử lại. ');
                    }
                })
                .catch(error => {
                    console.error('Lỗi:', error);
                    alert('Bạn cần phải đăng nhập trước khi thực hiện đánh giá!');
                    window.location.href = "{{ route('login') }}";
                });
            });
        }

         const commentList = document.querySelector('.comment-list');

        if (commentList) {
            commentList.addEventListener('click', function (e) {
                if (e.target.classList.contains('delete-comment-btn')) {
                    const commentItem = e.target.closest('.comment-item');
                    const commentId = commentItem.getAttribute('data-comment-id');
                    const productId = "{{ $productDetail->id }}"; 


                    if (confirm('Bạn có chắc chắn muốn xóa bình luận này?')) {
                        fetch(`/home/product/${productId}/comment/${commentId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 
                                'Content-Type': 'application/json',
                            },
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log(data);
                            if (data.success) {
                                commentItem.remove();
                                $.toast({
                                    heading: "Thông báo",
                                    text: data.message, 
                                    showHideTransition: "slide",
                                    position: "top-center",
                                    icon: "success",
                                });
                                // alert(data.message);
                            } else {
                                $.toast({
                                    heading: "Thông báo",
                                    text: data.message, 
                                    showHideTransition: "slide",
                                    position: "top-center",
                                    icon: "error",
                                });
                                // alert(data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Lỗi:', error);
                            alert('Có lỗi xảy ra. Vui lòng thử lại.'+ productId + ' ' + commentId);
                        });
                    }
                }
            });
        }
    });

</script>
@endsection