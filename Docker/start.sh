#!/bin/bash

# Colores para output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
CYAN='\033[0;36m'
MAGENTA='\033[0;35m'
NC='\033[0m' # No Color

echo -e "${GREEN}Iniciando Tala Trivia Laravel con Docker...${NC}"

echo -e "${YELLOW}Construyendo contenedores...${NC}"
docker-compose up -d --build

if [ $? -ne 0 ]; then
    echo -e "${RED}Error al construir los contenedores${NC}"
    exit 1
fi

echo -e "${YELLOW}Esperando a que la base de datos este lista...${NC}"
sleep 15

echo -e "${YELLOW}Instalando dependencias de Vendor...${NC}"
docker-compose exec -u root app composer install --no-dev --optimize-autoloader
docker-compose exec -u root app chown -R www:www /var/www/vendor
docker-compose exec app mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/app/public
docker-compose exec app cp .env.example .env
docker-compose exec app php artisan key:generate --force
docker-compose exec app php artisan storage:link
docker-compose exec app php artisan key:generate --force

echo -e "${YELLOW}Ejecutando migraciones de base de datos...${NC}"
docker-compose exec app php artisan migrate --force

echo -e "${YELLOW}Ejecutando seeders...${NC}"
docker-compose exec app php artisan db:seed --force

echo -e "${YELLOW}Limpiando cache...${NC}"
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear

echo ""
echo -e "${GREEN}Tala Trivia Laravel esta listo!${NC}"
echo -e "${CYAN}Aplicacion disponible en: http://localhost:8000${NC}"
echo -e "${CYAN}Base de datos MySQL: localhost:3306${NC}"
echo -e "${CYAN}Redis: localhost:6379${NC}"
echo ""
echo -e "${MAGENTA}Para detener: docker-compose down${NC}"
echo -e "${MAGENTA}Para logs: docker-compose logs -f${NC}"