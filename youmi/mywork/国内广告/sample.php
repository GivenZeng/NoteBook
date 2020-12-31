<?php
/**
 * User: zjw  zengjiwen@youmi.net
 * Date: 18-3-6
 * Time: 下午6:29
 */

chdir(dirname(__FILE__));
//require_once '../analytics/init_crond.php';
require_once '/home/ymserver/vhost/gateway/analytics/init_crond.php';

class Tools
{

    /**
     * @param url string
     * @return response
     */
    public static function sendGetRequest($url)
    {
        L('ym_request');
        var_dump($url);
        $response = YmRequest::curlGet($url);
        return $response;
    }

    /**
     * @param deviceInfo Array
     * @return Array
     */
    public static function getfurtherJobStatus($deviceInfo)
    {
        L('ymm_further_job');
        $yfj = YmmFurtherJob::getInstance();
        $atlist = $yfj->getJobData(YM_AD_PRODUCT_TYPE_WALL, $deviceInfo[1], $deviceInfo[2]);
        return $atlist;
    }

    /**生成回调链接
     * com.ruiqugames.buyuqianpao
     * @param $params
     */
    public static function genCallbackUrl($params) {
        L('wall.ymm_wall_ad');
        $ywa = YmmWallAd_::getInstance();
        static $adInfo = NULL;
        if (empty($adInfo)) {
            $adInfo = $ywa->queryWallAllDetail($params['ad']);
            $adInfo = $ywa->queryWallAdMergeExtens($adInfo);
        }

        $urlconfig = array('cid', 'ad', 'aid', 'from', 'at', 'product', 'ifa', 'package', 'ei');
        $edata = array();
        foreach($urlconfig as $key) {
            if (isset($params[$key])) {
                $edata[$key] = $params[$key];
            } else {
                $edata[$key] = '';
            }
        }
        $rsd = $params['rsd'];
        $edata['package'] = 'com.ruiqugames.buyuqianpao';

        L('ymm_param_parse');
        $ekey = YmmParamParse::getGwCryptKeyV2($rsd);
        $e = YmmParamParse::getGwEncodeStrV2(array_values($edata), $ekey);
        $s = $rsd . $e;
        return 'http://callback.api.youmi.net/v2/ad_eff?s=' . $s;
    }


    public static function sendCallbackRequest($url)
    {
        var_dump($url);
        $response = Tools::sendGetRequest($url);
        YmDebugHelper::debug_log('autonomy_further_job_cb_log', json_encode($response));
        var_dump('success send call-back url');
    }

    public static function handleException($exception, $context)
    {
        $msg = array();
        $msg['exception'] = $exception->getMessage();
        $msg['context'] = $context;
        YmDebugHelper::debug_log('autonomy_further_job_exception_log', json_encode($msg));
    }
}


/**
 * 主动查询广告主配置
 * 两种任务：主任务注册试玩3局、总金币金额达到某阈值
 * get http://qfishwx.91cb.com/api/thirt
 * 参数uid、qid=31000004、sig=md5(uid+qid+key)、type=uid/imei/username/player
 * key=dG7uEBUrzl0lAr0m
 */
class QueryAutonomy28669
{
    private $ad = '';
    private $deviceInfo = array();
    private $key = 'dG7uEBUrzl0lAr0m';
    private $qid = '31000004';
    private $winGold = 0;
    // 是否已经回调了该任务
    private $furtherJobStatus = array();
    // 是否已经查询到用户信息
    private $isQueryProperty = false;
    // 是否玩了三局以上，1表示达成条件，未达成为0
    private $playtime = 0;

    /*累计赢取金额的范围，单位：w*/
    private $totalMoneyRank = array(
        3243 => array("max" => 10),
        3244 => array("max" => 40),
        3245 => array("max" => 100),
        3246 => array("max" => 300),
        3247 => array("max" => 600),
        3248 => array("max" => 1200),
        3249 => array("max" => 3000),
        3250 => array("max" => 9000),
        3251 => array("max" => 30000),
    );

