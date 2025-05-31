# Tala Trivia Laravel 🏆

Una aplicación de trivia construida con Laravel y completamente dockerizada para facilitar el desarrollo y despliegue. La API está documentada con Swagger y se incluye una colección de Postman para pruebas rápidas.

## 🚀 Inicio Rápido con Docker

### Prerrequisitos

- Docker
- Git

### Instalación

1. **Clonar el repositorio**

   ```bash
   git clone https://github.com/FranciscoArenas/TalaTrivia.git
   cd TalaTrivia
   ```

2. **Configuración inicial automática**

   **En Windows (PowerShell):**

   ```powershell
   .\docker\start.ps1
   ```

   **En Linux/Mac:**

   ```bash
   chmod +x docker/start.sh
   ./docker/start.sh
   ```



3. **¡Listo!** Tu aplicación estará disponible en:
   - 🌐 **Aplicación**: http://localhost:8000
   - 🗄️ **MySQL**: localhost:3306
   - 🔴 **Redis**: localhost:6379

## 🔧 Comandos Útiles

### Docker Compose

```bash
# Iniciar contenedores
docker-compose up -d

# Detener contenedores
docker-compose down

# Ver logs
docker-compose logs -f

# Reconstruir
docker-compose up -d --build
```

### Laravel (dentro del contenedor)

```bash
# Ejecutar migraciones
docker-compose exec app php artisan migrate

# Limpiar cache
docker-compose exec app php artisan cache:clear
```

## 🏗️ Arquitectura Docker

La aplicación utiliza los siguientes servicios:

- **app**: PHP 8.2-FPM con Laravel
- **nginx**: Servidor web Nginx
- **db**: MySQL 8.0
- **redis**: Redis para cache y sesiones

## 📁 Estructura de Docker

```
docker/
├── mysql/
│   ├── my.cnf          # Configuración MySQL
│   └── init.sql        # Script de inicialización
├── nginx/
│   └── default.conf    # Configuración Nginx
├── php/
│   └── local.ini       # Configuración PHP
├── start.sh            # Script de inicio (Linux/Mac)
├── start.ps1           # Script de inicio (Windows)
└── README.md           # Documentación detallada
```

## 🔐 Credenciales por Defecto

### Base de datos MySQL

- **Host**: `db` (dentro de Docker) / `localhost` (desde host)
- **Puerto**: `3306`
- **Base de datos**: `tala_trivia`
- **Usuario**: `tala_trivia`
- **Contraseña**: `tala_trivia`
- **Root password**: `root`

### Redis

- **Host**: `redis` (dentro de Docker) / `localhost` (desde host)
- **Puerto**: `6379`


## 🔄 Desarrollo

Para desarrollo activo, puedes usar:

```bash
# Modo watch para assets
docker-compose exec app npm run dev --watch

# Acceder al shell del contenedor
docker-compose exec app bash

# Ver logs en tiempo real
docker-compose logs -f app
```

## 🛠️ Solución de Problemas

Consulta `docker/README.md` para información detallada sobre solución de problemas comunes.

### Reiniciar todo desde cero

```bash
# Con Make
make reset

# Manualmente
docker-compose down -v
docker system prune -f
docker-compose up -d --build
```

---

## Sobre la tecnologia de la API


### Tecnologías Utilizadas

La aplicación está construida utilizando un conjunto moderno de tecnologías para garantizar un desarrollo eficiente y un rendimiento óptimo:

- **Laravel**: Framework PHP para el desarrollo backend.
- **MySQL**: Base de datos relacional para el almacenamiento de datos.
- **Redis**: Sistema de almacenamiento en memoria para cache y manejo de sesiones.
- **Nginx**: Servidor web para servir la aplicación.
- **Docker**: Contenedores para simplificar el desarrollo y despliegue.
- **Swagger**: Documentación interactiva de la API.
- **Postman**: Colección para pruebas rápidas de la API.

### Arquitectura de la Aplicación

Esta aplicación implementa una **arquitectura API-First** con los siguientes componentes:

1. **API RESTful**: Endpoints JSON que siguen los principios REST para operaciones CRUD.
2. **Controladores de API**: Manejan las peticiones HTTP y devuelven respuestas JSON estructuradas.
3. **Modelos Eloquent**: Gestión de la lógica de negocio y interacción con la base de datos usando el ORM de Laravel.
4. **Servicios y Repositorios**: Capas adicionales para separar la lógica de negocio compleja.
5. **Middleware**: Para autenticación, validación y manejo de CORS.
6. **Recursos de API**: Transformación consistente de datos para las respuestas JSON.

### Separación Frontend-Backend

- **Backend (Esta API)**: Proporciona endpoints JSON para todas las operaciones de trivia.
- **Frontend (Separado)**: Cualquier aplicación cliente (React, Vue, Flutter, etc.) puede consumir esta API.
- **Documentación**: Swagger UI integrado para explorar y probar los endpoints.

Esta arquitectura permite:
- **Escalabilidad**: El frontend y backend pueden escalar independientemente.
- **Flexibilidad**: Múltiples frontends pueden usar la misma API.
- **Mantenibilidad**: Separación clara de responsabilidades.
- **Reutilización**: La API puede ser consumida por diferentes tipos de aplicaciones.