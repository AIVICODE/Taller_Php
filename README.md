# 🎫 Ticketera PHP - Sistema de Gestión de Eventos y Tickets

Una aplicación web completa desarrollada en PHP para la gestión y venta de tickets de eventos. Permite a organizadores crear eventos y a clientes comprar tickets con generación automática de PDFs.

## 📋 Tabla de Contenidos

- [Características Principales](#características-principales)
- [Tecnologías Utilizadas](#tecnologías-utilizadas)
- [Flujo de la Aplicación](#flujo-de-la-aplicación)
- [Generación de PDFs](#generación-de-pdfs)
- [Instalación](#instalación)
- [Uso](#uso)
- [API Reference](#api-reference)

[#Prototipado Figma](https://www.figma.com/proto/AHn70jh8VVLNr8RYx8TWfg/Ticketed-Web-Design--Community-?node-id=68-707&t=kFhrivLGaQw2sTs3-1)

## 🚀 Características Principales

### Para Visitantes (Sin Cuenta)
- ✅ Acceso completo al dashboard
- ✅ Navegación por todos los eventos disponibles
- ✅ Búsqueda de eventos por título o categoría
- ✅ Visualización de detalles de eventos
- ✅ Solicitud de registro/login para comprar tickets

### Para Organizadores
- ✅ Registro e inicio de sesión
- ✅ Creación y gestión de eventos
- ✅ Subida de imágenes para eventos
- ✅ Categorización de eventos
- ✅ Gestión de cupos y precios

### Para Clientes
- ✅ Registro e inicio de sesión
- ✅ Todas las funcionalidades de visitante
- ✅ Compra de tickets
- ✅ Visualización de tickets comprados
- ✅ Mail de confirmación de compra
- ✅ **Generación automática de tickets en PDF**
- ✅ Perfil personalizable con foto

- ### Para Administrador
- ✅ Inicio de sesión
- ✅ Aprobar organizadores
- ✅ Monitoreo general de la app

### Características Generales
- ✅ Dashboard interactivo con categorías
- ✅ Sistema de autenticación basado en sesiones
- ✅ Gestión de pagos (simulado)
- ✅ Interfaz responsive y moderna
- ✅ Validación de formularios
- ✅ Subida segura de archivos

## 💻 Tecnologías Utilizadas

### Backend
- **PHP** - Lenguaje principal del servidor
- **MySQL** - Base de datos relacional
- **FPDF** - Librería para generación de PDFs

### Frontend
- **HTML5** - Estructura de páginas
- **CSS3** - Estilos y diseño responsive
- **JavaScript** - Interactividad del frontend
- **Bootstrap Icons** - Iconografía

### Herramientas
- **XAMPP** - Entorno de desarrollo local
- **phpMyAdmin** - Administración de base de datos

## 🔄 Flujo de la Aplicación

### Diagrama de Flujo Principal

![Editor _ Mermaid Chart-2025-06-22-221031](https://github.com/user-attachments/assets/00dfc2bf-21ff-4e78-96e4-fe8c34936141)

## 📄 Generación de PDFs

### Características del Sistema PDF

El sistema utiliza la librería **FPDF** para generar tickets en formato PDF con las siguientes características:

#### ✨ Elementos del Ticket PDF

1. **Header personalizado**
   - Logo/título de la aplicación
   - Numeración de páginas

2. **Información del evento**
   - Título del evento
   - Imagen del evento (si está disponible)
   - Fecha y hora
   - Lugar
   - Descripción

3. **Datos del comprador**
   - Nombre completo
   - Email de contacto

4. **Detalles del ticket**
   - Número único de ticket
   - Cantidad de entradas
   - Fecha de compra
   - Método de pago utilizado
   - Estado del pago
   - Total pagado

5. **Código de validación**
   - Código único generado: `TKT-[HASH]`
   - Representación gráfica (simulación QR)
   - Instrucciones de uso

**Características del PDF:**
- ✅ Formato A4 estándar
- ✅ Codificación UTF-8 para caracteres especiales
- ✅ Imágenes responsive
- ✅ Footer automático con numeración
- ✅ Diseño profesional y limpio
- ✅ Validación de archivos de imagen
- ✅ Manejo de errores en generación

#### 📁 Archivos Relacionados

- `pdf/generar_pdf.php` - Generador principal de PDFs
- `fpdf/fpdf.php` - Librería FPDF
- `controller/generar_pdf_controller.php` - Control de acceso
- `view/mis_tickets.php` - Interface para descargar PDFs

## 🚀 Instalación

### Requisitos Previos
- XAMPP (Apache + MySQL + PHP)
- PHP 7.4 o superior
- MySQL 5.7 o superior

### Pasos de Instalación

1. **Clonar el repositorio**
   ```bash
   git clone [URL_REPOSITORIO]
   cd Php_Tickets
   ```

2. **Configurar XAMPP**
   - Iniciar Apache y MySQL
   - Acceder a phpMyAdmin

3. **Crear base de datos**
   ```sql
   -- Ejecutar el archivo Tablas.sql en phpMyAdmin
   source Tablas.sql
   ```

4. **Configurar conexión**
   ```php
   // Verificar conection/sql.php
   $host = "localhost";
   $user = "root";
   $password = "";
   $database = "ticketera";
   ```

5. **Configurar permisos**
   ```bash
   chmod 755 uploads/
   chmod 755 uploads/eventos/
   chmod 755 uploads/profile_images/
   ```

6. **Acceder a la aplicación**
   ```
   http://localhost/Php_Tickets/view/dashboard.php
   ```

## 📖 Uso

### Para Visitantes (Sin Registro)

1. **Explorar sin cuenta**
   - Acceder directamente a dashboard.php
   - Navegar por todas las categorías de eventos
   - Usar la búsqueda de eventos
   - Ver detalles completos de los eventos

2. **Acceso a funcionalidades**
   - Para comprar tickets: se solicita login/registro
   - Para crear eventos: se solicita login como organizador

### Para Organizadores

1. **Registro como Organizador**
   - Ir a registro.php
   - Seleccionar "Organizador"
   - Completar datos

2. **Crear Evento**
   - Login → Dashboard
   - Clic en "Crear nuevo evento"
   - Completar formulario
   - Subir imagen del evento

### Para Clientes

1. **Registro como Cliente**
   - Ir a registro.php
   - Seleccionar "Cliente"
   - Completar datos

2. **Comprar Tickets**
   - Explorar eventos en Dashboard
   - Clic en "Comprar tickets"
   - Seleccionar cantidad y método de pago
   - Procesar compra

3. **Descargar PDF**
   - Ir a "Mis tickets"
   - Clic en "Descargar PDF"

## 🔗 API Reference

### Principales Controladores

#### Authentication
- `login_controller.php` - Manejo de autenticación
- `registro_controller.php` - Registro de usuarios
- `logout_controller.php` - Cierre de sesión

#### Events Management
- `crear_evento_controller.php` - Creación de eventos
- `procesar_evento_controller.php` - Procesamiento de eventos
- `dashboard_controller.php` - Datos del dashboard

#### Tickets & Payments
- `comprar_ticket_controller.php` - Compra de tickets
- `generar_pdf_controller.php` - Generación de PDFs
- `ver_tickets_controller.php` - Visualización de tickets

#### User Profile
- `perfil_cliente_controller.php` - Gestión de perfil
- `upload_profile_image.php` - Subida de imágenes

### Principales Modelos

- `Usuario.php` - Gestión de usuarios base
- `Cliente.php` - Funcionalidades específicas de clientes
- `Organizador.php` - Funcionalidades de organizadores
- `Evento.php` - Gestión de eventos
- `Ticket.php` - Gestión de tickets
- `Pago.php` - Procesamiento de pagos
- `Categoria.php` - Categorías de eventos

---

## 📝 Notas Técnicas

### Seguridad Implementada
- ✅ Validación de sesiones en todas las páginas protegidas
- ✅ Verificación de propiedad de tickets antes de generar PDF
- ✅ Sanitización de inputs del usuario
- ✅ Validación de tipos de archivo en uploads
- ✅ Protección contra inyección SQL básica

### Mejoras Futuras Sugeridas
- 🔄 Implementar JWT para autenticación
- 🔄 Integrar pasarelas de pago reales
- 🔄 Añadir códigos QR reales
- 🔄 Implementar sistema de reseñas
- 🔄 Añadir panel de administración
---

**Desarrollado con ❤️ usando PHP**

*Última actualización: Junio 2025*