    public function __construct($deviceInfo)
    {
        $this->ad = $deviceInfo[2];
        $this->deviceInfo = $deviceInfo;
        $this->furtherJobStatus = Tools::getfurtherJobStatus($deviceInfo);
    }

    public function getAd()
    {
        return $this->ad;
    }

    public function getPlayer()
    {
        if ($this->isQueryProperty) {
            return;
        }
        // 构造请求url
        $imei = $this->deviceInfo[0];
        $sign = md5($imei . $this->qid . $this->key);
        $url = "http://qfishwx.91cb.com/api/thirt?uid={$imei}&sign={$sign}&qid={$this->qid}&type=imei";
        var_dump($url);

        // 解析返回数据
        $response = Tools::sendGetRequest($url);
        $data = json_decode($response['output'], true);
        var_dump($data);
        if (isset($data['status']) && 1 == intval($data['status'])) {
            $data = $data['Result'];
            if (isset($data['Wingold'])) {
                $this->winGold = $data['Wingold'];
            } else {
                // throw new Exception("failed to get wingold");
            }

            if (!empty($data['PlayTime'])) {
                $this->playtime = intval($data['PlayTime']);
            } else {
                // throw new Exception("failed to get playtime");
            }

        } else {
            throw new Exception("no player matches imei");
        }
        $this->isQueryProperty = true;
        return $data;
    }

    public function callbackFurtherJob()
    {
        $ats = $this->deviceInfo[7];
        foreach ($ats as $at) {
            // 已回调过，不再回调此动作
            if (isset($this->furtherJobStatus[$at])) {
                continue;
            }
            $params = array(
                'ei' => $this->deviceInfo[0],
                'cid' => $this->deviceInfo[1],
                'ad' => $this->getAd(),
                'aid' => $this->deviceInfo[3],
                'from' => $this->deviceInfo[4],
                'at' => $at,
                'product' => $this->deviceInfo[5],
                'rsd' => $this->deviceInfo[6],
            );
            $this->getPlayer();    // 主任务及深度任务都需先获取用户基本信息

            if (YM_AD_EFF_TYPE_REG == $at) {
                /* 使用手机号码注册并且试玩三局,playtime=1 表示已经连玩三局了*/
                if ($this->playtime == 1) {
                    $callbackUrl = Tools::genCallbackUrl($params);
                    Tools::sendCallbackRequest($callbackUrl);
                } else {
                    throw new Exception("no enough win gold");
                }
            } else {
                /* 根据所赢取的money判断所在深度任务是否有效 */
                if (isset($this->totalMoneyRank[$at])) {
                    $rank = $this->totalMoneyRank[$at];
                    if ($this->winGold / 10000 >= $rank['max']) {
                        YmDebugHelper::debug_log('auto_query_debug_28669_log', "28669 game:{$at}");
                        $callbackUrl = Tools::genCallbackUrl($params);
                        Tools::sendCallbackRequest($callbackUrl);
                    }
                }
            }
        }
    }
}

/**
 * 从日志查询设备信息和任务信息
 */
class CMQueryAutonomyFurtherJob
{
    private $autonomyQueryAds = array("28669" => YM_AD_EFF_TYPE_REG);


