document.addEventListener("DOMContentLoaded", function () {
    const imageInputs = document.querySelectorAll('.item-image-input');
    const imageCropModal = document.getElementById('imageCropModal');
    const cropImage = document.getElementById('cropImage');
    const cropButton = document.getElementById('cropButton');
    const closeCropModal = document.getElementById('closeCropModal');
    let cropper;

    imageInputs.forEach((input, index) => {
        input.addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (file) {
                const maxSizeInMB = 2;
                if (file.size > maxSizeInMB * 1024 * 1024) {
                    alert(`The file size exceeds ${maxSizeInMB} MB. Please choose a smaller file.`);
                    event.target.value = ''; 
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (e) {
                    cropImage.src = e.target.result;
                    imageCropModal.style.display = 'flex';

                    if (cropper) {
                        cropper.destroy();
                        cropper = null;
                    }

                    cropper = new Cropper(cropImage, {
                        aspectRatio: NaN,
                        viewMode: 0,
                        background: false,
                        guides: true,
                        dragMode: 'move',
                        rotatable: true,
                        zoomable: true,
                    });
                };
                reader.readAsDataURL(file);

                
                cropButton.setAttribute('data-index', index + 1); 
            }
        });
    });

    cropButton.addEventListener('click', function () {
        const index = parseInt(cropButton.getAttribute('data-index'), 10);

        console.log(index +1)

        if (cropper) {
            const canvas = cropper.getCroppedCanvas();
            const croppedImage = canvas.toDataURL();
            const croppedFile = dataURLtoFile(croppedImage, `cropped_image_${index + 1}.png`);

            const fileInput = document.querySelector(`#image${index + 1}`);
            if (!fileInput) {
                console.error(`File input with ID image${index + 1} not found!`);
                return; 
            }

            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(croppedFile);
            fileInput.files = dataTransfer.files;

            const previewContainer = document.querySelector(`#preview${index + 1}`);
            if (previewContainer) {
                previewContainer.innerHTML = `
                    <img src="${croppedImage}" alt="Image Preview" class="preview-image">
                    <span id="remove-button-${index + 1 }" class="delete-icon" onclick="removeNewImage(${index + 1 })">Delete</span>
                `;
            }

            closeModal();
        } else {
            console.error('Cropper instance not found!');
        }
    });

    function dataURLtoFile(dataURL, filename) {
        const arr = dataURL.split(',');
        const mime = arr[0].match(/:(.*?);/)[1];
        const bstr = atob(arr[1]);
        let n = bstr.length;
        const u8arr = new Uint8Array(n);
        while (n--) {
            u8arr[n] = bstr.charCodeAt(n);
        }
        return new File([u8arr], filename, { type: mime });
    }

    closeCropModal.addEventListener('click', function () {
        console.log('Cancel button clicked');
        closeModal();
    });

    function closeModal() {
        console.log('Closing modal...');
        if (imageCropModal) {
            imageCropModal.style.display = 'none'; 
            console.log('Modal hidden');
        } else {
            console.error('Modal element not found');
        }

        if (cropper) {
            cropper.destroy();
            cropper = null;
            console.log('Cropper instance destroyed');
        } else {
            console.error('No cropper instance to destroy');
        }
        cropImage.src = ''; 
    }

    window.removeNewImage = function(slot) {
        const fileInput = document.getElementById(`image${slot}`);
        const imageUploadContainer = document.getElementById(`image-upload-${slot}`);
        const removeButtonContainer = document.getElementById(`remove-button-${slot}`);

        console.log(slot)

        fileInput.value = '';

        imageUploadContainer.innerHTML = `
            <input id="image${slot}" type="file" name="images[]" accept="image/*" class="file-input item-image-input" data-index="${slot}">
            <div id="preview${slot}" class="image-preview">
                <label id="icon${slot}" for="image${slot}" class="upload-icon">add_photo_alternate</label>
            </div>`;

        removeButtonContainer.innerHTML = '';

        const newFileInput = document.getElementById(`image${slot}`);
        newFileInput.addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (file) {
                const maxSizeInMB = 2;
                if (file.size > maxSizeInMB * 1024 * 1024) {
                    alert(`The file size exceeds ${maxSizeInMB} MB. Please choose a smaller file.`);
                    event.target.value = ''; 
                    return;
                }                
                const reader = new FileReader();
                reader.onload = function (e) {
                    cropImage.src = e.target.result;
                    imageCropModal.style.display = 'flex'; 

                    if (cropper) {
                        cropper.destroy();
                        cropper = null;
                    }

                    cropper = new Cropper(cropImage, {
                        aspectRatio: NaN,
                        viewMode: 0,
                        background: false,
                        guides: true,
                        dragMode: 'move',
                        rotatable: true,
                        zoomable: true,
                    });
                };
                reader.readAsDataURL(file);

                cropButton.setAttribute('data-index', slot - 1); 
            }
        });
    };

    window.handleExistingImageRemove = function(slot) {
        const fileInput = document.getElementById(`image${slot}`);
        const imageUploadContainer = document.getElementById(`image-upload-${slot}`);
        const removeButtonContainer = document.getElementById(`remove-button-${slot}`);

        imageUploadContainer.innerHTML = `
            <input id="image${slot}" type="file" name="images[]" accept="image/*" class="file-input item-image-input" data-index="${slot}">
            <div id="preview${slot}" class="image-preview">
                <label id="icon${slot}" for="image${slot}" class="upload-icon">add_photo_alternate</label>
            </div>`;

        removeButtonContainer.innerHTML = '';

        const newFileInput = document.getElementById(`image${slot}`);
        newFileInput.addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (file) {
                const maxSizeInMB = 2;
                if (file.size > maxSizeInMB * 1024 * 1024) {
                    alert(`The file size exceeds ${maxSizeInMB} MB. Please choose a smaller file.`);
                    event.target.value = ''; 
                    return;
                }                
                const reader = new FileReader();
                reader.onload = function (e) {
                    cropImage.src = e.target.result;
                    imageCropModal.style.display = 'flex'; 

                    if (cropper) {
                        cropper.destroy();
                        cropper = null;
                    }

                    cropper = new Cropper(cropImage, {
                        aspectRatio: NaN,
                        viewMode: 0,
                        background: false,
                        guides: true,
                        dragMode: 'move',
                        rotatable: true,
                        zoomable: true,
                    });
                };
                reader.readAsDataURL(file);

                cropButton.setAttribute('data-index', slot); 
            }
        });
    }
});


function toggleNewArtistInput() {
    const artistSelect = document.getElementById('artist');
    const newArtistInput = document.getElementById('new_artist_name');
    const orLabel = document.querySelector('label[for="new_artist_name"]');

    if (artistSelect.value == 1) { 
        newArtistInput.style.display = 'block';
        orLabel.style.display = 'block';
    } else {
        newArtistInput.style.display = 'none';
        orLabel.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', toggleNewArtistInput);