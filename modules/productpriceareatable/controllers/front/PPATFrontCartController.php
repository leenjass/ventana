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
 * @copyright 2016-2017 Musaffar Patel
 * @license   LICENSE.txt
 */

class PPATFrontCartController extends PPATControllerCore {

	protected $sibling;

	public function __construct(&$sibling)
	{
		parent::__construct($sibling);

		if ($sibling !== null) {
            $this->sibling = &$sibling;
        }
	}

	public function setMedia()
	{
		if (Context::getContext()->controller->php_self == 'cart') {
            $this->sibling->context->controller->addJS($this->sibling->_path . 'views/js/front/PPATFrontCartController.js');
        }
	}

	/**
	 * Add Tax to price
	 * @param $taxManager
	 * @param $price_display
	 * @param $use_tax
	 * @param $price
	 * @param int $is_cusomization_line
	 * @return mixed
	 */
	private static function _addTax($taxManager, $use_tax, $price, $is_cusomization_line = 0)
	{
		if ($use_tax)
			return $taxManager->addTaxes($price);
		else
			return $price;
	}

	/**
	 * Apply a discount to a given price
	 * @param $price
	 * @param $amount
	 * @param $type
	 * @return mixed
	 */
	private function _applyDiscount($price, $amount, $type)
	{
		if ($type == 'percentage')
			$price = $price - ($price * $amount);

		if ($type == 'amount')
			$price = $price - $amount;
		return $price;
	}

    /**
     * Called by overrides/cartcontroller.php when products is added
     * @param $mode
     * @param $id_customization
     * @return bool|int
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function processChangeProductInCartAdd($mode, $id_customization)
    {
        $id_product = Tools::getValue('id_product');
        $id_shop = Context::getContext()->shop->id;
        if ($mode == 'add') {
            $ppat_front_cart_controller = new PPATFrontCartController($this->sibling);
            if (PPATProductHelper::isPPATProduct($id_product, $id_shop)) {
                return $ppat_front_cart_controller->addToCart($id_customization);
            }
        }
    }


    /**
     * @param $mode
     * @param $id_customization
     */
	public function processChangeProductInCartUpdate($mode)
    {
        $id_product = Tools::getValue('id_product');
        $id_product_attribute = Tools::getValue('id_product_attribute');
        $id_customization = Tools::getValue('id_customization');
        $id_cart = Context::getContext()->cart->id;
        $op = Tools::getValue('op');

        if ($mode == 'update') {
            $ppat_only_customized = PPATCartHelper::hasPPATOnlyCustomizedData($id_customization, $id_product, $id_product_attribute, $id_cart);
            if ($ppat_only_customized == true) {
                if ($op == 'up') {
                    PPATCartHelper::incrementProductCustomizationQuantity($id_product, $id_product_attribute, $id_customization, $id_cart);
                }
                if ($op == 'down') {
                    PPATCartHelper::decrementProductCustomizationQuantity($id_product, $id_product_attribute, $id_customization, $id_cart);
                }
            }
        }
    }


