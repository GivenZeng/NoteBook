### api_now


#### 更新广告状态

```
// 当前正在跑的广告，并且需要新插入
    YmmApiNowUtil::updateAdsStatusByAdapi($ctids_to_be_archived, 'ARCHIVED', array(YM_AD_STATUS_RUNNING), $this->config['queue']);
    YmmApiNowUtil::updateAdsStatusByAdapi($ctids_to_be_running,  'ACTIVE', array(YM_AD_STATUS_ARCHIVE), $this->config['queue']);
```
