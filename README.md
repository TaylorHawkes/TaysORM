# TaysORM

This is a super simple class for saving/editing record in a mysql database. It really helps for quickly/easily updating single records. 
#installation & usage

Install with composer:
```sh
composer install taylor-hawkes\easy-connect
composer install taylor-hawkes\tays-orm
```

Generate a new model with the generator script:
```sh
cd vendor/taylor-hawkes/tays-orm
```
```sh
php generate_model.php --table="users" --host="database_ip" --user="db_user" --pass="db_pass" --database="db_name" --table="tablename"
```

``You can  hard code the database params in the generate_table script so you don't have to put them in as params everytime.``

``This script by default installs all new Models and Base Models three directories up (in a TModel folder) from the TaysORM installation directory. You can edit where the script installs your Models.``


# Usage Examples:
Create a new user:
```php
$user=new \TModel\Users();
$user->first_name="taylor";
$user->last_name="hawkes";
$user->save();
```
Edit a user:
```php
$user=new \TModel\Users();
$user->fetchRow("1");// 1 is the value of the tables primary key
$user->last_name="Malone"; // I got married and was forced to take my wifes name
$user->save();
```
You can also fetch a row like this: 
```php
$user=new \TModel\Users();
$user->fetchRowWhere("first_name = 'taylor' and lastname ='hawkes'");
$user->last_name="Malone"; 
$user->save();
```
Selecting Multiple Records:
```php
$user=new \TModel\Users();
$all_users= $user->fetchAssoc("select * from users");
```
All Other Queries:
```php
$user=new \TModel\Users();
$do_something_else=$user->query("update users set ...");
```

# Requirements
> EasyConnect - [https://github.com/TaylorHawkes/EasyConnect]


License
----

MIT
