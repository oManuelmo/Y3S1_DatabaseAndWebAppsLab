document.addEventListener("DOMContentLoaded", () => {
    console.log("Script loaded");
    const deleteButtons = document.querySelectorAll("button.button-danger");
    const popupOverlay = document.createElement("div");

    let targetForm = null;

    popupOverlay.classList.add("confirm-popup-overlay");
    popupOverlay.innerHTML = `
        <div class="confirm-popup">
            <h2>Are you sure?</h2>
            <p>Deleting your account is irreversible. This action cannot be undone.</p>
            <div class="confirm-popup-buttons">
                <button class="btn-confirm">Delete</button>
                <button class="btn-cancel">Cancel</button>
            </div>
        </div>
    `;
    document.body.appendChild(popupOverlay);

    deleteButtons.forEach(button => {
        button.addEventListener("click", (e) => {
            e.preventDefault(); 
            targetForm = button.closest("form");
            popupOverlay.classList.add("show");
        });
    });

    popupOverlay.addEventListener("click", (e) => {
        if (e.target.classList.contains("btn-confirm")) {
            if (targetForm) {
                targetForm.submit();
            } else {
                console.error("No form found for this delete action!");
            }
        }
        if (e.target.classList.contains("btn-cancel") || e.target === popupOverlay) {
            popupOverlay.classList.remove("show");
        }
    });
});
