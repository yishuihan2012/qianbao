### 1 文章表(Article)
| 字段                 | 类型          | 备注                        |
| ------------------ | ----------- | ------------------------- |
| article_id          | int         | 文章ID，pk，ai                |
| article_title          | varchar         	| 文章标题               |
| article_parent      | int         		| 文章顶级分类ID    |
| article_category      | int         		| 文章分类ID    |
| article_thumb      | varchar         	| 文章缩略图    |
| article_topper      | 1         			| 是否置顶   1置顶 0不置顶(默认) |
| article_recommend      | 1         	| 是否推荐   1推荐 0不推荐(默认) |
| article_show	    | 1         		| 是否显示(默认)   1显示 0不显示 |
| article_read	    | int         			| 文章的阅读数量 |
| article_desc      | samlltext       	| 文章简介 |
| article_edit_time      | datatime    | 文章更新时间 |
| article_add_time      | datatime    | 文章添加时间 |

### 1.1 文章内容表(Article_data)
| 字段                 | 类型          | 备注                        |
| ------------------ | ----------- | ------------------------- |
| data_id          | int         | 文章内容ID，pk，ai 		|
| data_article   | int         | 文章主表ID 		|
| data_text       | text       | 文章内容               		|

### 1.2 文章分类表(Article_category)
| 字段                 | 类型          | 备注                        |
| ------------------ | ----------- | ------------------------- |
| category_id          | int         | 分类ID			 		|
| category_name    | varchar | 分类名字           		|
| category_parent   | int | 分类父级ID	           		|

### 2 用户基本信息表(Member)
| 字段                 | 类型          | 备注                        |
| ------------------ | ----------- | ------------------------- |
| member_id          | int         | 用户信息ID			 		|
| member_nick    | varchar | 用户昵称           		|
| member_mobile   | varchar | 用户手机号	           		|
| member_pass_for_pay   | varchar | 支付密码	           		|
| member_image   | varchar | 用户头像	           		|
| member_code   | varchar | 用户推荐码	           		|
| member_ewn   | varchar | 二维码地址	           		|
| member_level_id   | int | 用户角色id	           		|
| member_level_name   | varchar | 用户角色名称	           		|
| member_commission   | int | 用户分润等级（根据人数自动升级，0员工，1店长，2老板）	           		|
| member_no   | varchar | 用户商户号	           		|
| member_state   | tinyint | 是否审核	           		|
| member_creat_time   | datetime | 注册时间	           		|
| member_token   | varchar | 用户token信息	           		|
| member_check_count   | int | 实名认证次数	           		|
| member_update_time   | datetime | 修改时间	           		|
| member_status   | tinyint | 1可用，0禁止	           		|
| member_os   | int | 已知晓的名下会员数	           		|
| member_cert   | tinyint | 是否实名	           		|

### 2 用户详细信息表（member_extend）
| 字段           | 类型       | 备注       |
|----------------|-----------|------------|
| member_auto_id | int       | 自增id     |
| member_id      |   int     | 用户id     |
| member_jyf_id  | varchar   | 金易付报备id|
| member_jyf_name| varchar   | 金易付报备名称|
| member_jpush_id| varchar   | 极光推送id   |
| member_device_id| varchar  | 用户手机设备号|
| member_cert_pack| tinyint  | 实名红包0未领取|
| member_cardpay_pack| tinyint| 首次刷卡红包 |

### 2.1 用户登录表(Member_login)
| 字段                 | 类型          | 备注                        |
| ------------------ | ----------- | ------------------------- |
| login_id          | int         | 登录表自增id 		|
| login_member_id   | int         | 用户信息表id 		|
| login_account       | varchar       | 登录账号               		|
| login_pass       | varchar       | 登录密码               		|
| login_pass_salt       | varchar       | 登录盐值               		|
| login_attempts       | int       | 尝试次数               		|
| login_token       | varchar       | token               		|
| login_state       | tinyint       | 状态1正常，-1禁止，0异常               		|
| login_update_time       | datetime       | 登陆时间               		|
| login_create_time       | datetime       | 添加时间               		|

