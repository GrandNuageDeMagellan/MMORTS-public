<?php
/**
 * Developments Library
 *
 * PHP Version 7.1+
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
namespace application\libraries;

use application\core\XGPCore;

/**
 * DevelopmentsLib Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.0
 */
class DevelopmentsLib extends XGPCore
{

    /**
     * setBuildingPage
     *
     * @param int $element Element
     *
     * @return string
     */
    public static function setBuildingPage($element)
    {
        $resources_array = [1, 2, 3, 4, 12, 22, 23, 24];
        $station_array = [14, 15, 21, 31, 33, 34, 44];

        if (in_array($element, $resources_array)) {
            return 'resources';
        }

        if (in_array($element, $station_array)) {
            return 'station';
        }

        // IN CASE THE ELEMENT DOESN'T EXISTS
        return 'overview';
    }

    /**
     * maxFields
     *
     * @param array $current_planet Current planet
     *
     * @return void
     */
    public static function maxFields($current_planet)
    {
        return $current_planet['planet_field_max'] + (
            $current_planet[parent::$objects->getObjects(33)] * FIELDS_BY_TERRAFORMER
        );
    }

    /**
     * developmentPrice
     *
     * @param array   $current_user   Current user
     * @param array   $current_planet Current planet
     * @param string  $element        Element
     * @param boolean $incremental    Incremental
     * @param boolean $destroy        Destroy
     *
     * @return int
     */
    public static function developmentPrice($current_user, $current_planet, $element, $incremental = true, $destroy = false)
    {
        $resource = parent::$objects->getObjects();
        $pricelist = parent::$objects->getPrice();

        if ($incremental) {
            $level = (isset($current_planet[$resource[$element]])) ? $current_planet[$resource[$element]] : $current_user[$resource[$element]];
        }

        $array = ['metal', 'crystal', 'deuterium', 'energy_max'];

        foreach ($array as $res_type) {
            if (isset($pricelist[$element][$res_type])) {
                if ($incremental) {
                    if ($element == 124) {
                        $cost[$res_type] = round(
                            ($pricelist[$element][$res_type] * pow($pricelist[$element]['factor'], $level)) / 100
                        ) * 100;
                    } else {
                        $cost[$res_type] = floor(
                            $pricelist[$element][$res_type] * pow($pricelist[$element]['factor'], $level)
                        );
                    }
                } else {
                    $cost[$res_type] = floor($pricelist[$element][$res_type]);
                }

                if ($destroy == true) {
                    $cost[$res_type] = floor($cost[$res_type] / 4);
                }
            }
        }

        return $cost;
    }

    /**
     * isDevelopmentPayable
     *
     * @param array   $current_user   Current user
     * @param array   $current_planet Current planet
     * @param string  $element        Element
     * @param boolean $incremental    Incremental
     * @param boolean $destroy        Destroy
     *
     * @return boolean
     */
    public static function isDevelopmentPayable(
        $current_user, $current_planet, $element, $incremental = true, $destroy = false
    ) {

        $return = true;
        $costs = self::developmentPrice($current_user, $current_planet, $element, $incremental, $destroy);

        foreach ($costs as $resource => $amount) {
            if ($costs[$resource] > $current_planet['planet_' . $resource]) {
                $return = false;
            }
        }

        return $return;
    }

