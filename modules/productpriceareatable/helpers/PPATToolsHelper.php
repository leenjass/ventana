<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    Musaffar Patel
 * @copyright 2016-2021 Musaffar Patel
 * @license   LICENSE.txt
 */

class PPATToolsHelper
{
    /**
     * @return int
     */
    public static function getPricePrecision()
    {
        if ((int)_PS_PRICE_COMPUTE_PRECISION_ == 0 || !defined(_PS_PRICE_COMPUTE_PRECISION_)) {
            return 2;
        } else {
            return _PS_PRICE_COMPUTE_PRECISION_;
        }
    }
}
