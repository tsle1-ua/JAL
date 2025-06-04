# Spandam - Plataforma Integral para Universitarios

Spandam es una plataforma web completa desarrollada en Laravel 11 que satisface las diversas necesidades de los estudiantes universitarios, combinando funcionalidades de alojamiento, conexión entre compañeros de piso, acceso a información académica y descubrimiento de ocio y eventos locales.

## 🚀 Características Principales

### 🏠 Módulo de Alojamientos
- Publicación y búsqueda de alojamientos (tipo Idealista)
- Filtros avanzados por precio, ubicación, tipo de propiedad
- Gestión de imágenes múltiples
- Búsqueda por proximidad geográfica
- Sistema de favoritos
- Pagos mensuales automáticos para alquiler mediante domiciliación

### 🎓 Información Académica
- Consulta de notas de corte por universidad y carrera
- Base de datos de becas y ayudas estudiantiles
- Sistema de preferencias académicas personalizadas
- Notificaciones de deadlines importantes

### 👥 RoomieMatch (Sistema de Emparejamiento)
- Algoritmo de compatibilidad basado en preferencias de vida
- Sistema de "swipe" estilo Tinder
- Matches mutuos entre estudiantes
- Análisis detallado de compatibilidad

### 🎉 Ocio y Eventos
- Creación y gestión de eventos universitarios
- Integración con Google Maps
- Filtros por categoría, fecha y ubicación
- Sistema de registro de asistencia
- **NUEVO**: Zonas de ocio filtradas por universidad y ciudad, con posibilidad de añadir nuevas zonas, valoraciones y opiniones.

## 🛠️ Tecnologías Utilizadas

- **Backend**: Laravel 11 (PHP 8.2+)
- **Frontend**: Bootstrap 5, Blade Templates
- **Base de Datos**: MySQL
- **Autenticación**: Laravel UI + Bootstrap
- **Mapas**: Google Maps Embed API
- **Arquitectura**: MVC con capas de Servicio y Repositorio

## 📋 Requisitos del Sistema

- PHP >= 8.2
- Composer
- Node.js & npm
- MySQL >= 8.0
- Git

## 🔧 Instalación

### 1. Clonar el repositorio
```bash
git clone https://github.com/tu-usuario/spandam.git
cd spandam
```

### 2. Instalar dependencias de PHP
```bash
composer install
```

### 3. Instalar dependencias de Node.js
```bash
npm install
```

### 4. Configurar el entorno
```bash
cp .env.example .env
php artisan key:generate
```

### 5. Configurar la base de datos
Edita el archivo `.env` con tus credenciales de MySQL:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=spandam_db
DB_USERNAME=root
DB_PASSWORD=tu_password
DB_COLLATION=utf8mb4_unicode_ci
```

### 6. Crear la base de datos
```sql
CREATE DATABASE spandam_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 7. Ejecutar migraciones y seeders
```bash
php artisan migrate
php artisan db:seed
```

### 7.1 Importar notas de corte reales
```bash
php artisan import:cutoff-marks
```

### 8. Configurar el almacenamiento
```bash
php artisan storage:link
```

### 9. Compilar assets
```bash
npm run build
```

### 10. Configurar Google Maps (Opcional)
Obtén una API key de Google Maps y añádela al `.env`:
```env
GOOGLE_MAPS_API_KEY=tu_api_key_aqui
```

## 🚀 Ejecutar el proyecto

### Desarrollo
```bash
# Servidor de desarrollo
php artisan serve

# En otra terminal, para assets
npm run dev
```

### Producción
```bash
# Optimizar para producción
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
```

## 👥 Usuarios de Prueba

Después de ejecutar los seeders, tendrás disponibles:

### Administrador
- **Email**: admin@spandam.com
- **Password**: password

### Usuarios de prueba
- **Email**: maria@test.com | carlos@test.com | ana@test.com | david@test.com
- **Password**: password

## 📁 Estructura del Proyecto

```
spandam/
├── app/
│   ├── Http/
│   │   ├── Controllers/     # Controladores
│   │   ├── Middleware/      # Middleware personalizado
│   │   └── Requests/        # Form Requests (validación)
│   ├── Models/              # Modelos Eloquent
│   ├── Repositories/        # Interfaces y repositorios
│   └── Services/            # Lógica de negocio
├── database/
│   ├── migrations/          # Migraciones de BD
│   └── seeders/            # Seeders con datos de prueba
├── resources/
│   ├── views/              # Plantillas Blade
│   ├── css/                # Estilos CSS
│   └── js/                 # JavaScript
└── routes/
    └── web.php             # Rutas de la aplicación
```

## 🔒 Roles y Permisos

### Administrador
- Gestión completa de usuarios y contenido
- Acceso a panel de administración
- Verificación de lugares y eventos

### Propietario
- Publicación y gestión de anuncios de alojamiento
- Estadísticas de sus propiedades

### Estudiante
- Acceso a información académica
- Sistema RoomieMatch
- Creación de eventos
- Búsqueda de alojamientos

### Invitado
- Acceso limitado a información pública
- Visualización de anuncios y eventos

## 🧪 Testing