    /**
     * formatedDevelopmentPrice
     *
     * @param array   $current_user   Current user
     * @param array   $current_planet Current planet
     * @param string  $element        Element
     * @param boolean $userfactor     User factor
     * @param boolean $level          Level
     *
     * @return string
     */
    public static function formatedDevelopmentPrice(
        $current_user, $current_planet, $element, $userfactor = true, $level = false
    ) {
        $resource = parent::$objects->getObjects();
        $pricelist = parent::$objects->getPrice();
        $lang = parent::$lang;

        if ($userfactor && ($level === false)) {

            $level = (isset($current_planet[$resource[$element]])) ? $current_planet[$resource[$element]] : $current_user[$resource[$element]];
        }

        $is_buyeable = true;
        $text = $lang['fgp_require'];
        $array = [
            'metal' => $lang['Metal'],
            'crystal' => $lang['Crystal'],
            'deuterium' => $lang['Deuterium'],
            'energy_max' => $lang['Energy'],
        ];

        foreach ($array as $res_type => $ResTitle) {

            if (isset($pricelist[$element][$res_type]) && $pricelist[$element][$res_type] != 0) {

                $text .= $ResTitle . ": ";

                if ($userfactor) {
                    if ($element == 124) {
                        $cost = round(
                            ($pricelist[$element][$res_type] * pow($pricelist[$element]['factor'], $level)) / 100
                        ) * 100;
                    } else {
                        $cost = floor(
                            $pricelist[$element][$res_type] * pow($pricelist[$element]['factor'], $level)
                        );
                    }
                } else {
                    $cost = floor($pricelist[$element][$res_type]);
                }

                if ($cost > $current_planet['planet_' . $res_type]) {
                    $text .= "<b style=\"color:red;\"> <t title=\"-" . FormatLib::prettyNumber(
                        $cost - $current_planet['planet_' . $res_type]
                    ) . "\">";
                    $text .= "<span class=\"noresources\">" . FormatLib::prettyNumber($cost) . "</span></t></b> ";
                    $is_buyeable = false;
                } else {

                    $text .= "<b style=\"color:lime;\">" . FormatLib::prettyNumber($cost) . "</b> ";
                }
            }
        }

        return $text;
    }

    /**
     * developmentTime
     *
     * @param array   $current_user    Current user
     * @param array   $current_planet  Current planet
     * @param string  $element         Element
     * @param boolean $level           Level
     * @param int     $total_lab_level Total lab level
     *
     * @return int
     */
    public static function developmentTime(
        $current_user, $current_planet, $element, $level = false, $total_lab_level = 0
    ) {
        $resource = parent::$objects->getObjects();
        $pricelist = parent::$objects->getPrice();
        $reslist = parent::$objects->getObjectsList();

        // IF ROUTINE FIX BY JSTAR
        if ($level === false) {
            $level = (isset($current_planet[$resource[$element]])) ? $current_planet[$resource[$element]] : $current_user[$resource[$element]];
        }

        if (in_array($element, $reslist['build'])) {

            $cost_metal = floor($pricelist[$element]['metal'] * pow($pricelist[$element]['factor'], $level));
            $cost_crystal = floor($pricelist[$element]['crystal'] * pow($pricelist[$element]['factor'], $level));
            $time = (($cost_crystal + $cost_metal) / FunctionsLib::readConfig('game_speed')) * (1 / ($current_planet[$resource['14']] + 1)) * pow(0.5, $current_planet[$resource['15']]);
            $time = floor(($time * 60 * 60));
        } elseif (in_array($element, $reslist['tech'])) {

            $cost_metal = floor($pricelist[$element]['metal'] * pow($pricelist[$element]['factor'], $level));
            $cost_crystal = floor($pricelist[$element]['crystal'] * pow($pricelist[$element]['factor'], $level));
            $intergal_lab = $current_user[$resource[123]];

            if ($intergal_lab < 1) {
                $lablevel = $current_planet[$resource['31']];
            } else {
                $lablevel = $total_lab_level;
            }

            $time = (($cost_metal + $cost_crystal) / FunctionsLib::readConfig('game_speed')) / (($lablevel + 1) * 2);
            $time = floor(
                ($time * 60 * 60) * (1 - ((OfficiersLib::isOfficierActive(
                    $current_user['premium_officier_technocrat']
                )) ? TECHNOCRATE_SPEED : 0))
            );
        } elseif (in_array($element, $reslist['defense'])) {

            $time = (($pricelist[$element]['metal'] + $pricelist[$element]['crystal']) / FunctionsLib::readConfig('game_speed')) * (1 / ($current_planet[$resource['21']] + 1)) * pow(1 / 2, $current_planet[$resource['15']]);
            $time = floor(($time * 60 * 60));
        } elseif (in_array($element, $reslist['fleet'])) {
            $time = (($pricelist[$element]['metal'] + $pricelist[$element]['crystal']) / FunctionsLib::readConfig('game_speed')) * (1 / ($current_planet[$resource['21']] + 1)) * pow(1 / 2, $current_planet[$resource['15']]);
            $time = floor(($time * 60 * 60));
        }

        if ($time < 1) {
            $time = 1;
        }

        return $time;
    }

