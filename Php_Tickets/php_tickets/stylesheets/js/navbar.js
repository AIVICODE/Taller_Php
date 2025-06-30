// Script para controlar el menú hamburguesa
document.addEventListener('DOMContentLoaded', function() {
    const hamburgerButton = document.querySelector('.hamburger-menu');
    const actionsMenu = document.querySelector('.actions');

    if (hamburgerButton && actionsMenu) {
        hamburgerButton.addEventListener('click', function(e) {
            // Evitar propagación del evento
            e.stopPropagation();
            
            // Toggle menu display
            actionsMenu.style.display = actionsMenu.style.display === 'flex' ? 'none' : 'flex';
            
            // Solo añadir el listener si el menú está abierto
            if (actionsMenu.style.display === 'flex') {
                // Pequeño retraso para evitar conflictos con el evento actual
                setTimeout(() => {
                    document.addEventListener('click', closeMenuOnClickOutside);
                }, 10);
            }
        });

        // Function to close menu when clicking outside
        function closeMenuOnClickOutside(event) {
            if (!actionsMenu.contains(event.target) && !hamburgerButton.contains(event.target)) {
                actionsMenu.style.display = 'none';
                document.removeEventListener('click', closeMenuOnClickOutside);
            }
        }
    }
});
