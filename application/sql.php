<?php
return [
	"alter table wt_member add column member_root int(11) comment '运营商id'",//wt_member 表新增 字段 member_root 2018-1-23 13:30 许成成 目的：区分当前用户属于那个代理商名下的。
	"alter table wt_adminster add column adminster_user_id int(11) comment '运营商绑定的member_id'",

];