    /**
     * Add product along with dimensions to the cart and create the customizatiomn
     * @param $id_customization
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
	public function addToCart($id_customization)
	{
        $id_cart = $this->context->cart->id;
        $id_product = Tools::getValue('id_product');
        $id_shop = Context::getContext()->shop->id;

        $ppat_product = new PPATProductModel();
        $ppat_product->load($id_product, $id_shop);

        if ($ppat_product->enabled == false || !isset($ppat_product->enabled)) {
            return false;
        }

        if (Tools::getValue('row') == '' || Tools::getValue('col') == '') {
	        return false;
        }

		if (!$this->context->cart->id) {
			if (Context::getContext()->cookie->id_guest) {
				$guest = new Guest(Context::getContext()->cookie->id_guest);
				$this->context->cart->mobile_theme = $guest->mobile_theme;
			}
			$this->context->cart->add();
			if ($this->context->cart->id) {
				$this->context->cookie->id_cart = (int)$this->context->cart->id;
				$id_cart = (int)$this->context->cart->id;
            }
		}
		
		if (!isset($this->context->cart->id)) {
		    return false;
        }

        if (Tools::getValue('group') != '') {
            $id_product_attribute = Product::getIdProductAttributeByIdAttributes(Tools::getValue('id_product'), Tools::getValue('group'));
        } else {
            $id_product_attribute = 0;
        }

        if ((int)$id_product_attribute == 0 && (int)Tools::getValue('id_product_attribute') > 0) {
            $id_product_attribute = Tools::getValue('id_product_attribute');
        }

		$id_cart = $this->context->cart->id;		
        $quantity = Tools::getValue('qty');

		$cart_unit_collection = array();
		$ppat_units = PPATUnitModel::getUnitsList(Context::getContext()->language->id);

		if (is_array($ppat_units))
		{
			foreach ($ppat_units as $ppat_unit)
			{
				$ppat_cart_unit = new stdClass();
				$ppat_cart_unit->id_ppat_unit = $ppat_unit['id_ppat_unit'];
				$ppat_cart_unit->suffix = $ppat_unit['suffix'];
				$ppat_cart_unit->name = $ppat_unit['name'];
				$ppat_cart_unit->display_name = $ppat_unit['display_name'];
				$ppat_cart_unit->value = Tools::getValue($ppat_unit['name']);
				$ppat_cart_unit->type = $ppat_unit['type'];

				if ((float)Tools::getValue($ppat_unit['name']) != '')
					$cart_unit_collection[] = $ppat_cart_unit;
			}
		}

		if (Tools::getValue('ppat_id_option') != '') {
            $cart_unit_collection['id_option'] = Tools::getValue('ppat_id_option');
        }

        $cart_unit_collection['id_product'] = $id_product;

        if (version_compare(_PS_VERSION_, '1.7.7.0', '<')) {
            if ($id_customization == 0) {
                $id_customization = PPATCartHelper::addCustomization($id_product, $id_cart, $id_product_attribute, Context::getContext()->cart->id_address_delivery, $cart_unit_collection, $this->sibling->id, $quantity, $this->context->shop->id);
            } else {
                $display_text = PPATCartHelper::getCustomizationDisplayText($cart_unit_collection);
                PPATCartHelper::addCustomizedData($id_customization, 0, $display_text, $this->sibling->id, $cart_unit_collection);
            }
        }

        if (version_compare(_PS_VERSION_, '1.7.7.0', '>=')) {
            $quantity = 0;  // Prestashop will apply the customization quantity after add to cart
            if ($id_customization > 0) {
                $id_customization_field = PPATCartHelper::getCustomizationField($id_product, $id_shop);
                $display_text = PPATCartHelper::getCustomizationDisplayText($cart_unit_collection);
                PPATCartHelper::addCustomizedData($id_customization, $id_customization_field, $display_text, $this->sibling->id, $cart_unit_collection);
            } else {
                $id_customization = PPATCartHelper::addCustomization($id_product, $id_cart, $id_product_attribute, Context::getContext()->cart->id_address_delivery, $cart_unit_collection, $this->sibling->id, $quantity, $this->context->shop->id);
            }
        }
		return $id_customization;
	}

	private function _removeNumberFormatting($number)
	{
		if (substr_count($number, ',') == 1)
			return str_replace(',', '.', $number);
		return $number;
	}
	

	/**
	 * Return a price based on the product dimensions and option
	 */
	public function priceCalculation($params)
	{
		return $this->calculateCustomizationPrice(array(), $params, false);
	}


