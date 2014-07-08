## Trusty - Roles and Permissions for Laravel 4

### Server Requirements

- PHP 5.4 or higher

### Installation

Open your composer.json file, and add the new required package.
```
"pingpong/trusty": "dev-master" 
```
Next, open a terminal and run.
```
composer update 
```

Next, Add new service provider in `app/config/app.php`.

```php
  
  'Pingpong\Trusty\TrustyServiceProvider',
  
```

Next, Add new aliases in `app/config/app.php`.

```php
'Trusty'      => 'Pingpong\Trusty\Facades\Trusty',
'Role'		  => 'Pingpong\Trusty\Entities\Role',
'Permission'  => 'Pingpong\Trusty\Entities\Permission',
```

Next, migrate the database.
```
php artisan migrate --package=pingpong/trusty
```

**NOTE:** If you want to modify the `roles` and `permissions` table, you can publish the migration.

Done.

### Usage

Open your `app/models/User.php` file and use the `Pingpong\Trusty\Traits\TrustyTrait` trait. Look like this.

```php
<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

use Pingpong\Trusty\Traits\TrustyTrait;

class User extends \Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait, TrustyTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token');

	/**
	 * The fillable property.
	 * 	
	 * @var array
	 */
	protected $fillable = array('name', 'username', 'email', 'password', 'status', 'remember_token');

}
?>
```

Creating new Role.
```php
Role::create([
	'name'			=>	'Administrator',
	'slug'			=>	Str::slug('Administrator', '_'),
	'description'	=>	'The Super Administrator'
]);

// without description
Role::create([
	'name'	=>	'Editor',
	'slug'	=>	Str::slug('Editor', '_'),
]);
```

Creating new Permission.

```php
Permission::create([
	'name'			=>	'Manage Users',
 	'slug'			=>	Str::slug('Manage Users', '_'), // manage_users
 	'description'	=>	'Create, Read, Update and Delete Users'
]);

// without description
Permission::create([
	'name'			=>	'Manage Posts',
 	'slug'			=>	Str::slug('Manage Posts', '_'), // manage_posts
]);
```

Set permission for the specified role.

```php
$permission_id = 1;
$role = Role::findOrFail(1);
$role->permissions()->attach($permission_id);
```

Set role for current user.
```php
$role_id = 1;
$user = Auth::user();
$user->roles()->attach($role_id);
```

Check role for current user.
```php
if(Auth::user()->is('administrator'))
{
	// your code here
}
```

Or using magic method.
```php
if(Auth::user()->isAdministrator())
{
	// your code
}
```

Check permission for current user.
```php
if(Auth::user()->can('manage_users'))
{
	// your code here
}
```

Or using magic method.
```php
if(Auth::user()->canManageUsers())
{
	// your code
}
```

Get all permission from current users.
```php
$myPermissions = Auth::user()->getPermissions();
dd($myPermissions);
// or 

$permissions = Auth::user()->permissions();
dd($permissions);

```

Get role for current user.
```php
$myRole = Auth::user()->getRole();
```

Simple filtering route based on permission.
```php

// set view when the current user is not have a specified permission
Trusty::setView('403');

// register all permission as filter
Trusty::registerPermissions();

// filter request 
Trusty::when('admin/*', 'filter_name');
 
// mutiple request 
Trusty::when(['admin/users'. 'admin/users/*'], 'manage_users');
```

### License

This package is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).