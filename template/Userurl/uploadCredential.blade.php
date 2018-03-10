<!DOCTYPE html>
<html>
<head>
    <title>測試上傳</title>
</head>
<body>
        <form action="http://test.trx.helipay.com/trx/merchantEntry/upload.action" enctype="multipart/form-data" method="post">
        <!-- <form action="http://wallet.dev.com/index.php/api/Helibao/uploadCredential" enctype="multipart/form-data" method="post"> -->
            <input type="text" name="interfaceName" value="uploadCredential">
            <input type="text" name="merchantNo" value="C1800193823">
            <input type="text" name="body" value="Blpz6K/yz2Dznini7URbXU/k6mMBPMagp4LUin03Kjw+70uH5lP/ufUUjx9TpHqgtBST0xvBAs2JYc5XMGpvV61gsvSJuYzMboGr9g7qqG6J+C2bvFgrxrm/Ui8JAKCIav+qQiiI2BBozSw4MqkKbMg+2sgHF1nTXCbWpWN+6ntFRJkbM/qMsw==">
            <input type="text" name="sign" value="7041946510bc1348c3feccdbb0856c79">
            <input type="file" name="file">
            <input type="submit" name="" value="提交">
        </form>
</body>
</html>
