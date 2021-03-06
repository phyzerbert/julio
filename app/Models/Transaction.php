<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $guarded = [];

    public function payment(){
        return $this->belongsTo(Payment::class);
    }

    public function tcategory() {
        return $this->belongsTo(Tcategory::class);
    }
}