### 2.2 用户实名表(Member_certification)
| 字段                 | 类型          | 备注                        |
| ------------------ | ----------- | ------------------------- |
| certification_id          | int         | 实名表自增id 		|
| certification_member_id          | int         | 用户信息id 		|
| certification_card          | varchar         | 身份证号 		|
| certification_name          | varchar         | 真实姓名 		|
| certification_info          | varchar         | 认证信息 		|
| certification_return_message          | varchar         | 记录返回信息 		|
| certification_state          | tinyint         | 认证状态 		|
| certification_creat_time          | datetime         | 认证时间 		|
| certification_sex          | varchar         | 性别 		|
| certification_birth          | varchar         | 生日 		|
| certification_province          | varchar         | 省 		|
| certification_city          | varchar         | 市 		|
| certification_town          | varchar         | 县 		|
| certification_moblie          | varchar         | 手机号 		|
| certification_identity_front          | varchar         | 身份证正面 		|
| certification_identity_back          | varchar         | 身份证反面 		|
| certification_bankcard_front          | varchar         | 银行卡正面 		|
| certification_bankcard_back          | varchar         | 银行卡反面 		|
| certification_people_bankcard          | varchar         | 人卡合一 		|

### 2.3 用户绑定的银行卡信息(wt_bank_card)
| 字段                 | 类型          | 备注                        |
| ------------------ | -----------    | ------------------------- |
| card_id            | int            | 表自增id 		     |
| card_member_id     | int            | 用户id 		      |
| card_number        | varchar         | 银行卡号 		    |
| card_type          | varchar         | 卡类型0信用卡1储蓄卡|
| card_tel           | varchar          | 预留手机号 		|
| card_bank          | varchar          | 所属银行    |
| card_name          |varchar           |持卡人姓名   |
| card_identify      | varchar          | 身份证号   |
| card_cvn2          | varchar          |卡背面cvn2码|
|card_validity_date  |varchar           |卡的有效期  |
|card_provice        |varchar           |开户省份    |
| card_city          |varchar           |开户城市    |
| card_area          | varchar          |开户区      |
|card_city_code      |varcjar           |开户行城市码 |

### 2.4 用户第三方账户信息（wt_member_account）
| 字段                 | 类型            | 备注             |
| ------------------   | -----------    | ---------------- |
| account_id           |  int           | 表自增id 		|
| account_user         | varchar        | 用户id 		     |
| account_code         | varchar        | 账户类型  	    |
| account_name         | varchar        | 账户类型名称 	  |
| account_info         | varchar        | 其他账户信息 	  |
| account_account      | varchar        | 账户名称          |
| account_create_at    | varchar        | 创建日期          |   

### 2.5 用户提现表（wt_member_cash）
| 字段                 | 类型            | 备注             |
| ------------------   | -----------    | ---------------- |
| cash_id              |  int           | 表自增id 		 |
| cash_member_id       | varchar        | 用户id 		      |
| cash_amount          | varchar        | 提现金额  	    |
| service_charge       | varchar        | 提现手续费  	   |
| cash_state           | varchar        | 提现状态 	        |
| cash_other_info      | varchar        | 备注信息          |
| cash_create_at       | varchar        | 创建日期          | 

### 2.5 用户反馈（wt_member_suggestion）
| 字段                 | 类型            | 备注             |
| ------------------   | -----------    | ---------------- |
| suggestion_id              |  int           | 表自增id 		 |
| suggestion_member_id       | varchar        | 用户id 		      |
| suggestion_info          | varchar        | 反馈信息  	    |
| return_info       | varchar        | 回复信息  	   |
| return_adminster           | int        | 处理人id 	        |
| suggestion_state      | tinyint        | 是否处理（0未处理，1已处理）          |
| suggestion_creat_time       | timestamp        | 创建日期          |    

### 3 基本配置表（wt_config）
| 字段                 | 类型          | 备注                |
| ------------------ | ----------- | -----------------------|
| config_id          | int         | 表自增id 		          |
| config_key         | int         | 配置项键名 		        |
| config_value        | varchar         | 配置项键值 		|
| config_des          | varchar         | 配置描述 		     |
| config_type          | varchar         | 配置分类 		 |
| config_field_type   | varchar          | 字段类型          |
| config_create_time |varchar            |创建时间           |
| config_update_time | varchar          | 更新时间           |

### 4 APP版本控制（wt_version）
| 字段                 | 类型            | 备注             |
| ------------------   | -----------    | ---------------- |
| version_id           |  int           | 表自增id 		|
| version_name         | varchar        | 版本名称 		   |
| version_code         | varchar        | 版本code 		 |
| version_type         | varchar        | android ios 	   |
| vrsion_link          | varchar        | 版本更新链接 	  |
| version_desc         | varchar        | 版本更新描述      |
| version_state        | varchar        | 版本状态 1目前版本 |
| version_create_at    | varchar        |  创建日期         |

