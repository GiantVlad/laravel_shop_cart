-- Second database for the PHPUnit suite (see phpunit.xml -> DB_DATABASE=shop_test).
-- Living in the same MariaDB container as the dev `shop` database keeps the stack small and
-- stops `RefreshDatabase` from emptying the database you are working in.
--
-- Files here run only while the data directory is being initialised (docker-entrypoint-initdb.d
-- semantics). On an installation that already has data, create it once by hand:
--   docker compose exec mariadb mariadb -uroot -p"$MARIADB_ROOT_PASSWORD" \
--     -e "CREATE DATABASE IF NOT EXISTS shop_test; GRANT ALL ON shop_test.* TO 'admin'@'%';"

CREATE DATABASE IF NOT EXISTS shop_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- MARIADB_USER is granted on MARIADB_DATABASE only, so the app user needs an explicit grant here.
GRANT ALL PRIVILEGES ON shop_test.* TO 'admin'@'%';

FLUSH PRIVILEGES;
