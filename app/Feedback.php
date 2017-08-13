<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App;

/**
 * Description of Feedback
 *
 * @author matheus
 */
class Feedback {
    
    public $success;
    public $alert;
    public $error;
    
   function __construct() {
       $this->success = null;
       $this->alert = null;
       $this->error = null;
   }
   
   public static function feedbackWithErrors($errors){
       $feedback = new Feedback();
       $feedback->error = "";
       foreach($errors as $msg){
            $feedback->error = $feedback->error. $msg . "\n";
       }
       return $feedback;
   }
}
