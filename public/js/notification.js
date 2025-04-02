document.addEventListener('DOMContentLoaded', () => {
    const notification = document.getElementById('notification-success');
    if (notification) {
        setTimeout(() => {
            notification.classList.add('hide');
        }, 5000);
    }
});