<?php

namespace Orobogenius\Statable\Tests\Fixtures;

use Orobogenius\Statable\Statable;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use Statable;

    protected $guarded = ['id'];

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function statePaid()
    {
        return [
            'status' => 'paid',
            'with_relations' => ['items' => 'processed'],
        ];
    }
}
