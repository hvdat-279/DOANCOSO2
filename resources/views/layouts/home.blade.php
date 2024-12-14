<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>DSport</title>
    <link rel="icon" href="{{ asset('/image/logo.png') }}" type="image/x-icon">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    {{-- swiper --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    {{-- toast --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.css">
    {{-- link css --}}
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/product_details.css') }}">
    <link rel="stylesheet" href="{{ asset('css/products.css') }}">


    <style>
        /* Icon chatbot */
        #chatbot-icon {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            cursor: pointer;
            z-index: 1000;
        }

        #chatbot-icon img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
        }

        /* Giao diện hộp chat */
        #chatbot-box {
            position: fixed;
            bottom: 90px;
            right: 20px;
            width: 300px;
            height: 400px;
            background: #f1f1f1;
            border: 1px solid #ccc;
            border-radius: 10px;
            display: none;
            flex-direction: column;
            z-index: 1000;
        }

        #chatbot-header {
            background: #007bff;
            color: white;
            padding: 10px;
            text-align: center;
            border-radius: 10px 10px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        #chatbot-header button {
            background: transparent;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        #chatbot-messages {
            flex: 1;
            overflow-y: auto;
            padding: 10px;
        }

        #chatbot-input {
            display: flex;
            border-top: 1px solid #ccc;
        }

        #chatbot-input input {
            flex: 1;
            border: none;
            padding: 10px;
        }

        #chatbot-input button {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
        }
    </style>
    @yield('css')

</head>

<body>
    <div class="container">
        {{-- nav-bar --}}
        @include('partials.home.header_home')
        {{-- content --}}
        @yield('content')

        {{-- @include('partials.home.chatbot.chatbot') --}}
        {{-- footer --}}
        @include('partials.home.footer_home')

    </div>


    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    {{-- main js --}}
    <script src="{{ asset('js/home.js') }}"></script>
    <script src="{{ asset('js/detail.js') }}"></script>
    <script src="{{ asset('js/product.js') }}"></script>
    <script src="{{ asset('js/cart.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js">
    </script>
    @if(Session::has('success'))
    <script>
        $.toast({
            heading: 'Thông báo',
            text: "{{Session::get('success')}}",
            showHideTransition: 'slide',
            position: 'top-center',
            icon: 'success'
            })
    </script>
    @endif
    @if(Session::has('error'))
    <script>
        $.toast({
            heading: 'Thông báo',
            text: "{{Session::get('error')}}",
            showHideTransition: 'slide',
            position: 'top-center',
            icon: 'error'
            })
    </script>
    @endif
    @yield('script')
    {{-- <script>
        // Toggle hiển thị chatbot
        function toggleChatbot() {
            const chatbotBox = document.getElementById('chatbot-box');
            chatbotBox.style.display = chatbotBox.style.display === 'none' ? 'flex' : 'none';
        }
    
        // Gửi tin nhắn tới chatbot
        function sendMessage() {
            const userMessage = document.getElementById('chatbot-user-message').value;
            const messagesDiv = document.getElementById('chatbot-messages');
    
            if (!userMessage.trim()) return;
    
            // Hiển thị tin nhắn của người dùng
            const userMessageDiv = document.createElement('div');
            userMessageDiv.textContent = `Bạn: ${userMessage}`;
            messagesDiv.appendChild(userMessageDiv);
    
            // Gửi tin nhắn tới server Laravel
            fetch('/chatbot', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ message: userMessage }),
            })
            .then(response => response.json())
            .then(data => {
                // Hiển thị phản hồi từ chatbot
                const botMessageDiv = document.createElement('div');
                botMessageDiv.textContent = `AI: ${data.choices[0].message.content}`;
                messagesDiv.appendChild(botMessageDiv);
    
                // Cuộn xuống cuối
                messagesDiv.scrollTop = messagesDiv.scrollHeight;
            })
            .catch(error => {
                console.error('Lỗi:', error);
            });
    
            // Xóa nội dung input
            document.getElementById('chatbot-user-message').value = '';
        }
    </script> --}}


</body>

</html>