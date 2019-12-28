<?php
/**
 * Officier Model
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
 * Officier Class
 *
 * @category Classes
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
class Officier
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
     * Set premium access to the current user
     *
     * @param integer $user_id
     * @param integer $price
     * @param string $officier
     * @param integer $time_to_add
     * @return void
     */
    public function setPremium(int $user_id, int $price, string $officier, int $time_to_add): void
    {
        if ($user_id > 0) {
            $this->db->query(
                "UPDATE `" . PREMIUM . "` SET
                    `premium_dark_matter` = `premium_dark_matter` - '" . $price . "',
                    `" . $officier . "` = '" . $time_to_add . "'
                WHERE `premium_user_id` = '" . $user_id . "';"
            );
        }
    }
}

/* end of officier.php */
