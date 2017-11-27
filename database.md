###1 文章表(Article)
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

###1.1 文章内容表(Article_data)
| 字段                 | 类型          | 备注                        |
| ------------------ | ----------- | ------------------------- |
| data_id          | int         | 文章内容ID，pk，ai 		|
| data_article   | int         | 文章主表ID 		|
| data_text       | text       | 文章内容               		|

###1.2 文章分类表(Article_category)
| 字段                 | 类型          | 备注                        |
| ------------------ | ----------- | ------------------------- |
| category_id          | int         | 分类ID			 		|
| category_name    | varchar | 分类名字           		|
| category_parent   | int | 分类父级ID	           		|

###2 用户信息表(Member)
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
| member_device_number   | varchar | 设备号	           		|
| member_isget_cert_redpackets   | tinyint | 是否领取实名红包	           		|
| member_isget_firstflush_redpackets   | tinyint | 是否领取首刷红包	           		|
| member_merchid   | varchar | 商户号	           		|
| member_merchname   | varchar | 商户名称	           		|
| member_regid   | varchar | 极光推送id	           		|
| member_package_people   | varchar | 刷卡奖励红包人数	           		|
| member_jyf_fee   | varchar | 	           		|
| member_tzadd_fee   | varchar | 	           		|

###2.1 用户登录表(Member_login)
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

###2.2 用户实名表(Member_certification)
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
