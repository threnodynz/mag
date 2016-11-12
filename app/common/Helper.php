<?php
namespace Common;


class Helper {
    const STATE_ALIVE = 1;
    const STATE_DEAD = 0;
    const STATE_CANNOT_WALK = 2;

    private static $animalStates = array(
        self::STATE_ALIVE       => 'Alive',
        self::STATE_DEAD        => 'Dead',
        self::STATE_CANNOT_WALK => 'Can\'t Walk'
    );

    public static function getFormattedState($state) {
        return self::$animalStates[$state];
    }

}