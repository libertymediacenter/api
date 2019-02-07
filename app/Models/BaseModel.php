<?php

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    use Cachable;

    protected $casts = [
        'id' => 'string',
    ];

    protected $keyType = 'string';


    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }
}
