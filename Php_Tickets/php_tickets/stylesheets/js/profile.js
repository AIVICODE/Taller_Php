/**
 * Script para manejar la carga y visualización previa de imágenes de perfil
 * con mejoras en la experiencia del usuario
 */
document.addEventListener('DOMContentLoaded', function() {
    // Obtener elementos
    const fileInput = document.getElementById('profile_image');
    const uploadButton = document.getElementById('upload-button');
    const profileImageContainer = document.querySelector('.profile-image-container');
    const profileForm = document.getElementById('profile-form');
    
    // Si existen los elementos necesarios
    if (fileInput && uploadButton) {
        // Escuchar cambios en el input de archivo
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                
                // Validar el tipo de archivo
                const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (!validTypes.includes(file.type)) {
                    alert('Formato no válido. Usa JPG, PNG, GIF o WebP');
                    return;
                }
                
                // Validar el tamaño (máximo 2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('Imagen demasiado grande (máx. 2MB)');
                    return;
                }
                
                // Mostrar el botón de guardar
                uploadButton.style.display = 'inline-block';
                
                // Vista previa de la imagen
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Actualizar la vista previa de la imagen
                    const profileImage = document.getElementById('profile-image-preview');
                    if (profileImage) {
                        profileImage.src = e.target.result;
                        
                        // Animación suave de cambio
                        profileImage.style.opacity = '0.8';
                        setTimeout(() => {
                            profileImage.style.opacity = '1';
                        }, 300);
                    }
                };                reader.readAsDataURL(file);
            } else {
                uploadButton.style.display = 'none';
            }
        });
        
        // También permitir hacer clic en la imagen para seleccionar un archivo
        if (profileImageContainer) {
            profileImageContainer.addEventListener('click', function() {
                fileInput.click();
            });
        }
        
        // Mostrar mensaje de carga durante el envío del formulario
        if (profileForm) {
            profileForm.addEventListener('submit', function() {
                uploadButton.disabled = true;
                uploadButton.textContent = 'Subiendo...';
                uploadButton.style.opacity = '0.7';
            });
        }
    }
});
