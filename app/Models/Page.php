<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'pages';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];
}
