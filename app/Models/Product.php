<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use App\Models\User;

class Product extends Model implements TranslatableContract
{
    use HasFactory;
    use Translatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'cost',
        'wallet',
        'seller_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'wallet' => 'json'
    ];

    /**
     * The attributes that can be translated.
     *
     * @var array<string>
     */
    public $translatedAttributes = ['name'];

    public function seller()
    {
        return $this->belongsTo(User::class);
    }
}
