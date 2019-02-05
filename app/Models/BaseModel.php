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
        config(['laravel-model-caching.cache-prefix' => str_slug(config('app.name') . 'model-cache')]);

        parent::__construct($attributes);
    }
}