    /**
     * formatedDevelopmentTime
     *
     * @param int $time Time
     *
     * @return string
     */
    public static function formatedDevelopmentTime($time)
    {
        return "<br>" . parent::$lang['fgf_time'] . FormatLib::prettyTime($time);
    }

    /**
     * isDevelopmentAllowed
     *
     * @param array $current_user   Current user
     * @param array $current_planet Current planet
     * @param string $element       Element
     *
     * @return boolean
     */
    public static function isDevelopmentAllowed($current_user, $current_planet, $element)
    {
        $resource = parent::$objects->getObjects();
        $requeriments = parent::$objects->getRelations();

        if (isset($requeriments[$element])) {
            $enabled = true;

            foreach ($requeriments[$element] as $ReqElement => $EleLevel) {
                if (isset($current_user[$resource[$ReqElement]]) && $current_user[$resource[$ReqElement]] >= $EleLevel) {
                    $enabled = true;
                } elseif (isset($current_planet[$resource[$ReqElement]]) && $current_planet[$resource[$ReqElement]] >= $EleLevel) {
                    $enabled = true;
                } else {
                    return false;
                }
            }

            return $enabled;
        } else {
            return true;
        }
    }

    /**
     * currentBuilding
     *
     * @param string $call_program Call program
     * @param int    $element_id   Element ID
     *
     * @return string
     */
    public static function currentBuilding($call_program, $element_id = 0)
    {
        $parse = parent::$lang;

        $parse['call_program'] = $call_program;
        $parse['current_page'] = ($element_id != 0) ? DevelopmentsLib::setBuildingPage($element_id) : $call_program;

        return parent::$page->parseTemplate(parent::$page->getTemplate('buildings/buildings_buildlist_script'), $parse);
    }

    /**
     * setLevelFormat
     *
     * @param int    $level        Level
     * @param string $element      Element
     * @param string $current_user Current user
     *
     * @return void
     */
    public static function setLevelFormat($level, $element = '', $current_user = '')
    {
        $return_level = '';

        // check if is base level
        if ($level != 0) {

            $return_level = ' (' . parent::$lang['bd_lvl'] . ' ' . $level . ')';
        }

        // check a commander plus
        switch ($element) {
            case 106:
                if (OfficiersLib::isOfficierActive($current_user['premium_officier_technocrat'])) {

                    $return_level .= FormatLib::strongText(
                        FormatLib::colorGreen(' +' . TECHNOCRATE_SPY . parent::$lang['bd_spy'])
                    );
                }

                break;

            case 108:
                if (OfficiersLib::isOfficierActive($current_user['premium_officier_admiral'])) {

                    $return_level .= FormatLib::strongText(
                        FormatLib::colorGreen(' +' . AMIRAL . parent::$lang['bd_commander'])
                    );
                }

                break;
        }

        return $return_level;
    }

    /**
     * isLabWorking
     *
     * @param array $current_user Current user
     *
     * @return boolean
     */
    public static function isLabWorking($current_user)
    {
        return ($current_user['research_current_research'] != 0);
    }

    /**
     * isShipyardWorking
     *
     * @param array $current_planet Current planet
     *
     * @return boolean
     */
    public static function isShipyardWorking($current_planet)
    {
        return ($current_planet['planet_b_hangar'] != 0);
    }

    /**
     * Check if there are any fields available
     *
     * @param type $current_planet
     *
     * @return boolean
     */
    public static function areFieldsAvailable($current_planet)
    {
        if ($current_planet['planet_field_current'] < self::maxFields($current_planet)
        ) {

            return true;
        } else {

            return false;
        }
    }
}

/* end of DevelopmentsLib.php */
