"use strict";

function protectPage() {
  if (!window.currentUser) {
    const protectedElements = document.querySelectorAll(".requires-login");

    protectedElements.forEach((element) => {
      element.addEventListener(
        "click",
        (e) => {
          e.preventDefault();
          e.stopPropagation();
          Swal.fire({
            icon: "warning",
            title: "Login Diperlukan",
            text: "Silakan login terlebih dahulu untuk melanjutkan.",
            confirmButtonText: "Login Sekarang",
            confirmButtonColor: "#ff6b35",
          }).then((result) => {
            if (result.isConfirmed) {
              window.location.href = "/login";
            }
          });
        },
        true
      );
    });
  }
}

document.addEventListener("userStatusChecked", protectPage);
