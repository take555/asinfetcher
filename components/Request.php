<?php
/**
 * Created by PhpStorm.
 * User: youyaimac
 * Date: 2015/02/03
 * Time: 13:44
 */

namespace app\components;

use Yii;

class Request extends \yii\web\Request{

    public $noCsrfRoutes = [];

    public function validateCsrfToken()
    {
        if(
            $this->enableCsrfValidation &&
            in_array(Yii::$app->getUrlManager()->parseRequest($this)[0],
            $this->noCsrfRoutes)
        ){
            return true;
        }

        return parent::validateCsrfToken();

    }


}