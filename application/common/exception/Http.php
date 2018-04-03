<?php
namespace app\common\exception;
use app\common\helper\MailHelper;
use think\exception\Handle;
class Http extends Handle
{
    /**
     * 接管异常
     * @param \Exception $e
     * @return \think\Response
     */
    public function render(\Exception $e)
    {
        // print_r(self::getErrorHtml($e));die;
        // MailHelper::errorSend('出错了~', self::getErrorHtml($e));
        
        $a=parent::render($e);
        $trace=(array)($a);
        foreach ($trace as $k => $v) {
            if($_SERVER['REMOTE_ADDR']!='127.0.0.1'){
                $arr=[
                    '模块不存在:favicon.ico',
                    '模块不存在:uploads',
                    '模块不存在:static',
                ];
                array_walk($arr, function($value,$key,$v){
                    if(strpos($v, $value)===false)
                        die;
                },$v);
                MailHelper::errorSend('出错了~', $v);
            }
            break;
        }
        // MailHelper::errorSend('出错了~', $a->data);
        // halt(parent::render($e));
        return $a;
    }
    /**
     * 生成异常html
     * @param $e
     * @return string
     */
    protected static function getErrorHtml($e)
    {
        $html = '<!DOCTYPE html>
                    <html lang="en">
                        <head>
                            <title></title>
                            <meta charset="UTF-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1">
                        </head>
                        <body>
                            <table style="width: 800px; margin: 0 auto; border: 1px solid #ccc;">
                                <thead>
                                    <tr style="background: #ff0000; color: #ffffff; height: 50px;">
                                        <th colspan="2">Error Info:</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="height: 50px; width: 100px; text-align: center; background: #ccc">message</td>
                                        <td style="height: 50px;">'. $e->getMessage() .'</td>
                                    </tr>
                                    <tr>
                                        <td style="height: 50px; width: 100px; text-align: center; background: #ccc">file</td>
                                        <td style="height: 50px;">'. $e->getFile() .'</td>
                                    </tr>
                                    <tr>
                                        <td style="height: 50px; width: 100px; text-align: center; background: #ccc">line</td>
                                        <td style="height: 50px;">'. $e->getLine() .'</td>
                                    </tr>
                                    <tr>
                                        <td style="height: 50px; width: 100px; text-align: center; background: #ccc">getTraceAsString</td>
                                        <td style="height: 350px;">'. $e->getTraceAsString() .'</td>
                                    </tr>
                                </tbody>
                            </table>
                        </body>
                    </html>
                ';
        return $html;
    }
}