### 5 渠道来源（wt_channel_type）
| 字段                 | 类型            | 备注             |
| ------------------   | -----------    | ---------------- |
| ct_id                |  int           | 表自增id 		|
| ct_name              | varchar        | 渠道名称 		   |
| ct_min_money         | varchar        | 渠道最小刷卡金额  |
| ct_max_money         | varchar        | 渠道最大刷卡金额  |
| ct_min_rate          | varchar        | 最低税率      	|
| ct_des               | varchar        | 渠道描述          |
| ct_img               | varchar        | 渠道图片          |
| ct_status            | varchar        |  状态1启用0关闭   |
| ct_unique_sign       | varchar        |  渠道标识         |
| ct_create_time       | varchar        |  渠道创建时间     |

### 5.1 渠道对应会员费率表（wt_channel_rate）
| 字段                 | 类型            | 备注             |
| ------------------   | -----------    | ---------------- |
| channel_id           |  int           | 表自增id 		 |
| channel_level        | varchar        | 会员级别 		    |
| channel_type         | varchar        | 渠道类型          |
| channel_rate         | varchar        | 交易费率          |
| channel_generation   | varchar        | 固定代收费        |
| channel_show         | varchar        | 是否显示          |
| channe_create_time   | varchar        | 创建时间          |


### 6 银行列表(bank)
| 字段                 | 类型          | 备注                |
| ------------------ | ----------- | -----------------------|
| bank_id          | int         | pk ai 		          |
| bank_name    | varchar | 银行名称             |
| bank_avatar   | varchar | 银行图标		    |
| bank_letter     | varchar | 银行名称首字母  |
| bank_state     | int         | 银行状态 1启用 0停用 		|
| bank_update_time   | datetime          | 修改时间          |
| bank_add_time        | datetime          | 添加时间          |

### 6.1 银行卡识别(bank_ident)
| 字段                 | 类型          | 备注                |
| ------------------ | ----------- | -----------------------|
| ident_id          | int         | pk ai 		          |
| ident_name    | varchar | 卡在银行名称             |
| ident_type      | int         | 卡类型 1信用卡 2借记卡 3贷记卡 4准贷记卡 5预付费卡 |
| ident_desc     | varchar | 卡详细信息  |
| ident_code     | int         | 卡识别码 前6位		|
| ident_update_time   | datetime          | 修改时间          |
| ident_add_time        | datetime          | 添加时间          |


### 7 通道列表(passageway)
| 字段                 | 类型          | 备注                |
| ------------------ | ----------- | -----------------------|
| passageway_id     | int         | pk ai 		          |
| passageway_no    | varchar | 通道代号 /类名             |
| passageway_name      | int         | 通道名称 |
| passageway_avatar     | varchar | 通道图标  |
| passageway_mech     | int         |  通道机构号		|
| passageway_key  |varchar | 通道机构key |
| passageway_min  | float | 最小刷卡额 |
| passageway_max | float | 最大刷卡额 |
| passageway_rate | float | 最低税率 | 
| passageway_desc | varchar | 通道描述 | 
| passageway_state | int | 通道状态 | 
| passageway_update_time   | datetime          | 修改时间          |
| passageway_add_time        | datetime          | 添加时间          |

### 7.1 通道对会员组的税率表(passageway_item)
| 字段                 | 类型          | 备注                |
| ------------------ | ----------- | -----------------------|
| item_id     | int         | pk ai 		           |
| item_passageway  | int  | 通道id            |
| item_group      		| int  | 用户组id        |
| item_rate     		| float | 税率	 	     |
| item_update_time   | datetime  | 修改时间          |
| item_add_time        | datetime  | 添加时间          |

### 8 推广素材列表(generalize)
| 字段                 | 类型          | 备注                |
| ------------------ | ----------- | -----------------------|
| generalize_id     | int         | 自增id 		          |
| generalize_titile    | varchar | 标题           |
| generalize_content      | text         | 内容 |
| generalize_creat_time     | datetime | 添加时间  |
| generalize_download_num     | int         |  下载数量		|
| generalize_sort  |tinyint | 排序 |
| generalize_is_del  | tinyint | 是否删除（0未删除，1已删除） |
| generalize_thumb  | varchar | 图片 |


