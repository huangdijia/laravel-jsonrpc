# laravel-jsonrpc

[![Latest Version on Packagist](https://img.shields.io/packagist/v/huangdijia/laravel-jsonrpc.svg?style=flat-square)](https://packagist.org/packages/huangdijia/laravel-jsonrpc)
[![Total Downloads](https://img.shields.io/packagist/dt/huangdijia/laravel-jsonrpc.svg?style=flat-square)](https://packagist.org/packages/huangdijia/laravel-jsonrpc)
[![GitHub license](https://img.shields.io/github/license/huangdijia/laravel-jsonrpc)](https://github.com/huangdijia/laravel-jsonrpc)

## Installation

composer require huangdijia/laravel-jsonrpc

## Usage

### As Server

#### Controller

```php
use Huangdijia\JsonRpc\Controller;

class ExampleController extends Controller
{
    public function action()
    {
        return 'some result';
    }
}
```

### Route

```php
Route::middleware([Huangdijia\JsonRpc\Middleware::class])->group(function() {
    Route::post('/example', 'ExampleController');
});
```

### As Client

```php
$client = new Huangdijia\JsonRpc\Client($url);
$result = $client->action(); // some result
```
