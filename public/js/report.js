document.addEventListener('DOMContentLoaded', () => {
    const reportBtn = document.getElementById('reportAuctionBtn');
    const reportPopup = document.getElementById('reportPopup');
    const popupOverlay = document.getElementById('popupOverlay');
    const closePopupBtn = document.getElementById('closePopupBtn');

    reportBtn.addEventListener('click', () => {
        reportPopup.style.display = 'block';
        popupOverlay.style.display = 'block';
    });

    closePopupBtn.addEventListener('click', () => {
        reportPopup.style.display = 'none';
        popupOverlay.style.display = 'none';
    });

    popupOverlay.addEventListener('click', () => {
        reportPopup.style.display = 'none';
        popupOverlay.style.display = 'none';
    });
});

document.getElementById('submitReportButton').addEventListener('click', function () {
    const reportForm = document.getElementById('reportForm');
    const formData = new FormData(reportForm);
    
    fetch("{{ route('item.report') }}", {
        method: "POST",
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        alert("Report submitted successfully");
        console.log(data);
    })
    .catch(error => {
        console.error('Error:', error);
    });
});
