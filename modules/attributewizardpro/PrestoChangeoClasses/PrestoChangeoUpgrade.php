<?php
/**
 * 2008 - 2018 Presto-Changeo
 *
 * MODULE USPS
 *
 * @author    Presto-Changeo <info@presto-changeo.com>
 * @copyright Copyright (c) permanent, Presto-Changeo
 * @license   Addons PrestaShop license limitation
 * @version   2.0.0
 * @link      http://www.presto-changeo.com
 *
 * NOTICE OF LICENSE
 *
 * Don't use this module on several shops. The license provided by PrestaShop Addons
 * for all its modules is valid only once for a single shop.
 */

class PrestoChangeoUpgrade
{

    protected $context = '';
    public $smarty = '';
    public $module = NULL;
    public $version = NULL;
    public $path = NULL;
    protected $_last_updated;
    protected $_full_version;
    protected $pcURL = ' https://www.presto-changeo.com/en/contact_us';

    /**
     * Add to contructor instance of context
     */
    public function __construct($module, $path, $full_version)
    {

        $this->module = $module;
        $this->path = $path;
        $this->_full_version = $full_version;
        $this->version = $module->version;
        $this->context = Context::getContext();
        $this->smarty = $this->context->smarty;
        $this->_last_updated = Configuration::get('PRESTO_CHANGEO_UC');
    }

    public function getPCURL()
    {
        return $this->pcURL;
    }

    /**
     * get version of PrestaShop
     * return float value version
     */
    protected function getPSV()
    {
        return (float) (substr($this->getRawPSV(), 0, 3));
    }

    /**
     * get raw version of PrestaShop
     */
    private function getRawPSV()
    {
        return _PS_VERSION_;
    }

    public function getContactUsOnlyLink()
    {
        return 'https://www.presto-changeo.com/en/contact_us';
    }

    public function getPrestoChangeoLogo()
    {
        return '<img src="https://updates.presto-changeo.com/logo.jpg" border="0" /></a>';
    }

    public function displayUpgradeCheck($module)
    {
        $html = '';

        $module = $module . '2';

        if ($url = $this->upgradeCheck($module))
            $html .= ($this->getPSV() >= 1.6 ? '<div class="panel">' :
                    '<fieldset class="width3">') .
                    ($this->getPSV() < 1.6 ? '<legend>' : '<h3>') .
                    '' . $this->module->l('New version available') .
                    '<span class="upgrade_check_icon"> </span>' .
                    ($this->getPSV() < 1.6 ? '</legend>' : '</h3>') .
                    '<div class="upgrade_check_content">' .
                    (isset($url['message']) && !empty($url['message']) ?
                    $url['message'] :
                    $this->module->l('We have released a new version of the module. For a list of new features, improvements and bug fixes, view the ') . '<a href="' . $url['url'] . '#change" target="_index">' . $this->module->l('Change Log') . '</a> ' . $this->module->l('on our site.') . '
			<br /><br />' .
                    $this->module->l('For real-time alerts about module updates, be sure to join us on our') . ' <a href="http://www.facebook.com/pages/Presto-Changeo/333091712684" target="_index">Facebook</a> / <a href="http://twitter.com/prestochangeo1" target="_index">Twitter</a> ' . $this->module->l('pages') . '.
			<br /><br />' .
                    $this->module->l('Please') . ' <a href="https://www.presto-changeo.com/en/contact_us" target="_index">' . $this->module->l('contact us') . '</a> ' . $this->module->l('to request an upgrade to the latest version.') .
                    '<br/><br/><br/><br/>'
                    . '<a class="upgrade_now_btn" href="https://www.presto-changeo.com/en/contact_us" target="_index">' . $this->module->l('Upgrade now') . '</a>'

                    ) .
                    '</div>' .
                    ($this->getPSV() < 1.6 ? '</fieldset>' : '</div>') . '
			<br />';
        return $html;
    }

