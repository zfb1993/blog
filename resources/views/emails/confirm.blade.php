<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>注册确认链接</title>
  </head>
  <body>
    <h1>感谢您在Sample网站进行注册！</h1>
    <p>
      点击下面的链接完成注册：
      <a href="{{ route('confirm_email',$user->activation_token) }}">
        {{ route('confirm_email',$user->activation_token) }}
      </a>
    </p>
    <p>
      如果不是您本人的操作请忽略此邮件
    </p>
  </body>
</html>
