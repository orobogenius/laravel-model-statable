# Laravel Model Statable

This package lets you add predefined states to your laravel models. This allows you to define states that can be applied to your models at various points in your application.
You can define all your states in one place and transition your model instances into different states. For example, you may define an
`admin` state that transforms a user into an admin.

## Contents

- [Installation](#installation)
- [Usage](#usage)
- [Testing](#testing)
- [License](#license)

<a name="installation"></a>

## Installation
#### Composer
To install this package via composer, simply run: 
```bash
$ composer require orobogenius/laravel-model-statable
```
or just add:
```json
"orobogenius/laravel-model-statable": "~1.0"
```
to your `composer.json` file, then run `composer install` or `composer udpate`.

<a name="usage"></a>

## Usage
To add states to your laravel models, add the `Orobogenius\Statable\Statable` trait the model.
```php
<?php

use Orobogenius\Statable\Statable;

class User extends Model
{
    use Statable;
    //...
}
```

This trait lets you apply predefined states to your model instances. To tell the package about the states that can be applied to your models,
you first have to define them. States are defined as methods on your models. To define a state, prefix the model method with `state`.

Defined states should always return an array that is keyed by attributes on your model.

```php

<?php

use Illuminate\Database\Eloquent\Model;
use Orobogenius\Statable\Statable;

class User extends Model
{
    use Statable;
    
    public function stateAdmin()
    {
        return [
            'is_admin' => true
        ];
    }
}
```
#### Applying states
For states that has been defined on your model, you may call the `states` method on your model instances and pass the state(s)
that should be applied to the model:

```php
$user = User::find(1);
$user->states('admin');

// $user->is_admin: true
```
You can, of course, apply more than one state to a model by passing in an array of states to the `states` method:

```php
use Illuminate\Database\Eloquent\Model;
use Orobogenius\Statable\Statable;

class User extends Model
{
    use Statable;
    
    public function stateAdmin()
    {
        return [
            'is_admin' => true
        ];
    }
    
    public function stateModerator()
    {
        return [
            'is_moderator' => true
        ];
    }
}
```
```php
$user = User::find(1);
$user->states(['admin', 'moderator']);

// $user->is_admin: true
// $user->is_moderator: true
```

#### Closure attributes
You may add closure attributes to your model states definitions. The closure also receives the evaluated attributes of the
enclosing states that define them:

```php

<?php

use Illuminate\Database\Eloquent\Model;
use Orobogenius\Statable\Statable;

class User extends Model
{
    use Statable;
    
    public function stateSuperAdmin()
    {
        return [
            'is_super_admin' => function ($attributes) {
                return $this->is_admin && $this->is_moderator;
            }
        ];
    }
}
```

### Relationships
You may specify relationships and states to apply to those relationships in a model's state definition. This lets you to update related
models when you apply certain states to model instances. For example, setting all invoice items to `processed` when an invoice is
in a paid state. To add relations to a model's state definition, add a `with_relations` key to the array that is being returned from the
state defintion. The value should be an array that specifies the relationship as a key and the states to apply as the value:

#### Invoice
```php

<?php

use Illuminate\Database\Eloquent\Model;
use Orobogenius\Statable\Statable;

class Invoice extends Model
{
    use Statable;
    
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function statePaid()
    {
        return [
            'status' => 'paid',
            'with_relations' => ['items' => 'processed']
        ];
    }
}
```
#### InvoiceItem
```php

<?php

use Illuminate\Database\Eloquent\Model;
use Orobogenius\Statable\Statable;

class InvoiceItem extends Model
{
    use Statable;
    
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function stateProcessed()
    {
        return [
            'status' => 'processed'
        ];
    }
}
```
```php
$invoice = Invoice::find(1);
$invoice->states('paid');

// $invoice->status: paid
// $invoice->items: (status) processed
```
The value of the relationship in the model definition can also be an array of states to be applied to the related models.
```php
    public function statePaid()
    {
        return [
            'status' => 'paid',
            'with_relations' => ['items' => ['processed', 'valid']]
        ];
    }
```
<a name="testing"></a>

## Testing
```bash
$ composer test
```

<a name="license"></a>

## License

MIT license (MIT) - Check out the [License File](LICENSE) for more.