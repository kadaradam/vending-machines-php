<?php

namespace App\Filters;

use App\Filters\ApiFilter;

class ProductFilter extends ApiFilter
{
    protected $allowedParms = [
        'name' => ['eq'],
        'cost' => ['eq', 'gt', 'lt'],
        'sellerId' => ['eq']
    ];

    protected $columnMap  = [
        'sellerId' => 'seller_id'
    ];
}
