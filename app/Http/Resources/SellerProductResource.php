<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Astrotomic\Translatable\Locales;

class SellerProductResource extends JsonResource
{
    protected $langQueryKey = 'lang';

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locales = app(Locales::class);
        $defaultLang = $locales->current();
        $lang = $request->query($this->langQueryKey) ?? $defaultLang;

        return [
            'id' => $this->id,
            'name' => $this->translations
                ->where('locale', $lang)
                ->pluck('name')
                ->first(),
            'cost' => $this->cost,
            'wallet' => $this->wallet,
            'sellerId' => $this->seller_id,
        ];
    }
}
