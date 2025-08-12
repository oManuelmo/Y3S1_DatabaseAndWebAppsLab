document.addEventListener('DOMContentLoaded', function () {
    const updatePictureRoute = document.getElementById('updatePictureRoute').getAttribute('data-route');
    const profilePictureInput = document.getElementById('profile_picture_input');
    const imageCropModal = document.getElementById('imageCropModal');
    const cropImage = document.getElementById('cropImage');
    const cropButton = document.getElementById('cropButton');
    const closeCropModal = document.getElementById('closeCropModal');
    const profileImagePreview = document.getElementById('profileImagePreview');
    const allProfileImagePreviews = document.querySelectorAll('img[id="profileImagePreview"]'); 
    let cropper;

    function showCropModal() {
        imageCropModal.style.display = 'flex';
    }

    function closeCropModalFunction() {
        imageCropModal.style.display = 'none';
        if (cropper) {
            cropper.destroy();
            cropper = null; 
        }
    }

    if (closeCropModal) {
        closeCropModal.addEventListener('click', closeCropModalFunction);
    }

    if (profilePictureInput) {
        profilePictureInput.addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    cropImage.src = e.target.result;

                    if (cropper) {
                        cropper.destroy();
                        cropper = null;
                    }

                    imageCropModal.style.display = 'flex';
                    cropImage.style.display = 'block';

                    cropper = new Cropper(cropImage, {
                        aspectRatio: 1,
                        viewMode: 0,
                        background: false,
                        guides: false,
                        dragMode: 'move',
                        rotatable: true,
                        zoomable: true,
                        ready() {
                            const cropperOverlay = document.querySelector('.cropper-container .cropper-view-box');
                            if (cropperOverlay) {
                                cropperOverlay.classList.add('circle-overlay');
                            }
                        },
                    });
                };
                reader.readAsDataURL(file);
            }
        });
    }

    if (cropButton) {
        cropButton.addEventListener('click', function () {
            if (cropper) {
                const canvas = cropper.getCroppedCanvas();
                canvas.toBlob(function (blob) {
                    const formData = new FormData();
                    formData.append('profile_picture', blob);

                    fetch(updatePictureRoute, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                        body: formData,
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP status ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            console.log('Image updated successfully:', data.newImageUrl);

                            const timestamp = new Date().getTime();
                            allProfileImagePreviews.forEach(img => {
                                img.src = `${data.newImageUrl}?t=${timestamp}`;
                            });

                            closeCropModalFunction(); 
                        } else {
                            console.error('Server error:', data.message);
                            alert('Upload failed: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred: ' + error.message);
                    });
                }, 'image/jpeg');
            }
        });
    }
});


document.addEventListener('DOMContentLoaded', function () {
    const stars = document.querySelectorAll('.stars .star');
    const rateInput = document.getElementById('rate');

    stars.forEach((star) => {
        star.addEventListener('mouseover', function () {
            const value = parseFloat(this.getAttribute('data-value'));
            stars.forEach((s, index) => {
                if (index < value) {
                    s.classList.add('filled');
                    s.classList.remove('half');
                } else if (index + 1 === Math.ceil(value) && value % 1 >= 0.5) {
                    s.classList.add('half');
                } else {
                    s.classList.remove('filled');
                    s.classList.remove('half');
                }
            });
        });

        star.addEventListener('mouseout', function () {
            const currentValue = parseFloat(rateInput.value) || 0;
            stars.forEach((s, index) => {
                if (index < currentValue) {
                    s.classList.add('filled');
                    s.classList.remove('half');
                } else if (index + 1 === Math.ceil(currentValue) && currentValue % 1 >= 0.5) {
                    s.classList.add('half');
                } else {
                    s.classList.remove('filled');
                    s.classList.remove('half');
                }
            });
        });

        star.addEventListener('click', function () {
            const value = parseFloat(this.getAttribute('data-value'));
            rateInput.value = value;
            stars.forEach((s, index) => {
                if (index < value) {
                    s.classList.add('filled');
                    s.classList.remove('half');
                } else if (index + 1 === Math.ceil(value) && value % 1 >= 0.5) {
                    s.classList.add('half');
                } else {
                    s.classList.remove('filled');
                    s.classList.remove('half');
                }
            });
        });
    });
});


