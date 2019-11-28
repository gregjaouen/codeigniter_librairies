# codeigniter_librairies

Some simples librairies for CodeIgniter 3.x

## Auth.php
Authorization implementation as CodeIgniter Library:

Must set $config['auth'] in config.php
This must contain at least one type, as an associative array.
This array must contain:
* 'table'       :   for the table to look at
* 'login'       :   the field name for the login
* 'password'    :   the field name for the password

Example:
```
$config['auth'] = [
     'customer' => [
         'table'     => 'customer',
         'login'     => 'customer_mail',
         'password'  => 'customer_password',
     ], [
     'seller' => [
         'table'     => 'seller',
         'login'     => 'seller_mail',
         'password'  => 'seller_password',
     ]
];
```
