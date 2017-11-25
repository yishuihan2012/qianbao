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
