document.addEventListener("DOMContentLoaded", function () {
    const popupButton = document.getElementById("show-popup-btn"); 
    const popup = document.querySelector(".user-popup"); 
    const closePopupButton = document.getElementById("close-popup"); 

    popupButton.addEventListener("click", function () {
        popup.classList.add("show-popup");
    });

    closePopupButton.addEventListener("click", function () {
        popup.classList.remove("show-popup");
    });

    document.addEventListener("click", function (event) {
        if (!popup.contains(event.target) && !popupButton.contains(event.target)) {
            popup.classList.remove("show-popup");
        }
    });
});
