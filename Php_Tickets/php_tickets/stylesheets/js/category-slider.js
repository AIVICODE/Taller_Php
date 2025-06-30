/**
 * Controlador para el slider de categorías
 * Maneja el desplazamiento horizontal suave y la navegación entre categorías
 */

document.addEventListener('DOMContentLoaded', function() {
    // Elementos principales
    const categoryEvents = document.querySelectorAll('.category-events');
    const categoryItems = document.querySelectorAll('.category-item');
    const categoriesContainer = document.getElementById('categoriesContainer');
    const btnScrollLeft = document.getElementById('scrollLeft');
    const btnScrollRight = document.getElementById('scrollRight');
    
    // Verificar si existen los elementos necesarios
    if (!categoriesContainer || !categoryEvents.length) return;
    
    // Ocultar todas las categorías excepto la primera
    for (let i = 1; i < categoryEvents.length; i++) {
        categoryEvents[i].style.display = 'none';
    }
    
    // Marcar la primera categoría como activa
    if (categoryItems.length > 0) {
        categoryItems[0].classList.add('active-category');
    }
    
    // Variables para el scroll
    const scrollAmount = 300; // píxeles a desplazar por clic
    let isAnimating = false; // para evitar múltiples clics durante la animación
    
    /**
     * Función para animación de scroll suave personalizado
     * @param {HTMLElement} element - Elemento a desplazar
     * @param {Number} target - Posición objetivo en píxeles
     * @param {Number} duration - Duración de la animación en ms
     */
    function smoothScroll(element, target, duration) {
        if (isAnimating) return;
        isAnimating = true;
        
        const start = element.scrollLeft;
        const startTime = performance.now();
        const distance = target - start;
        
        // Función de animación con efecto ease-in-out
        function easeInOutCubic(t) {
            return t < 0.5 
                ? 4 * t * t * t 
                : 1 - Math.pow(-2 * t + 2, 3) / 2;
        }
        
        function animate(currentTime) {
            const elapsedTime = currentTime - startTime;
            const progress = Math.min(elapsedTime / duration, 1);
            const easeProgress = easeInOutCubic(progress);
            
            element.scrollLeft = start + distance * easeProgress;
            
            if (progress < 1) {
                requestAnimationFrame(animate);
            } else {
                isAnimating = false;
                updateScrollButtons();
            }
        }
        
        requestAnimationFrame(animate);
    }
    
    // Función para desplazarse a la izquierda
    if (btnScrollLeft) {
        btnScrollLeft.addEventListener('click', function() {
            const targetPosition = categoriesContainer.scrollLeft - scrollAmount;
            smoothScroll(categoriesContainer, targetPosition, 600);
        });
    }
    
    // Función para desplazarse a la derecha
    if (btnScrollRight) {
        btnScrollRight.addEventListener('click', function() {
            const targetPosition = categoriesContainer.scrollLeft + scrollAmount;
            smoothScroll(categoriesContainer, targetPosition, 600);
        });
    }
    
    /**
     * Función para actualizar el estado de los botones (habilitados/deshabilitados)
     */
    function updateScrollButtons() {
        if (!btnScrollLeft || !btnScrollRight) return;
        
        // Comprobar si hay scroll hacia la izquierda disponible
        btnScrollLeft.disabled = categoriesContainer.scrollLeft <= 0;
        
        // Comprobar si hay scroll hacia la derecha disponible
        const maxScrollLeft = categoriesContainer.scrollWidth - categoriesContainer.clientWidth;
        btnScrollRight.disabled = categoriesContainer.scrollLeft >= maxScrollLeft;
    }
    
    /**
     * Crear indicadores visuales de scroll (puntos)
     */
    function createScrollIndicators() {
        const container = document.getElementById('scrollIndicator');
        if (!container) return;
        
        // Calcular cuántos puntos necesitamos (uno por cada página completa)
        const containerWidth = categoriesContainer.clientWidth;
        const totalContentWidth = categoriesContainer.scrollWidth;
        const numPages = Math.ceil(totalContentWidth / containerWidth);
        
        // Limitar el número de puntos para evitar demasiados
        const maxDots = 8;
        const numDots = Math.min(numPages, maxDots);
        
        // Crear los puntos
        container.innerHTML = '';
        for (let i = 0; i < numDots; i++) {
            const dot = document.createElement('div');
            dot.className = 'scroll-dot' + (i === 0 ? ' active' : '');
            dot.dataset.position = i;
            
            // Al hacer clic en un punto, desplazamos a esa sección
            dot.addEventListener('click', function() {
                const position = i / (numDots - 1); // De 0 a 1
                const targetScroll = position * (totalContentWidth - containerWidth);
                smoothScroll(categoriesContainer, targetScroll, 800);
            });
            
            container.appendChild(dot);
        }
    }
    
    /**
     * Actualizar el indicador visual según la posición de scroll
     */
    function updateScrollIndicator() {
        const dots = document.querySelectorAll('.scroll-dot');
        if (!dots.length) return;
        
        const containerWidth = categoriesContainer.clientWidth;
        const totalContentWidth = categoriesContainer.scrollWidth;
        const maxScroll = totalContentWidth - containerWidth;
        if (maxScroll <= 0) return; // No hay suficiente contenido para scroll
        
        const currentPosition = categoriesContainer.scrollLeft / maxScroll; // De 0 a 1
        const numDots = dots.length;
        const activeDotIndex = Math.min(Math.floor(currentPosition * numDots), numDots - 1);
        
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === activeDotIndex);
        });
    }
    
    // Inicializar indicadores
    createScrollIndicators();
    
    // Verificar inicialmente la visibilidad de los botones
    updateScrollButtons();
    
    // Actualizar indicadores durante el scroll
    categoriesContainer.addEventListener('scroll', function() {
        updateScrollIndicator();
        updateScrollButtons();
    });
    
    // Actualizar botones e indicadores al redimensionar la ventana
    window.addEventListener('resize', function() {
        updateScrollButtons();
        createScrollIndicators();
        updateScrollIndicator();
    });
    
    // Soporte para navegación con teclado (teclas de flecha izquierda/derecha)
    document.addEventListener('keydown', function(e) {
        // Solo si estamos enfocados en la sección de categorías o sus hijos
        if (document.activeElement === categoriesContainer || 
            categoriesContainer.contains(document.activeElement)) {
            if (e.key === 'ArrowLeft' && btnScrollLeft && !btnScrollLeft.disabled) {
                btnScrollLeft.click();
                e.preventDefault();
            } else if (e.key === 'ArrowRight' && btnScrollRight && !btnScrollRight.disabled) {
                btnScrollRight.click();
                e.preventDefault();
            }
        }
    });
});

