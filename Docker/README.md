# Configuración de entorno Docker para Tala Trivia Laravel

## Puertos utilizados

- **Aplicación Laravel**: http://localhost:8000
- **Base de datos MySQL**: localhost:3306
- **Redis**: localhost:6379

## Credenciales de la base de datos

- **Host**: db (dentro de Docker) / localhost (desde host)
- **Puerto**: 3306
- **Base de datos**: tala_trivia
- **Usuario**: tala_trivia
- **Contraseña**: tala_trivia
- **Usuario root**: root
- **Contraseña root**: root

## Comandos útiles

### Iniciar el proyecto

```bash
# En Linux/Mac
./docker/start.sh

# En Windows PowerShell
.\docker\start.ps1
```

### Comandos Docker Compose

```bash
# Iniciar contenedores
docker-compose up -d

# Detener contenedores
docker-compose down

# Ver logs
docker-compose logs -f

# Reconstruir contenedores
docker-compose up -d --build

# Ejecutar comandos en el contenedor de la app
docker-compose exec app php artisan migrate
docker-compose exec app php artisan tinker
docker-compose exec app composer install
```

### Comandos Laravel

```bash
# Migraciones
docker-compose exec app php artisan migrate
docker-compose exec app php artisan migrate:fresh --seed

# Cache
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear

# Generar clave de aplicación
docker-compose exec app php artisan key:generate

# Crear enlace simbólico de storage
docker-compose exec app php artisan storage:link
```


## Estructura de archivos Docker

```
docker/
├── mysql/
│   ├── my.cnf          # Configuración MySQL
│   └── init.sql        # Inicialización de la BD
├── nginx/
│   └── default.conf    # Configuración Nginx
├── php/
│   └── local.ini       # Configuración PHP
├── start.sh            # Script de inicio (Linux/Mac)
└── start.ps1           # Script de inicio (Windows)
```

## Solución de problemas

### Si la base de datos no se conecta

1. Verificar que el contenedor de MySQL esté ejecutándose
2. Esperar unos segundos más para que MySQL termine de inicializarse
3. Verificar la configuración en el archivo .env

### Si hay problemas de permisos

```bash
# Arreglar permisos de storage y cache
docker-compose exec app chmod -R 775 storage
docker-compose exec app chmod -R 775 bootstrap/cache
```

### Si necesitas acceder a la base de datos directamente

```bash
docker-compose exec db mysql -u tala_trivia -p tala_trivia
# Contraseña: tala_trivia
```

### Para limpiar todo y empezar de nuevo

```bash
docker-compose down -v
docker system prune -f

# En Linux/Mac
./docker/start.sh

# En Windows PowerShell
.\docker\start.ps1
```
