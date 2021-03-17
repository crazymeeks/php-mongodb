## php-mongodb
#
php-mongodb is a PHP library that wraps `MongoDB\Client` library and provides a clean api for interacting with MongoDB database.
#
### # System requirements
* `>=php7.2`
* `php7.x-mongodb extension`
* `mongodb php driver`

### # Installation
* Install mongodb driver  
   * `apt update && apt upgrade -y`
   * `pecl install mongodb-1.9.0`  
* Install this library via composer 
  * `composer install crazymeeks/php-mongodb`  
#
### # Usage
*Connect to MongoDB Database*
```php
use Crazymeeks\MongoDB\Facades\Connection;

Connection::setUpConnection('127.0.0.1', ['username' => 'root', 'password' => 'root'], [])
          ->setDefaultDatabase('testing_mytestdb_crzymix')
          ->connect();
```  
*Extend **Crazymeeks\MongoDB\Model\AbstractModel** class*  
```php
namespace Some\Namespace;

use Crazymeeks\MongoDB\Model\AbstractModel;

class User extends AbstractModel
{

    // Required
    protected $collection = 'users';

    // Required
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
    ];
}
```  
### # Inserting data ##
First approach:
```php
$user = new User([
    'firstname' => 'John',
    'lastname' => 'Doe',
    'email' => 'john.doe@example.com',
]);
$user->save();
echo $user->firstname;
// result: John
```  
Second approach:
```php
$user = User::create([
    'firstname' => 'John',
    'lastname' => 'Doe',
    'email' => 'john.doe@example.com',
]);

echo $user->firstname;
// result: John
```  
**Note:** `save()` and `create()` methods will automatically add `created_at` and `updated_at`  
 timestamps fields when performing an insert. If you wish to disable this, just add `protected $timestamps = true;` to your model class.  
 Or error, this will throw `\Exception` or `\Error` when something went wrong.  
#
### # Update ###
*Single*  
```php
$user = new User();
$user->whereEq('firstname', 'John')
     ->update([
         'firstname' => 'Jane',
     ]);
```  
*Bulk update*  
```php
$user = new User();
$user->whereEq('firstname', 'John')
     ->updateMany([
         'firstname' => 'Jane',
     ]);
```  
### # Delete ###
*Single*
```php
$user = new User();
$user->whereEq('firstname', 'John')
     ->delete();
```  
*Bulk delete*  
```php
$user = new User();
$user->whereEq('firstname', 'John')
     ->deleteMany();
```
#
### # Finding or Querying data from collection. ##
### Current methods available ###

*__whereEq(string $field, string $value)__* - where equal query and case-insentive.  
*__whereNotEq(string $field, string $value)__* - where not equal query and case-insensitive.  
*__whereIn(string $field, array($value1, $value2, ...))__* - Case-insensitive.  
*__whereNotIn(string $field, array($value1, array $value2, ...))__* - Case-insensitive.  
*__whereGreater(string $field, mixed $value)__*  $value could be int|string. Works great for int types.  
*__whereGreaterOrEq(string $field, mixed $value)__* - $value could be int|string. Works great for int types.  
*__whereLessThanOrEq(string $field, mixed $value)__* - $value could be int|string. Works great for int types.  
*__whereLessThan(string $field, mixed $value)__* - $value could be int|string. Works great for int types.  
*__first()__* - Returns object that extends \Crazymeeks\MongoDB\Model\AbstractModel. You may `count()` result of this function for counter checking.
*__get()__* - Returns an array object that extends \Crazymeeks\MongoDB\Model\AbstractModel. You may `count()` result of this function for counter checking.  
#
### # Query samples ###
```php
$user = new User();
$result = $user->whereEq('firstname', 'John')->first();
if (count($result) > 0) {
    echo $result->firstname;
    // result: John
}
```  
### # Chaining multiple queries ###
You may chain multiple queries too.
```php
$user = new User();
$users = $user->whereEq('email', 'john.doe@example.com')
                ->whereIn('name', ['john'])
                ->get();
foreach($users as $user){
    echo $user->name . "<br>";
}
// or you may also call methods statically
$users = User::whereEq('email', 'john.doe@example.com')
                ->whereIn('name', ['john'])
                ->get();

```  
#
### # Laravel integration ###
Register connection to app service provider.
```php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Crazymeeks\MongoDB\Facades\Connection;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        
        Connection::setUpConnection('127.0.0.1', ['username' => 'root', 'password' => 'root'], [])
          ->setDefaultDatabase('testing_mytestdb_crzymix')
          ->connect();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
```  
*Model*  
```php
namespace App;

use Crazymeeks\MongoDB\Model\AbstractModel;

class User extends AbstractModel
{
    protected $collection = 'users';

    protected $fillable = [
        'firstname',
        'lastname',
        'email',
    ];

}
```  
__Important:__ Laravel's relationship is not yet supported.