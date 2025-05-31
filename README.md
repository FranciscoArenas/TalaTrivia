# Tala Trivia Laravel 🏆

Una aplicación de trivia construida con Laravel y completamente dockerizada para facilitar el desarrollo y despliegue. La API está documentada con Swagger y se incluye una colección de Postman para pruebas rápidas.

## 🚀 Inicio Rápido con Docker

### Prerrequisitos

- Docker
- Git

## 🚀 Instalación Rápida

> **⚡ ¡Ejecuta tu API TalaTrivia en menos de 5 minutos!**

### 📋 Prerrequisitos

Asegúrate de tener instalado:
- ✅ **Docker** (con Docker Compose)
- ✅ **Git**

---

### 🛠️ Pasos de Instalación

#### **1️⃣ Clonar el repositorio**

```bash
git clone https://github.com/FranciscoArenas/TalaTrivia.git
cd TalaTrivia
```

#### **2️⃣ Ejecutar configuración automática**

<details>
<summary><strong>🐧 Para Linux/Mac</strong></summary>

```bash
chmod +x docker/start.sh
./docker/start.sh
```

</details>

<details>
<summary><strong>🪟 Para Windows (PowerShell)</strong></summary>

```powershell
.\docker\start.ps1
```

</details>

#### **3️⃣ ¡Todo listo! 🎉**

> **⏱️ Tiempo estimado:** 3-5 minutos (dependiendo de la conexión a internet)

---

## 🌐 Tu aplicación estará disponible en:

<table>
<tr>
<td align="center">

### 🚀 **API Principal**
**http://localhost:8000**

Documentación Swagger incluida

</td>
<td align="center">

### 🗄️ **Base de Datos MySQL**
**localhost:3306**

Usuario: `tala_trivia`
Contraseña: `tala_trivia`

</td>
<td align="center">

### 🔴 **Redis Cache**
**localhost:6379**

Para sesiones y caché

</td>
</tr>
</table>

---

### 🔗 Enlaces Rápidos

| Servicio | URL | Descripción |
|----------|-----|-------------|
| 📚 **API Docs** | [http://localhost:8000/api/documentation](http://localhost:8000/api/documentation) | Documentación Swagger interactiva |
| 🎯 **API Base** | [http://localhost:8000/api](http://localhost:8000/api) | Endpoint base de la API |
| 🔍 **Health Check** | [http://localhost:8000/health](http://localhost:8000/health) | Estado de la aplicación |

> 💡 **Tip:** Guarda estos enlaces en tus favoritos para acceso rápido durante el desarrollo.

---

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



## 💻 Sobre la tecnologia de la API


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