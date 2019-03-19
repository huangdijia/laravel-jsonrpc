# laravel-jsonrpc

## Installation

composer require huangdijia/laravel-jsonrpc

## Usage

### As Server

#### Controller

```php
use Huangdijia\JsonRpc\Controller;

class ExampleController extends Controller
{
    //
}
```

### Route

```php
Route::middleware([Huangdijia\JsonRpc\Middleware::class])->group(function() {
    Route::post('/user', 'ExampleController');
});
```

### As Client

```php
$client = new Huangdijia\JsonRpc($url);
$result = $client->action();
```