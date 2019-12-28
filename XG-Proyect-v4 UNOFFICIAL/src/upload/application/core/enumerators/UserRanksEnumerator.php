<?php
/**
 * User ranks enumerator
 *
 * PHP Version 7.1+
 *
 * @category Library
 * @package  Application
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.0.4
 */
namespace application\core\enumerators;

/**
 * UserRanksEnumerator Class
 *
 * @category Enumerator
 * @package  Core
 * @author   XG Proyect Team
 * @license  http://www.xgproyect.org XG Proyect
 * @link     http://www.xgproyect.org
 * @version  3.1.0
 */
abstract class UserRanksEnumerator
{
    const PLAYER = 0;
    const GO = 1;
    const SGO = 2;
    const ADMIN = 3;
}

/* end of UserRanksEnumerator.php */
