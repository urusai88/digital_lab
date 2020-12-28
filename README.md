1) git clone git@github.com:urusai88/digital_lab.git
2) cp (или copy в windows) .env.example .env
Для тестирования будем использовать sqlite базу данных
Следующая команда создаёт storage/app/db.sqlite файл 
3) php artisan app:create_sqlite_database
4) Зайти в файл .env и поправить DB_CONNECTION=mysql на DB_CONNECTION=sqlite
