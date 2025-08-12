document.addEventListener('DOMContentLoaded', function () {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });


    const sidebar = document.getElementById('notificationsSidebar');
    const toggleBtn = document.getElementById('toggleSidebar');
    const closeBtn = document.getElementById('closeSidebar');
    const contentDiv = document.getElementById('notificationsContent');

    const userId = toggleBtn.getAttribute('data-user-id');
    
    toggleBtn.addEventListener('click', () => {
        sidebar.classList.add('open');
        
        if (!contentDiv.innerHTML.trim()) {
            fetch(`/notifications/${userId}`)  
                .then(response => response.text())
                .then(html => {
                    contentDiv.innerHTML = html;
                })
                .catch(error => {
                    contentDiv.innerHTML = '<p>Erro ao carregar as notificações.</p>';
                    console.error(error);
                });
        }
    });

    
    closeBtn.addEventListener('click', () => {
        sidebar.classList.remove('open');
    });


    const pusher = new Pusher('eedefc1ec5f312a69b3a', {
        cluster: 'eu',
        encrypted: true,
    });


    const userChannel = pusher.subscribe('user.'+ userId); 
    userChannel.bind('item-notification', function(data) {
        console.log(`New notification: ${data.message}`);

        const notificationType = (data.type === 'canceled' || data.type === 'suspended' || data.type === 'canceledowner' || data.type === 'suspendedowner') ? 'error' : 'success';
        showNotificationPopUp(data.message, notificationType);
    })

});


function showNotificationPopUp(message, type= 'success'){
    const container = document.getElementById('notification-container');
    const notification = document.createElement('div');
    
    notification.className = `notification ${type}`;
    notification.textContent = message;

    container.appendChild(notification);

    setTimeout(() =>{
        notification.classList.add('show');
    },50);
    
    setTimeout(() =>{
        notification.classList.add('hide');
        notification.addEventListener('transitionend', () => {
            notification.remove();
        });
    },7000);
}