/**
 * Cambiar la categoría activa y mostrar su contenido
 * @param {String} categoryId - ID de la categoría a mostrar
 */
function toggleCategory(categoryId) {
    const categoryEvents = document.querySelectorAll('.category-events');
    const categoryItems = document.querySelectorAll('.category-item');
    const categoriesContainer = document.getElementById('categoriesContainer');
    
    if (!categoryEvents.length || !categoryItems.length || !categoriesContainer) return;
    
    // Mostrar/ocultar categorías
    categoryEvents.forEach(cat => {
        if (cat.id === categoryId) {
            cat.style.display = 'block';
            // Scroll suave a la sección de eventos
            cat.scrollIntoView({ behavior: 'smooth' });
        } else {
            cat.style.display = 'none';
        }
    });
    
    // Actualizar la clase activa y asegurar que la categoría seleccionada es visible
    categoryItems.forEach((item) => {
        if (item.getAttribute('onclick') && item.getAttribute('onclick').includes(categoryId)) {
            item.classList.add('active-category');
            
            // Asegurar que el elemento activo es visible en el contenedor de categorías
            const itemLeft = item.offsetLeft;
            const itemWidth = item.offsetWidth;
            const containerWidth = categoriesContainer.clientWidth;
            const currentScroll = categoriesContainer.scrollLeft;
            
            // Calcular la posición óptima para centrar el elemento
            const idealPosition = itemLeft - (containerWidth / 2) + (itemWidth / 2);
            const targetPosition = Math.max(0, Math.min(idealPosition, 
                categoriesContainer.scrollWidth - containerWidth));
            
            // Verificar si la función de desplazamiento suave está disponible
            if (typeof window.smoothScroll === 'function') {
                window.smoothScroll(categoriesContainer, targetPosition, 800);
            } else {
                // Usar el método de scroll nativo como fallback
                categoriesContainer.scrollTo({
                    left: targetPosition,
                    behavior: 'smooth'
                });
            }
        } else {
            item.classList.remove('active-category');
        }
    });
}
