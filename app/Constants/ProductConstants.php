<?php

namespace App\Constants;

class ProductConstants
{
    const PRODUCT_1001 = "PRODUCT-1001";
    const PRODUCT_1002 = "PRODUCT-1002";
    const PRODUCT_1003 = "PRODUCT-1003";
    const PRODUCT_1004 = "PRODUCT-1004";
    const PRODUCT_1005 = "PRODUCT-1005";

    const CATEGORY_2001 = "CATEGORY-2001";
    const CATEGORY_2002 = "CATEGORY-2002";




    const RESPONSE_CODES_MESSAGES = [
        self::PRODUCT_1001 => 'translation.listRetrieved',
        self::PRODUCT_1002 => 'translation.listUpdated',
        self::PRODUCT_1003 => 'translation.createdSuccefully',
        self::PRODUCT_1004 => 'translation.notFound',
        self::PRODUCT_1005 => 'translation.deleted',

        self::CATEGORY_2001 => 'translation.categoryCreatedSuccefully',
        self::CATEGORY_2002 => 'translation.listRetrieved',




    ];
}
