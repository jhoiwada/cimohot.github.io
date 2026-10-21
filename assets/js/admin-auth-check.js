document.addEventListener("DOMContentLoaded", async () => {
  try {
    const response = await fetch("/check_session.php");
    const user = await response.json();
    if (!user.loggedIn || user.user.role !== "admin") {
      alert("Akses hanya untuk admin. Silakan login.");
      window.location.href = "//login";
    }
  } catch (error) {
    alert("Error memeriksa sesi. Silakan login kembali.");
    window.location.href = "//login";
  }
});
