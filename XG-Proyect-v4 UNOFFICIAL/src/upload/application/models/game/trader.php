<?php
/**
 * Trader Model
 *
 * PHP Version 7.1+
 *
 * @category Model
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
namespace application\models\game;

use application\core\Database;

/**
 * Trader Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Trader
{
    private $db = null;

    /**
     * Constructor
     *
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        // use this to make queries
        $this->db = $db;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->db->closeConnection();
    }

    /**
     * Refill planet storage and discount the needed dark matter
     *
     * @param integer $dark_matter
     * @param string $resource
     * @param float $amount
     * @param integer $user_id
     * @param integer $planet_id
     * @return void
     */
    public function refillStorage(int $dark_matter, string $resource, float $amount, int $user_id, int $planet_id): void
    {
        $this->db->query(
            "UPDATE `" . PREMIUM . "` pr, `" . PLANETS . "` p SET
            pr.`premium_dark_matter` = pr.`premium_dark_matter` - '" . $dark_matter . "',
            p.`planet_" . $resource . "` = '" . $amount . "'
            WHERE pr.`premium_user_id` = '" . $user_id . "'
                AND p.`planet_id` = '" . $planet_id . "';"
        );
    }
}

/* end of trader.php */
