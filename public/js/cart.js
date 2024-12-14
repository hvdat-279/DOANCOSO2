document.addEventListener("DOMContentLoaded", function () {
    const buttons = document.querySelectorAll(".add-to-cart");

    buttons.forEach((button) => {
        button.addEventListener("click", function () {
            const productId = this.getAttribute("data-id");
            const size = this.getAttribute("data-size");
            const quantity = this.getAttribute("data-quantity");

            fetch("/home/cart/add-cart", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
                body: JSON.stringify({
                    id: productId,
                    size: size,
                    quantity: quantity,
                }),
            })
                .then((response) => {
                    // if (!response.ok && response.status === 401) {
                    //     window.location.href = "/home/login";
                    // }
                    return response.json();
                })
                .then((data) => {
                    if (data.success) {
                        $.toast({
                            heading: "Thông báo",
                            text: data.message,
                            showHideTransition: "slide",
                            position: "top-center",
                            icon: "success",
                        });
                        const cartQuantity =
                            document.getElementById("cart-quantity");
                        if (cartQuantity) {
                            cartQuantity.textContent = data.totalQuantity;
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
                .catch((error) => {
                    $.toast({
                        heading: "Thông báo",
                        text: "Không thể thực hiện yêu cầu!",
                        showHideTransition: "slide",
                        position: "top-center",
                        icon: "error",
                    });
                    window.location.href = "/home/login";
                    console.error("Error:", error);
                });
        });
    });
});
