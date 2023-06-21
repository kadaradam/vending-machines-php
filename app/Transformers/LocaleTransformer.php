<?php

namespace App\Transformers;

use Astrotomic\Translatable\Locales;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;

class LocaleTransformer
{
  protected $transformParms = [];
  protected $langQueryKey = 'lang';

  public function transform(Request $request)
  {
    $reqBody = $request->validated();
    $locales = app(Locales::class);
    $allLangs = $locales->all();
    $defaultLang = $locales->current();
    $lang = $request->query($this->langQueryKey) ?? $defaultLang;

    if (isset($lang) && !in_array($lang, $allLangs)) {
      throw abort(
        HttpResponse::HTTP_UNPROCESSABLE_ENTITY,
        'Invalid language key provided'
      );
    }

    foreach ($this->transformParms as $parm) {
      $translatableProp = $reqBody[$parm];

      if (!isset($translatableProp)) {
        continue;
      }

      $originalValue = $reqBody[$parm];

      unset($reqBody[$parm]);

      $reqBody = [
        ...$reqBody,
        $lang => [$parm => $originalValue],
      ];
    }

    return $reqBody;
  }
}
