<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App;

/**
 * Description of Util
 *
 * @author elderjr
 */
class Util {

    public static function generateFeedbackObject() {
        return (object) array(
                    'success' => null,
                    'alert' => null,
                    'error' => null
        );
    }

}
