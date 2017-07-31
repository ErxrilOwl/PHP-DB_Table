# PHP-DB_Table
A PHP Classes for Database and Table helper methods and CRUD.
---

## Using Database Class
Database is a class for database connection, disconnection, table creation and deletion.

* Create Table
```php
	Database::createTable("product", "
		CREATE TABLE IF NOT EXISTS product(
		id 				INT(6) 			AUTO_INCREMENT,
		name				VARCHAR(255) 		NOT NULL,
		description 			VARCHAR(255) 		NULL,
		price 				DECIMAL 		NOT NULL,
		image 				LONGBLOB 		NULL,
		created_at			TIMESTAMP,
		deleted_at			DATETIME		DEFAULT NULL,
		
		PRIMARY KEY(id)
	)");

```

* Drop Table
```php
	Database::dropTable($tblName);
```

## Using DB_Table Class
DB_Table is a class for retrieving, inserting, updating and deleting rows in the database table.

* SELECT ALL
```php
	DB_Table::all('roles');
	DB_Table::all('roles', 'assoc');
	DB_Table::all('roles', 'array');
	DB_Table::all('roles', 'object');
```

* SELECT WHERE
```php
	DB_Table::where('roles', 'object', ["name" => "admin"]);
```
	
* INSERT 
```php
	DB_Table::insert('users', ['fname' => 'Nikko', 'lname' => 'Atuan', 'email' => 'sampleemail@email.com']);
	DB_Table::insert('roles', ['name' => 'admin', 'display_name' => 'admin', 'description' => 'admin']);
	DB_Table::insert('roles', ['name' => 'superadmin', 'display_name' => 'Super Admin', 'description' => 'Can do all']);
```

* UPDATE
```php
	DB_Table::update('roles', [
		"name" => "admin", 
	 	"display_name" => "Admin", 
	 	"description" => "admin"
	  ], ["id" => 1]);
```

* DELETE
```php
	DB_Table::delete('roles', ["name" => "user", "display_name" => "user"]);
```	

* RESET 
```php
	DB_Table::reset('roles');
```	
