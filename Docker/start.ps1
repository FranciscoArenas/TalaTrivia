[Console]::OutputEncoding = [System.Text.Encoding]::UTF8

Write-Host "Iniciando Tala Trivia Laravel con Docker..." -ForegroundColor Green

Write-Host "Construyendo contenedores..." -ForegroundColor Yellow
docker-compose up -d --build

if ($LASTEXITCODE -ne 0) {
    Write-Host "Error al construir los contenedores" -ForegroundColor Red
    exit 1
}

Write-Host "Esperando a que la base de datos este lista..." -ForegroundColor Yellow
Start-Sleep -Seconds 15

Write-Host "Instalando dependencias de Vendor..." -ForegroundColor Yellow
docker-compose exec -u root app composer install --no-dev --optimize-autoloader
docker-compose exec -u root app chown -R www:www /var/www/vendor
docker-compose exec app php artisan storage:link
docker-compose exec app php artisan key:generate --force

Write-Host "Ejecutando migraciones de base de datos..." -ForegroundColor Yellow
docker-compose exec app php artisan migrate --force

Write-Host "Ejecutando seeders..." -ForegroundColor Yellow
docker-compose exec app php artisan db:seed --force

Write-Host "Limpiando cache..." -ForegroundColor Yellow
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear

Write-Host ""
Write-Host "Tala Trivia Laravel esta listo!" -ForegroundColor Green
Write-Host "Aplicacion disponible en: http://localhost:8000" -ForegroundColor Cyan
Write-Host "Base de datos MySQL: localhost:3306" -ForegroundColor Cyan
Write-Host "Redis: localhost:6379" -ForegroundColor Cyan
Write-Host ""
Write-Host "Para detener: docker-compose down" -ForegroundColor Magenta
Write-Host "Para logs: docker-compose logs -f" -ForegroundColor Magenta