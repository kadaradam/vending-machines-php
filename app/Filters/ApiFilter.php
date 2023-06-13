<?php

namespace App\Filters;

use Illuminate\Http\Request;

class ApiFilter
{
  protected $allowedParms = [];

  protected $columnMap  = [];

  protected $operatorMap  = [
    'eq' => '=',
    'gt' => '>',
    'gte' => '>=',
    'lt' => '<',
    'lte' => '<=',
  ];

  public function transform(Request $request)
  {
    $eloQuery = [];

    foreach ($this->allowedParms as $parm => $operators) {
      $query = $request->query($parm);

      if (!isset($query)) {
        continue;
      }

      $column = $this->columnMap[$parm] ?? $parm;

      foreach ($operators as $operator) {
        if (isset($query[$operator])) {
          $eloOperator = $this->operatorMap[$operator];
          $eloQuery[] = [$column, $eloOperator, $query[$operator]];
        }
      }
    }

    return $eloQuery;
  }
}
