<?php

namespace Orobogenius\Statable\Tests\Fixtures;

use Orobogenius\Statable\Statable;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use Statable;

    protected $guarded = ['id'];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function stateProcessed()
    {
        return [
            'status' => 'processed',
        ];
    }
}
