<?php
/**
 * Created by PhpStorm.
 * User: youyaimac
 * Date: 2015/02/19
 * Time: 23:32
 */

class fTag{

    const kFATAL   = 'app.fatal';

    const kERR     = 'app.err';

    const kWARN    = 'app.warn';

    const kDEBUG   = 'app.debug';

    const kINFO    = 'app.info';


}


class pKey{

    const kAPPTYPE    = 'app_type';//アプリの種別 assigner か fetcher

    const kTYPE       = 'type';//ログの種別

    const kAPPID      = 'app_id';

    const kMSG        = 'msg';//短い文言

    const kDESC       = 'desc';//詳細な説明

    const kACTION     = 'action';

    const kCLASS      = 'class';

    const kLINE       = 'line';

    const kFILE       = 'file';

    const kPARAMS     = 'params';

    const kID         = 'id';

    const kDATETIME   = 'datetime';

}


class pVal{

    const kAPPTYPE_ASSIGNER    = 'assigner';

    const kAPPTYPE_FETCHER     = 'fetcher';

    const kTYPE_PROCESS        = 'proc';

    const kTYPE_PROCESS_ITEM   = 'proc_item';

    const kTYPE_ERR            = 'err';

    const kTYPE_ERR_DB         = 'err_db';

    const kTYPE_ERR_INVALID_PARAMS  = 'err_invalid_params';
}
