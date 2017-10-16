<?php

namespace App\Transformers;

use App\Buyer;
use League\Fractal\TransformerAbstract;

class BuyerTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Buyer $buyer)
    {
        return [
            'identifier' => (int)$buyer->id,
            'name' => (string)$buyer->name,
            'email' => (string)$buyer->email,
            'isVerified' => (int)$buyer->verified,
            'isAdmin' => ($buyer->admin === 'true'),
            'creationDate' => (string)$buyer->created_at,
            'lastChange' => (string)$buyer->updated_at,
            'deletedDate' => isset($buyer->deleted_at) ? (string)$buyer->deleted_at : null,
            'links' => [
                [
                    'rel' => 'self',
                    'href' => route('buyers.show', $buyer->id),
                ],
                [
                    'rel' => 'buyer.transaction',
                    'href' => route('buyers.transactions.index', $buyer->id),
                ],
                [
                    'rel' => 'buyer.product',
                    'href' => route('buyers.products.index', $buyer->id),
                ],
                [
                    'rel' => 'buyer.seller',
                    'href' => route('buyers.sellers.index', $buyer->id),
                ],
                [
                    'rel' => 'buyer.category',
                    'href' => route('buyers.categories.index', $buyer->id),
                ],
            ]
        ];
    }

    public static function originalAttribute($index)
    {
        $attribute = [
            'identifier' => 'id',
            'name' => 'name',
            'email' => 'email',
            'isVerified' => 'verified',
            'creationDate' => 'created_at',
            'lastChange' => 'updated_at',
            'deletedDate' => 'deleted_at',
        ];

        return isset($attribute[$index]) ? $attribute[$index] : null;
    }

    public static function transformedAttribute($index)
    {
        $attribute = [
            'id' => 'identifier',
            'name' => 'name',
            'email' => 'email',
            'verified' => 'isVerified', 
            'created_at' => 'creationDate',
            'updated_atl' => 'lastChange',
            'deleted_at' => 'deletedDate',
        ];

        return isset($attribute[$index]) ? $attribute[$index] : null;
    }
}
