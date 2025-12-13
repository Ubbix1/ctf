
document.addEventListener("DOMContentLoaded", function() {
    let btn = document.getElementById("toggle");
let mobile = document.getElementById("showNav");

// Toggle mobile navigation on button click
btn.addEventListener("click", (e) => {
    e.preventDefault();
    toggleMobileNav();
});

// Hide mobile navigation when clicking outside the box
document.addEventListener("click", (e) => {
    const isClickInsideNav = mobile.contains(e.target);
    const isClickOnBtn = e.target === btn;

    if (!isClickInsideNav && !isClickOnBtn) {
        mobile.style.display = "none";
    }
});

// Function to toggle mobile navigation display
function toggleMobileNav() {
    if (mobile.style.display === "none" || mobile.style.display === "") {
        mobile.style.display = "flex";
    } else {
        mobile.style.display = "none";
    }
}
});
