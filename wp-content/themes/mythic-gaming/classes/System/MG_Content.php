<?php

namespace Mythic_Gaming\System;

use Mythic_Core\Utils\MC_Server;

/**
 * Class MG_Content
 *
 * @package Mythic_Core\System
 */
class MG_Content {

    /**
     * @return bool
     */
    public static function isMythicFrames() {
        return MC_Server::primaryPath() == 'mythic-frames';
    }

}