# 1 Create Symfony project
symfony new --webapp allo_ambulance

# 2 Install required bundles
composer require symfony/orm-pack
composer require --dev symfony/maker-bundle
composer require symfony/security-bundle
composer require symfony/form
composer require symfony/validator
composer require symfony/twig-pack
composer require symfony/asset

# 3 Configure database (.env)
DATABASE_URL="mysql://root:@127.0.0.1:3306/allo_ambulance_symfony?serverVersion=8.0"

# 4 Create database
php bin/console doctrine:database:create

# 5 Generate entities
php bin/console make:entity

# 6 Create migrations
php bin/console make:migration

# 7 Run migrations
php bin/console doctrine:migrations:migrate
# 8 Install fixtures
composer require --dev orm-fixtures

# 9 Load fixtures
php bin/console doctrine:fixtures:load

# 10 Run development server
symfony server:start
# Clear cache
php bin/console cache:clear

