<?php

namespace Orobogenius\Statable\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Orobogenius\Statable\Statable;

class Invoice extends Model
{
    use Statable;

    protected $guarded = ['id'];

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function statePaid()
    {
        return [
            'status' => 'paid',
            'with_relations' => ['items' => 'processed']
        ];
    }
}