    /**
     * @param  $ad string：wadid
     * @return Array
     */
    public function batchGetDeviceKeys($ad, $primaryTask = YM_AD_EFF_TYPE_REG)
    {
        var_dump('wadid=' . $ad);
        $start = date("Ymd");
        $interval = 7;
        $imeis = array();
        $keys = array();
        $keyParams = array('ei', 'cid', 'ad', 'aid', 'from', 'product', 'rsd');

        L('ymm_further_job');
        $yfj = YmmFurtherJob::getInstance();
        // 遍历过去七天的日志
        for ($i = 0; $i < $interval; $i++) {
            $date = date('Ymd', strtotime($start) - $i * 86400);
            $fileNames = glob("/data1/bak/wall/v3/ac201/{$date}/2*");
            //$fileNames = glob("/home/ymserver/tmp/zengjiwen/autonomy_query_further_job/{$date}/2*");
            // 遍历每个文件，选出keyParms中的键
            foreach ($fileNames as $name) {
                var_dump($name);
                // 打开文件，由不同种类的文件，有gz文件和普通文件
                if (preg_match('/[\w\W]*gz/', $name)) {
                    $content = gzfile($name);
                } else {
                    $content = file($name, FILE_IGNORE_NEW_LINES);
                }
                // 遍历每一行
                foreach ($content as $line) {
                    parse_str($line, $initialization);

                    // 主任务回调类型上报安装效果时积分墙会报EC_ACTION_FAILED
                    if ($ad == $initialization['wadid'] && YM_AD_EFF_TYPE_INSTALL == $initialization['at'] && YM_AD_PRODUCT_TYPE_WALL == $initialization['pd'] && EC_ACTION_FAILED == $initialization['code']) {
                        if (in_array($initialization['ei'], $imeis)) {
                            continue;
                        }

                        $imeis[] = $initialization['ei'];

                        $jobs = $yfj->queryCidAdJobs(YM_AD_PRODUCT_TYPE_WALL, $initialization['wadid'], $initialization['cid']);
                        /* 主任务及深度任务完成情况自主查询 */
                        $ats = array();
                        if (!empty($jobs)) {
                            // 查询是否有深度任务可做
                            foreach ($jobs as $job) {
                                if (!in_array($job['mode'], array(YM_AD_EFF_TYPE_MODE_PREDEFINED, YM_AD_EFF_TYPE_MODE_SIGNIN)) && JOB_STATUS_STARTING == $job['status']) {
                                    $ats[] = coverPayType2ActionType($job['pay_type']);
                                }
                            }
                        }

                        if (empty($ats)) {
                            $ats[] = $primaryTask;    // 未回调主任务下深度任务为空，所以需先回调主任务
                        }

                        if (!empty($ats)) {
                            $rsd = YmString::getRandStr(10);
                            $initialization['rsd'] = $rsd;
                            $initialization['from'] = API_AD_TYPE_WALL;
                            $initialization['ad'] = $initialization['wadid'];
                            $initialization['product'] = $initialization['pd'];

                            $edata = array();
                            foreach ($keyParams as $key) {
                                if (isset($initialization[$key])) {
                                    $edata[$key] = $initialization[$key];
                                } else {
                                    $edata[$key] = "";
                                }
                            }
                        }
                        $at = implode(",", $ats);
                        $key = "{$edata['ei']}:{$edata['cid']}:{$edata['ad']}:{$edata['aid']}:{$edata['from']}:{$edata['product']}:{$edata['rsd']}:{$at}";
                        $keys[] = $key;
                    }
                }
            }
        }
        YmDebugHelper::debug_log('auto_query_debug_log', json_encode($keys));
        return $keys;
    }

    public function callbackFurtherJob($configClass)
    {
        $callbackConfig = array(
            "28669" => function () use ($configClass) {
                $configClass->callbackFurtherJob();
            },
        );

        $ad = $configClass->getAd();
        if (isset($callbackConfig[$ad])) {
            $closure = $callbackConfig[$ad];
            $closure();
        }
    }

    public function run()
    {
        foreach ($this->autonomyQueryAds as $ad => $primaryTask) {
            $keys = $this->batchGetDeviceKeys($ad, $primaryTask);
            foreach ($keys as $key) {
                $deviceInfo = explode(":", $key);

                // 将深度任务动作序列字符串转为数组,并排序
                $deviceInfo[7] = explode(",", $deviceInfo[7]);
                sort($deviceInfo[7]);

                $className = "QueryAutonomy{$ad}";
                $configClass = new $className($deviceInfo);
                try {
                    $this->callbackFurtherJob($configClass);
                } catch (Exception $ex) {
                    Tools::handleException($ex, $key);
                }
            }
        }
    }
}

$cr = new CMQueryAutonomyFurtherJob();
$cr->run();