	/**
	 * @param $customization_data
	 * @param $params
	 * @param bool $is_customization_line
	 * @return float
	 */
	public function calculateCustomizationPrice($customization_data, $params, $is_customization_line = false)
	{
		static $address = null;
		static $context = null;

		if (empty(Context::getContext()->cart)) {
		    return false;
        }

		$group_reduction = 0;
		$id_shop = $params['id_shop'];
		$id_product = $params['id_product'];
		$id_product_attribute = $params['id_product_attribute'];
		$id_cart = (int)Context::getContext()->cart->id;
		$id_customization = (int)$params['id_customization'];
		$line_quantity = $params['quantity'];
		$currency = Context::getContext()->currency;
        $cache_id = 'PPATFrontCartController::calculateCustomizationPrice_' . $id_product . '-' . $id_product_attribute . '-' . $id_customization . '-' . $id_cart . '-' . (int)$params['use_tax'];

        if (empty($customization_data)) {
            if (Cache::isStored($cache_id)) {
                return Tools::ps_round(Cache::retrieve($cache_id), 6);
            }
        }

        if (!empty(Context::getContext()->customer->id_default_group)) {
            $id_group = Context::getContext()->customer->id_default_group;
            $group_reduction = PPATProductHelper::getGroupReduction($id_product, $id_group);
        }

        /* set up tax calculator */
		if ($address === null)
			$address = new Address();
		$address->id_country = $params['id_country'];
		$address->id_state = $params['id_state'];
		$address->postcode = $params['zipcode'];

		$tax_manager = TaxManagerFactory::getManager($address, Product::getIdTaxRulesGroupByIdProduct((int)$id_product, $context));
		$product_tax_calculator = $tax_manager->getTaxCalculator();

		$total_area = 0;
		$product = new Product($id_product, null, null, $id_shop);
		$price_base = $product->price;

		$attribute_price = PPATProductHelper::getProductAttributePrice($id_product, $id_shop, $id_product_attribute);
        if (!empty($attribute_price['attribute_price'])) {
            $attribute_price = (float)$attribute_price['attribute_price'];
        }

        if (!empty($customization_data))
			$cart_unit_collection = $customization_data;
		else
		{
			$cart_unit_collection = PPATProductHelper::getCartProductUnits(
				$params['id_product'],
				$params['id_cart'],
				$params['id_product_attribute'],
				$params['id_shop'],
				$params['id_customization']
			);
		}

		$ppat_product = new PPATProductModel();
		$ppat_product->load($id_product, $id_shop);

		if ($ppat_product->enabled == false || !isset($ppat_product->enabled)) return $params['price'];

		if (!is_array($cart_unit_collection)) return $params['price'];

		$unit_total = 0.00;

		foreach ($cart_unit_collection as $cart_units)
		{
			$line_quantity = $cart_units['quantity'];
			$cart_units = json_decode($cart_units['ppat_dimensions']);

			/* get the id_option so we know whichprice table to look at*/
			if (!empty($cart_units))
			{
				$id_option = $cart_units->id_option;
				$ppat_product_options = new PPATProductTableOptionModel($id_option);

				foreach ($cart_units as $cart_unit)
				{
					if (isset($cart_unit->id_ppat_unit) && $cart_unit->name == 'row') {
						$row = $cart_unit->value;
						$row = $this->_removeNumberFormatting($row);
					}

					if (isset($cart_unit->id_ppat_unit) && $cart_unit->name == 'col') {
						$col = $cart_unit->value;
						$col = $this->_removeNumberFormatting($col);
					}
				}


				$price = PPATProductPriceTableModel::getUnitEntryPrice($id_option, $row, $col, $ppat_product_options->lookup_rounding_mode);
				//print $price."<br>";
				$price['price'] = Tools::convertPrice($price['price'], null, $currency);

				$price_impact = '';
				if (strpos($price['price'], '+') !== false) $price_impact = '+';
				if (strpos($price['price'], '-') !== false) $price_impact = '-';

				$price['price'] = str_replace('+', '', $price['price']);
				$price['price'] = str_replace('-', '', $price['price']);

				if ($price_impact == '+')
					$price = ($price_base + $price['price'] + $attribute_price);
				elseif ($price_impact == '-')
					$price = (($price_base + $attribute_price) - $price['price']);
				else
					$price = ($price['price'] + $attribute_price);

				$unit_total += $price;
			}
		}

		if (isset($params['specific_price']))
		{
			$specific_price = $params['specific_price'];
			if (isset($specific_price['reduction']) && isset($specific_price['reduction_type']))
				$unit_total = $this->_applyDiscount($unit_total, $specific_price['reduction'], $specific_price['reduction_type']);
		}

		$unit_total = self::_addTax($product_tax_calculator, $params['use_tax'], $unit_total);		
		$unit_total = $this->_applyDiscount($unit_total, $group_reduction / 100, 'percentage');  // apply group discount
		$unit_total = Tools::ps_round($unit_total, _PS_PRICE_COMPUTE_PRECISION_);				

        Cache::store($cache_id, $unit_total);
		return Tools::ps_round($unit_total, _PS_PRICE_COMPUTE_PRECISION_);
	}

	/**
	 * Add script initialisation for cart JS
	 * @param $params
	 * @return bool
	 */
	public function hookDisplayFooter($params)
	{
		if (Context::getContext()->controller->php_self != 'cart') return false;
		$this->sibling->smarty->assign(array(
            'baseDir' => __PS_BASE_URI__,
            'ppat_module_ajax_url' => $this->module_ajax_url,
        ));
		return $this->sibling->display($this->sibling->module_file, 'views/templates/front/hook_cart_footer.tpl');
	}

	public function hookDisplayCustomization($params)
    {
        if (!empty($params['customization']['value'])) {
            return $params['customization']['value'];
        }
    }
}