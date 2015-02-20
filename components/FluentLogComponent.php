<?php
/**
 * Created by PhpStorm.
 * User: youyaimac
 * Date: 2015/01/28
 * Time: 20:39
 */

namespace app\components;

use Fluent\Logger\FluentLogger;
use Yii;
use yii\base\Component;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use fluent\logger;

class FluentLogComponent extends Component
{
    public $host;

    public $port;

    public function init()
    {
        parent::init();
    }

    public function put($tag, array $data)
    {
        $logger = new FluentLogger($this->host, $this->port);

        $data[\pKey::kDATETIME] = date("Y-m-d H:i:s");

        $logger->post($tag, $data);
    }

    public function assignerErr(array $data)
    {
        $data[\pKey::kAPPTYPE] = \pVal::kAPPTYPE_ASSIGNER;
        $this->put(\fTag::kERR, $data);
    }

    public function assignerInfo(array $data)
    {
        $data[\pKey::kAPPTYPE] = \pVal::kAPPTYPE_ASSIGNER;
        $this->put(\fTag::kINFO, $data);
    }

    public function assignerDebug(array $data)
    {
        $data[\pKey::kAPPTYPE] = \pVal::kAPPTYPE_ASSIGNER;
        $this->put(\fTag::kDEBUG, $data);
    }




    public function fetcherErr(array $data)
    {
        $data[\pKey::kAPPTYPE] = \pVal::kAPPTYPE_FETCHER;
        $this->put(\fTag::kERR, $data);
    }

    public function fetcherInfo(array $data)
    {
        $data[\pKey::kAPPTYPE] = \pVal::kAPPTYPE_FETCHER;
        $this->put(\fTag::kINFO, $data);
    }

    public function fetcherDebug(array $data)
    {
        $data[\pKey::kAPPTYPE] = \pVal::kAPPTYPE_FETCHER;
        $this->put(\fTag::kDEBUG, $data);
    }

    public function fetcherWarning(array $data)
    {
        $data[\pKey::kAPPTYPE] = \pVal::kAPPTYPE_FETCHER;
        $this->put(\fTag::kWARN, $data);
    }


    public function fetcherFatal(array $data)
    {
        $data[\pKey::kAPPTYPE] = \pVal::kAPPTYPE_FETCHER;
        $this->put(\fTag::kFATAL, $data);
    }








}