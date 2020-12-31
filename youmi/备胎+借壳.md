备胎、别名计划

backup_ads: oid、backup_oid、aid、type（bak|shl|vest...）

### 备胎
如果广告$oid在某渠道$aid 表现很好（ecpc、ecpm、revenue、cov高），渠道$aid就会频繁展示、点击这些广告以提升收益。如果该广告$oid下线了，那么就会产生无效点击，或者渠道将不再下放、展示、点击该广告。

为了让这些效果好的广告能够继续吸收点击，因此有了备胎计划：
- 当效果好的广告$oid下线后，找一个同报名、同国家的广告$backup_oid顶替之：往backup_ads里面插入记录($aid, $oid, $backup_id, type=bak)
- 当渠道$aid再拉取广告时，会将备胎广告返回给它，返回的广告结构如下，详情见adfetchy/adfetch.go的doFilter函数
    ```
    {
        oid: $oid,
        tracking_link: http://click_domain/path?s=xxxxxxx   // s=genS($oid, $aid,...),
        ...
    }
    ```
- 当渠道点击tracking_link，adredirect会根据s串里面的信息，解析出oid，然后查询backup_oids表（where aid=$aid and oid=$oid），发现如果是备胎广告，就会跳转到一条oid=200580的sml 

因此，在渠道看来这个转化很好的广告并没有下线，只是其tracking link将会经由点击服务adredirect跳转到另外的广告。

### 借壳
如果一个广告$oid在渠道$aid点击量大，但是效果不好，就会产生借壳：往backup_ads里面插入记录($aid, $oid, $backup_id, type=shl)

- 下放的时候仍然下放$oid
    ```
    {
        oid: $oid,
        tracking_link: http://click_domain/path?s=xxxxxxx   // s=genS($oid, $aid,...),
        ...
    }
    ```
- 在adredirect接收点击阶段，congs串解析出oid，然后查询backup_oids表（where aid=$aid and oid=$oid），如果发现$aid-$oid有借壳行为，就会将点击重定向到跳转到一条oid=200580的sml 

## 注意
其实backup_oids字段是没有被用到的。点击阶段，如果一个广告是有备胎或者借壳，就会跳转到一条oid=200580的sml 