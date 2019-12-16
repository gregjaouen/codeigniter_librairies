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
  
This array might contain:
* 'encoding'    :   ["plaintext" | "bcrypt"];
The encoding "bcrypt" is set as default

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
         'encoding'  =>  'plaintext'
     ]
];
```
Session format:
```
table_name => [
     user_type => type,
     user => [
         login_name => login,
         password_name => password
     ]
]
```


## Basket.php
Basket manager in session for CodeIgniter 3.x

An element in the basket must be an array.

Must set $config['basket'] in config.php
This have to contain:
         - 'id_key'      :   for key to identify an element in the basket

Example:
$config['basket'] = [
     'id_key' => 'a_key'
];

TODO: API description

### Technical
#### Basket session format:
```
[
    [
        Basket::ELEMENT => array,
        Basket::QTY     => int
    ],
    [
        Basket::ELEMENT => array,
        Basket::QTY     => int
    ]
    ...
]
```
