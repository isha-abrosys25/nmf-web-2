<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Party;

class Candidate extends Model
{
    protected $fillable = ['party_id', 'candidate_name', 'candidate_image', 'area'];

    // Relationship with Party table
    public function party()
    {
        return $this->belongsTo(Party::class, 'party_id');
    }
}