```bash
# Ejecutar tests
php artisan test

# Tests con cobertura
php artisan test --coverage
```

## 📝 API Endpoints Principales

### Alojamientos
- `GET /listings` - Listar alojamientos
- `POST /listings` - Crear anuncio
- `GET /listings/{id}` - Ver detalle
- `PUT /listings/{id}` - Actualizar
- `DELETE /listings/{id}` - Eliminar
- `POST /subscriptions` - Activar pago mensual automático para un anuncio

### Eventos
- `GET /events` - Listar eventos
- `POST /events` - Crear evento
- `GET /events/{id}` - Ver detalle

### RoomieMatch
- `GET /roomie-match/discover` - Descubrir potenciales compañeros
- `POST /roomie-match/like/{user}` - Dar like a usuario
- `POST /roomie-match/dislike/{user}` - Dar dislike
- `GET /roomie-match/matches` - Ver matches mutuos

### Información Académica
- `GET /academic` - Información general
- `GET /academic/scholarships` - Becas disponibles
- `GET /academic/cut-off-marks` - Notas de corte

## 🔧 Configuración Avanzada

### Optimización de Base de Datos
Los índices están optimizados para las consultas más frecuentes:
- Búsquedas geoespaciales (latitude, longitude)
- Filtros por categoría y fecha
- Búsquedas de texto completo

### Caché
```bash
# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Regenerar caché
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Colas (Opcional)
Para procesar tareas en segundo plano:
```bash
# Configurar driver de cola en .env
QUEUE_CONNECTION=database

# Ejecutar worker
php artisan queue:work
```

## 🛡️ Seguridad

### Middleware Implementado
- Autenticación de usuarios
- Verificación de email
- Control de acceso basado en roles
- Protección CSRF
- Validación de entrada

### Autorización
- Los usuarios solo pueden editar su propio contenido
- Los administradores tienen acceso completo
- Verificación de permisos en cada acción sensible

## 📊 Características Técnicas

### Arquitectura en Capas
```
Controlador -> Servicio -> Repositorio -> Modelo -> Base de Datos
```

### Validación
- Form Requests para validación del lado del servidor
- Mensajes de error personalizados en español
- Validación de tipos de archivo y tamaños

### Gestión de Archivos
- Almacenamiento en `storage/app/public`
- Enlace simbólico para acceso público
- Gestión automática de eliminación de archivos huérfanos

## 🌍 Funcionalidades Geoespaciales

### Búsquedas por Proximidad
- Algoritmo Haversine para cálculo de distancias
- Filtros por radio (1-50 km)
- Optimización con índices geoespaciales

### Integración con Mapas
- Google Maps Embed API
- Marcadores personalizados
- Información emergente (tooltips)

## 📱 Responsividad

La aplicación está optimizada para:
- 📱 Móviles (< 768px)
- 📱 Tablets (768px - 1024px)
- 💻 Desktop (> 1024px)

## 🔄 Flujo de Trabajo Git

### Ramas Recomendadas
```bash
# Desarrollo de nueva característica
git checkout -b feature/nueva-caracteristica
git commit -m "feat: añadir nueva característica"
git push origin feature/nueva-caracteristica

# Pull request a develop
# Merge a main para producción
```

### Convenciones de Commits
- `feat:` Nueva característica
- `fix:` Corrección de bug
- `docs:` Documentación
- `style:` Formato/estilo
- `refactor:` Refactorización
- `test:` Tests
- `chore:` Tareas de mantenimiento

## 🐛 Resolución de Problemas

### Errores Comunes

#### Error de colación MySQL
```bash
# Añadir al .env
DB_COLLATION=utf8mb4_unicode_ci
```

#### Permisos de almacenamiento
```bash
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

#### Error de memoria en Composer
```bash
php -d memory_limit=-1 /usr/local/bin/composer install
```

#### Google Maps no funciona
1. Verificar que `GOOGLE_MAPS_API_KEY` esté configurado
2. Habilitar las APIs necesarias en Google Cloud Console
3. Configurar restricciones de dominio

## 📈 Monitoreo y Logs

### Logs de Laravel
```bash
# Ver logs en tiempo real
tail -f storage/logs/laravel.log

# Limpiar logs
truncate -s 0 storage/logs/laravel.log
```

### Debug Mode
Solo en desarrollo:
```env
APP_DEBUG=true
```

## 🤝 Contribuir

1. Fork del proyecto
2. Crear rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit de cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abrir Pull Request

### Estándares de Código
- PSR-12 para PHP
- Nombres de variables en inglés
- Comentarios en español
- Tests para nuevas funcionalidades

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver `LICENSE` para más detalles.

## 👨‍💻 Autores

- **Equipo Spandam** - *Desarrollo inicial* - Universidad

## 🙏 Agradecimientos

- Laravel Framework
- Bootstrap Team
- Google Maps API
- Comunidad de desarrolladores PHP

## 📞 Soporte

Para soporte técnico o preguntas:
- 📧 Email: soporte@spandam.com
- 📱 Discord: [Servidor Spandam](#)
- 📚 Wiki: [Documentación completa](#)

---

**¡Gracias por usar Spandam! 🎓✨**