    /**
     *  Does module need updating
     */
    public function upgradeCheck($module)
    {
        // Only run upgrae check if module is loaded in the backoffice.
        //if ((!is_object($this->context->cookie) || !$this->context->cookie->isLoggedBack()))
        //    return;


        if (!isset($this->context->employee) || !$this->context->employee->isLoggedBack())
            return;


        $returnArr = array();

        // Get Presto-Changeo's module version info
        $mod_info_str = Configuration::get('PRESTO_CHANGEO_SV');
        if (!function_exists('json_decode')) {
            if (!file_exists(dirname(__FILE__) . '/JSON.php'))
                return false;
            include_once(dirname(__FILE__) . '/JSON.php');
            $j = new JSON();
            $mod_info = $j->unserialize($mod_info_str);
        } else
            $mod_info = json_decode($mod_info_str);
        // Get last update time.
        $time = time();
        // If not set, assign it the current time, and skip the check for the next 7 days.
        if ($this->_last_updated <= 0) {
            Configuration::updateValue('PRESTO_CHANGEO_UC', $time);
            $this->_last_updated = $time;
        }

        // If haven't checked in the last 1-7+ days
        $update_frequency = max(86400, isset($mod_info->{$module}->{'T'}) ? $mod_info->{$module}->{'T'} : 86400);

        if ($this->_last_updated < $time - $update_frequency) {


            // If server version number exists and is different that current version, return URL
            if (isset($mod_info->{$module}->{'V'}) && $mod_info->{$module}->{'V'} > $this->_full_version) {
                $returnArr['url'] = $mod_info->{$module}->{'U'};
                $returnArr['message'] = (isset($mod_info->{$module}->{'M'}) ? $mod_info->{$module}->{'M'} : '');
                return $returnArr;
            }
            $url = 'http://updates.presto-changeo.com/?module_info=' . $module . '_' . $this->version . '_' . $this->_last_updated . '_' . $time . '_' . $update_frequency;
            $mod = @file_get_contents($url);
            if ($mod == '' && function_exists('curl_init')) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $mod = curl_exec($ch);
            }
            Configuration::updateValue('PRESTO_CHANGEO_UC', $time);
            $this->_last_updated = $time;
            if (!function_exists('json_decode')) {
                $j = new JSON();
                $mod_info = $j->unserialize($mod);
            } else
                $mod_info = json_decode($mod);
            if (!isset($mod_info->{$module}->{'V'}))
                return false;
            if (Validate::isCleanHtml($mod))
                Configuration::updateValue('PRESTO_CHANGEO_SV', $mod);
            if ($mod_info->{$module}->{'V'} > $this->_full_version) {
                $returnArr['url'] = $mod_info->{$module}->{'U'};
                $returnArr['message'] = (isset($mod_info->{$module}->{'M'}) ? $mod_info->{$module}->{'M'} : '');
                return $returnArr;
                //return $mod_info->{$module}->{'U'};
            } else
                return false;
        }
        elseif (isset($mod_info->{$module}->{'V'}) && $mod_info->{$module}->{'V'} > $this->_full_version) {
            $returnArr['url'] = $mod_info->{$module}->{'U'};
            $returnArr['message'] = (isset($mod_info->{$module}->{'M'}) ? $mod_info->{$module}->{'M'} : '');
            return false;
        } else
            return false;
    }

    public function getModuleRecommendations($module)
    {
        $arr = unserialize(Configuration::get('PC_RECOMMENDED_LIST'));
        // Get a new recommended module list every 10 days //
        if (!is_array($arr) || sizeof($arr) == 0 || Configuration::get('PC_RECOMMENDED_LAST') < time() - 864000) {
            $url = 'http://updates.presto-changeo.com/recommended.php';
            $str = @file_get_contents($url);
            if ($str == '' && function_exists('curl_init')) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $str = curl_exec($ch);
            }
            Configuration::updateValue('PC_RECOMMENDED_LIST', $str);
            Configuration::updateValue('PC_RECOMMENDED_LAST', time());
            $arr = unserialize($str);
        }


        $newArr = array();
        foreach ($arr as $newModule) {
            if (isset($newModule['price']) && !empty($newModule['price'])) {
                $newArr[] = $newModule;
            }
        }


        $arr = $newArr;



        if (isset($newArr) && !empty($newArr)) {

            $dupe = false;

            $i = 4;
            if (count($newArr) < 4)
                $i = count($newArr);

            shuffle($newArr);
            $rand = array_keys($newArr);

            $arr = $newArr;

            $out = '<div class="module_recommandations_block">
					<div>

						<div>
							<div class="module_recommandations_title">
								<div>' . $this->module->l('Explore') . ' ' . $this->module->l('Our') . ' ' . $this->module->l('Modules') . '</div>
							</div>

                            <div class="clear"></div>';

            $j = 0;

            for ($j = 0; $j < 6; $j++) {
                // Make sure to exclude the current module //
                if ($arr[$rand[$j]]['code'] == $module)
                    $dupe = true;


                $i = $rand[$dupe ? $j + 1 : $j];
                $out .= '
							<div class="module_recommandations_block_content ' . $j . ($j % 2 == 1 ? ' no_right_border' : '') . '' . ($j > 1 ? ' ' : '') . '">
								<div class="recommandations_product_image">
									<a target="_index" href="' . $arr[$i]['url'] . '">
										<img border="0" src="' . $arr[$i]['img'] . '" width="80" height="80" />
									</a>
								</div>
                                <div class="recommandations_product_buy">
                                    <div class="recommandations_product_name">
                                        <a  target="_index" href="' . $arr[$i]['url'] . '">
                                            ' . $arr[$i]['name'] . '
                                        </a>
                                    </div>
                                    <div class="recommandations_buy_block">
                                        <div class="recommandations_price">' . (isset($arr[$i]['price']) && !empty($arr[$i]['price']) ? '$' . $arr[$i]['price'] : '' ) . '</div>
                                        <div class="recommandations_buy"><a href="' . $arr[$i]['url'] . '">Buy</a></div>
                                    </div>
                                </div>
							</div>';
            }
            $out .= '
						<div class="clear"></div>
						</div>
					</div>
				</div>';
            return $out;
        } else {
            return '';
        }
    }

}
