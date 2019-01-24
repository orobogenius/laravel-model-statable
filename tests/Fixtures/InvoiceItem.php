<?php

namespace Orobogenius\Statable\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Orobogenius\Statable\Statable;

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
            'status' => 'processed'
        ];
    }
}
