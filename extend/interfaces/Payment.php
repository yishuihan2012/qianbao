<?php
interface Payment{
  public function pay($name, $var); //支付
  public function getHtml($template); //转账
}
 ?>
