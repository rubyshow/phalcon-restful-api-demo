<?php 
use Phalcon\Mvc\Model,
    Phalcon\Mvc\Model\Message,
    Phalcon\Mvc\Model\Validator\InclusionIn,
    Phalcon\Mvc\Model\Validator\Uniqueness;

class Users extends Model{

  public function validation(){

    if($this->validationHasFailed() == true){
      return false;
    }
  }


}




?>
