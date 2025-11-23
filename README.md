1. cmd /c "mysql -u root -p < database/sql/init_tracklin.sql"
2. klik enter
3. php artisan optimize:clear
4. php artisan config:clear
5. php artisan cache:clear
5. php artisan migrate

apabila terjadi error 419 (csrf token error), maka lakukan:
1. php artisan optimize:clear
2. php artisan view:clear
3. php artisan config:clear

<!-- php artisan optimize:clear
php artisan config:clear
php artisan cache:clear
php artisan migrate -->