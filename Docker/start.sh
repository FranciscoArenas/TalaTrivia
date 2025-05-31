#!/bin/bash

echo "🚀 Iniciando Tala Trivia Laravel con Docker..."

# Construir y ejecutar contenedores
echo "📦 Construyendo contenedores..."
docker-compose up -d --build

echo "⏳ Esperando a que la base de datos esté lista..."
sleep 10

# Ejecutar migraciones
echo "🗄️ Ejecutando migraciones de base de datos..."
docker-compose exec app php artisan migrate --force

# Ejecutar seeders (opcional)
echo "🌱 Ejecutando seeders..."
docker-compose exec app php artisan db:seed --force

# Limpiar cache
echo "🧹 Limpiando cache..."
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear

# Instalar dependencias de Node.js si es necesario
echo "📦 Instalando dependencias de Node.js..."
docker-compose exec app npm install

# Compilar assets
echo "⚡ Compilando assets..."
docker-compose exec app npm run build

echo "✅ ¡Tala Trivia Laravel está listo!"
echo "🌐 Aplicación disponible en: http://localhost:8000"
echo "🗄️ Base de datos MySQL disponible en: localhost:3306"
echo "🔴 Redis disponible en: localhost:6379"
echo ""
echo "Para detener los contenedores ejecuta: docker-compose down"
echo "Para ver logs ejecuta: docker-compose logs -f"
