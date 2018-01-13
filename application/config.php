<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    // +----------------------------------------------------------------------
    // | 应用设置
    // +----------------------------------------------------------------------

    // 应用命名空间
    'app_namespace'          => 'app',
    // 应用调试模式
    'app_debug'              => true,
    // 应用Trace
    'app_trace'              => false,
    // 应用模式状态
    'app_status'             => '',
    // 是否支持多模块
    'app_multi_module'       => true,
    // 入口自动绑定模块
    'auto_bind_module'       => false,
    // 注册的根命名空间
    'root_namespace'         => [],
    // 扩展函数文件
    '0'        => [THINK_PATH . 'helper' . EXT],
    // 默认输出类型
    'default_return_type'    => 'html',
    // 默认AJAX 数据返回格式,可选json xml ...
    'default_ajax_return'    => 'json',
    // 默认JSONP格式返回的处理方法
    'default_jsonp_handler'  => 'jsonpReturn',
    // 默认JSONP处理方法
    'var_jsonp_handler'      => 'callback',
    // 默认时区
    'default_timezone'       => 'PRC',
    // 是否开启多语言
    'lang_switch_on'         => false,
    // 默认全局过滤方法 用逗号分隔多个
    'default_filter'         => '',
    // 默认语言
    'default_lang'           => 'zh-cn',
    // 应用类库后缀
    'class_suffix'           => false,
    // 控制器类后缀
    'controller_suffix'      => false,

    // +----------------------------------------------------------------------
    // | 模块设置
    // +----------------------------------------------------------------------

    // 默认模块名
    'default_module'         => 'index',
    // 禁止访问模块
    'deny_module_list'       => ['common'],
    // 默认控制器名
    'default_controller'     => 'Index',
    // 默认操作名
    'default_action'         => 'index',
    // 默认验证器
    'default_validate'       => '',
    // 默认的空控制器名
    'empty_controller'       => 'Error',
    // 操作方法后缀
    'action_suffix'          => '',
    // 自动搜索控制器
    'controller_auto_search' => false,

    // +----------------------------------------------------------------------
    // | URL设置
    // +----------------------------------------------------------------------

    // PATHINFO变量名 用于兼容模式
    'var_pathinfo'           => 's',
    // 兼容PATH_INFO获取
    'pathinfo_fetch'         => ['ORIG_PATH_INFO', 'REDIRECT_PATH_INFO', 'REDIRECT_URL'],
    // pathinfo分隔符
    'pathinfo_depr'          => '/',
    // URL伪静态后缀
    'url_html_suffix'        => 'html',
    // URL普通方式参数 用于自动生成
    'url_common_param'       => false,
    // URL参数方式 0 按名称成对解析 1 按顺序解析
    'url_param_type'         => 0,
    // 是否开启路由
    'url_route_on'           => false,
    // 路由使用完整匹配
    'route_complete_match'   => false,
    // 路由配置文件（支持配置多个）
    'route_config_file'      => ['route'],
    // 是否强制使用路由
    'url_route_must'         => false,
    // 域名部署
    'url_domain_deploy'      => false,
    // 域名根，如thinkphp.cn
    'url_domain_root'        => '',
    // 是否自动转换URL中的控制器和操作名
    'url_convert'            => true,
    // 默认的访问控制器层
    'url_controller_layer'   => 'controller',
    // 表单请求类型伪装变量
    'var_method'             => '_method',
    // 表单ajax伪装变量
    'var_ajax'               => '_ajax',
    // 表单pjax伪装变量
    'var_pjax'               => '_pjax',
    // 是否开启请求缓存 true自动缓存 支持设置请求缓存规则
    'request_cache'          => false,
    // 请求缓存有效期
    'request_cache_expire'   => null,
    // 全局请求缓存排除规则
    'request_cache_except'   => [],

    // +----------------------------------------------------------------------
    // | 模板设置
    // +----------------------------------------------------------------------
    //blade模板系统
    'template'               => [
        // 模板引擎类型 支持 php think 支持扩展
        'type'         => 'Blade',
        // 模板路径
        'view_path'    => ROOT_PATH.'template'.DS,
        // 模板后缀
        'view_suffix'  => 'blade.php',
        // 模板文件名分隔符
        'view_depr'    => DS,
        // 模板引擎普通标签开始标记
        'tpl_begin'    => '{{',
        // 模板引擎普通标签结束标记
        'tpl_end'      => '}}',
        'tpl_raw_begin'    => '{!!',
        'tpl_raw_end'    => '!!}',
        // 标签库标签开始标记
        'taglib_begin' => '{',
        // 标签库标签结束标记
        'taglib_end'   => '}',
    ],

    // tp模板系统
    // 'template'               => [
    //     // 模板引擎类型 支持 php think 支持扩展
    //     'type'         => 'Think',
    //     // 模板路径
    //     'view_path'    => '',
    //     // 模板后缀
    //     'view_suffix'  => 'html',
    //     // 模板文件名分隔符
    //     'view_depr'    => DS,
    //     // 模板引擎普通标签开始标记
    //     'tpl_begin'    => '{',
    //     // 模板引擎普通标签结束标记
    //     'tpl_end'      => '}',
    //     // 标签库标签开始标记
    //     'taglib_begin' => '{',
    //     // 标签库标签结束标记
    //     'taglib_end'   => '}',
    // ],

    // 视图输出字符串内容替换
    'view_replace_str'       => [],
    // 默认跳转页面对应的模板文件
    'dispatch_success_tmpl'  => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',
    'dispatch_error_tmpl'    => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',

    // +----------------------------------------------------------------------
    // | 异常及错误设置
    // +----------------------------------------------------------------------

    // 异常页面的模板文件
    'exception_tmpl'         => THINK_PATH . 'tpl' . DS . 'think_exception.tpl',
    'http_exception_template'    =>  [
          // 定义404错误的重定向页面地址
          404 =>  ROOT_PATH.'template'.DS.'api/exception/404.blade.php',
          400 =>  ROOT_PATH.'template'.DS.'api/exception/400.blade.php',
          401 =>  ROOT_PATH.'template'.DS.'api/exception/401.blade.php',
    ],
    // 错误显示信息,非调试模式有效
    'error_message'          => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg'         => false,
    // 异常处理handle类 留空使用 \think\exception\Handle
    'exception_handle'       => '',

    // +----------------------------------------------------------------------
    // | 日志设置
    // +----------------------------------------------------------------------

    'log'                    => [
        // 日志记录方式，内置 file socket 支持扩展
        'type'  => 'File',
        // 日志保存目录
        'path'  => LOG_PATH,
        // 日志记录级别
        'level' => [],
    ],

    // +----------------------------------------------------------------------
    // | Trace设置 开启 app_trace 后 有效
    // +----------------------------------------------------------------------
    'trace'                  => [
        // 内置Html Console 支持扩展
        'type' => 'Html',
    ],

    // +----------------------------------------------------------------------
    // | 缓存设置
    // +----------------------------------------------------------------------

    'cache'                  => [
        // 驱动方式
        'type'   => 'File',
        // 缓存保存目录
        'path'   => CACHE_PATH,
        // 缓存前缀
        'prefix' => '',
        // 缓存有效期 0表示永久缓存
        'expire' => 0,
    ],

    // +----------------------------------------------------------------------
    // | 会话设置
    // +----------------------------------------------------------------------

    'session'            => [
        'id'             => '',
        // SESSION_ID的提交变量,解决flash上传跨域
        'var_session_id' => '',
        // SESSION 前缀
        'prefix'         => 'think',
        // 驱动方式 支持redis memcache memcached
        'type'           => '',
        // 是否自动开启 SESSION
        'auto_start'     => true,
    ],
    'adminster_key'   =>'xijujituan', //后台登录 口令
    // +----------------------------------------------------------------------
    // | Cookie设置
    // +----------------------------------------------------------------------
    'cookie'                 => [
        // cookie 名称前缀
        'prefix'    => '',
        // cookie 保存时间
        'expire'    => 0,
        // cookie 保存路径
        'path'      => '/',
        // cookie 有效域名
        'domain'    => '',
        //  cookie 启用安全传输
        'secure'    => false,
        // httponly设置
        'httponly'  => '',
        // 是否使用 setcookie
        'setcookie' => true,
    ],

    //分页配置
    'paginate'               => [
        'type'      => 'bootstrap',
        'var_page'  => 'page',
        'list_rows' => 15,
        'page_size'=>5, //页码数量
        'page_button'=>[
            'total_rows'=>true, //是否显示总条数
            'turn_page'=>true, //上下页按钮
            'turn_group'=>true, //上下组按钮
            'first_page'=>true, //首页
            'last_page'=>true  //尾页
        ]
    ],
    // auth配置 https://github.com/luoyt/auth 使用详解
    'auth'  => [
        'auth_on'           => 1, // 权限开关
        'auth_type'         => 1, // 认证方式，1为实时认证；2为登录认证。
        'auth_group'        => 'auth_group', // 用户组数据不带前缀表名
        'auth_group_access' => 'auth_group_access', // 用户-用户组关系不带前缀表名
        'auth_rule'         => 'auth_rule', // 权限规则不带前缀表名
        'auth_user'         => 'adminster', // 用户信息不带前缀表名
    ],
    //状态对应的值
    'status'  =>[
        '0'        =>'删除',
        '-1'       =>'禁用',
        '1'        =>'启用',
        '2'        =>'正常',
        '-2'       =>'冻结',
        '3'        =>'已实名',
        '-3'       =>'未实名',
        '4'        =>'已支付',
        '-4'       =>'待支付',
        '5'        =>'正常收益',
        '-5'       =>'结束收益',
        '-6'       =>'提前终止',
        '6'        =>'收益继续',
        '7'        =>'mars',
        '-7'       =>'venus',
        '8'        =>'有效订单',
        '-8'       =>'无效订单',
        '9'        =>'开始收益',
        '-9'       =>'未开始收益',
        '10'        =>'申请已提交',
        '-10'       =>'申请未提交',
        '11'        =>'通过审核',
        '-11'       =>'审核被拒',
    ],
    //单独的订单类型 具体操作 （1 - 6） 收 （7-8） 支
    'order_status'  =>[
        '1'        =>'每日红包',
        '2'        =>'新人推荐红包',
        '3'        =>'推荐收益红包',
        '4'        =>'项目收益红包',
        '5'        =>'项目到期回款',
        '6'        =>'活动红包',
        '7'        =>'余额提现',
        '8'        =>'购买项目'
    ],
    'sys_type'  =>[
        '1'        =>'注册验证码',
        '2'        =>'找回密码验证码'
    ],
    "payment"      =>[
        'Alipay'        =>"支付宝",
        'Weipay'     =>"微信",
        'Account'    =>"账户余额",
    ],
    "withdraws"      =>[ //提现方式
        'Alipay'     =>"支付宝",
        'Weipay'     =>"微信",
    ],
    "jpush"      =>[ //推送信息
        'api_key'       =>"f7104d032b431bc449d1a20d",
        'api_master'     =>"3b6c1e5e49eb1744b5bccf2c",
    ],
    'page_size'             =>  '10', // 默认每页大小
    'default_groups'        =>  1,//默认用户组（管理员）
    'default_title'         =>'惠钱包',


    'default_member_group'    =>'12',   //默认用户组（会员）
    'default_login_time'      =>'36000',//登录有效期(s)

    'countdown_days'         =>'7',     //倒计时时间天
    'order_validity'         =>'60',   //订单有效期（s）
    'certification_count'    =>'5',     //认证次数
    'everyday_log'           =>'./logs',
    'OSS' => [
        'accessKeyId'       =>'LTAIn4oprg773emN',
        'accessKeySecret'   =>'B6m8fnLnq6u2N27CTH7VdAhnTrlrC2',
        'endpoint'          =>'oss-cn-qingdao.aliyuncs.com',
        'isCName'           =>false,
        'securityToken'     =>null,
        'bucket'            =>'fangtouwang'
    ],
    'sms_template'  =>[
        'normal_vert_code'      =>'SMS_119093204',
        'register_vert_code'    =>'SMS_71055044',//注册验证码
        'change_info'   =>'SMS_71055041',//信息变更验证码
        'change_pwd'    =>'SMS_71055042',//密码修改验证码
        'active_code'   =>'SMS_71055043',//活动确认验证码
        'login_msg'     =>'SMS_71055045',//登录异常验证码
        'login_conifrm'  =>'SMS_71055046',//登录确认验证码
        'check_info'    =>'SMS_71055048',//验证身份
        'invite_msg'  =>'SMS_119083227',//邀请注册提示信息
    ],
    'alipay'    =>[
        'gateway_url'  =>"https://openapi.alipay.com/gateway.do",
        'app_id'       =>"2017121500800116",
        'app_version'  =>"1.0",
        'post_charset'  =>'utf-8',
        'sign_type'    =>"RSA2",
        'format'       =>"json",
        'timeout_express' =>"30m",
        'notify_url'       =>"http://huiqianbao.lianshuopay.com/index/Alipaycallback/callback",
        'login_url'       =>"http://huiqianbao.lianshuopay.com/api/Alipay/users",
        'product_code'    =>"QUICK_MSECURITY_PAY",
        'pid'             =>"2088421433746925",
        'rsa_public_key'    =>"MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAt0UwDpyne+hDFjS7PzAZgPompNwimVU/Fhzpz+HQF3RTWvbirV28pt+hX9eyL+Y+cUBT0tLr4gG2tQS+vmN5ZqSbTBKkV38G/QFqdLV/6U8+opWzKMkt9HoD3+22Idk/9uL4v5dKqBRDe5D5dnRcVuUxpMFVbLZZv+O4VuU1KKd/AfJbcSGANrLcO7FVgjAfl59VWSI/3UWz2+M+de6csRGWso3H3wQ9KeaO82+USQu2120OSux+ghgifbIvT4dLZyfOvr6/oDX8+KgR6etsQ+DtQFzD+8S9HGDivtEex4PASbepxj4pCpoDtJno9N23eKLGjkMrngTybE9dLcO6qwIDAQAB",


        'rsa_private_key'     => "MIIEpAIBAAKCAQEA6zbdTOUzarCq+ISXi3AmLh0PE7RAKV+UjFijRsQaktjUClYw917is4J/y5u/nmKWCTIWqk5ynu2WLVUYWjYe9Cy8f0sOF4eLdqzJcQ4ByepWTvHa7zowwObFiTg4RmHW/jIzfdK/H0Nh1/P/8/kCwwZdtZkWSeEa4MxY1KTNj1PP2trCIdpLWohXHHpITjGYsdpZ4oOLPV81XohOfdjYx1n6Vy1phRz4JK7u1d0Q/e0JUXA86zxpBwOSe/rgB2VReK0MXok91AIKBVfdAxv8s9dZVuHQikT8nu8sMC2NHtqD1EBGH35Gy3kwTVdc69GOZeO7YvFuE0Pv6bCthbGaIwIDAQABAoIBAG7xsfBTK61nJz2RTOm4PEoD6tigbKMXMjoAlQML/HzHSTyW6wHC/6ssIpsnG3rZhWtQ9N/foLDV38ACIzppzK/fagmgnUT76gxP87QfpzhWD9+stFeiWtyh/Qp8Og74ly/O/fqbLQrR/ZSewluRWHwkxI6jZp/yTXs656tlEJBJUHYxrAn+50wSfewIkFg0AFt0+eVcmPvSIVyvjxxrt9Tws8uFvGJ+Z9XTS7YIsvFxq+D1FV29S9+KLXFJyV7i1nR8lU+16GN9PGkyN/7JbGWgSSAz6ohGpluvN9wHrcraVs9j7Tk+TWVpCqjjKmPG9mHFoWEjbnw5uUOKL9FDgAECgYEA+sX2DqKxqRuo8b74nz/jIzpf3ynIN0jkoqAv138v9qcPefLZthiU2Rl0NAp/YE8zXnDb+RUl5xXKQ40N75TuY9pqur6MACrw11Ixr1wHVQijXSRrlZvcNT5xR3fnxCCqw3IBzEK8etataWO/4eBe6IzmZh+3EgxLzF7+7Aes8wECgYEA8B3i090lk1BqLbmtJu3wcRk5HCjo7GYTgezY5at9QP6QZFUCis8oocqbchqzNtOGAzyHn+dsjmyJO+PeJJUUbsAF0LpluGo2kLmnOn9RP+zSEvvk6mZRElsDS/sPHJUs0pbCeq5I5aGpdSQLZUGhmmE86NE70fWs9oM+2JX5YSMCgYAV/ZQVNnkj3jQTdu6wcFbHuQ+9LxnKl07NYASx/gpDs+SsifpDeKZc31mMUrtWw7s6deUkU5FuUT/aap0lPEcnZHjlhV4biahzCVcoDIbsEwTYXyA8D13hIWenn8EoNsEiwHWd5uBGWVr9meyyrw1DkU+G9xMDtyZSdqYeJpIpAQKBgQDN4NIb1gHhzZE8o3dTabA/SOEuw+TyzJLtU6alReSfVaV4YWtMmVK9Znrh2wLOf47oN34SHNt3QphoQXi25Rwf2YUi8q9E78zZSlWYUpHXvbZhkJk2YYQhuOeavii22TEVXy5hAg5+1Z32UnFy4FMvizAubzhbTcp/kwjPq8enAQKBgQDu+/PJH275HPMijaS8Tnwa5+6Qqr0DzjquPfrrJXJQjDJfVg8bamDPN5RcSbVpBTzXeh7raKAkyI22g51fZgZaK2Un9Cr0Y3uldBB5Vbr7RHdwlDvICCw3EmOoZ7hzMPrhs8bG0aSGw+QAIOSMR9s587axpO564YPviEB9TwNWzQ=="
    ]
];
