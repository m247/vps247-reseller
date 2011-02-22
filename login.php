<?php
/**
 * Copyright (c) 2011 M247 Ltd
 * Original by Phil Evans - 17/02/2011
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

  require_once('lib/header.inc.php');
  if ($_SESSION['username']) { header("Location: vms.php"); }

  if ($_POST) {
    if ($end_user_id = $api->authenticate_end_user($_POST['username'],$_POST['password'])) {
      $_SESSION['username'] = $_POST['username'];
      $_SESSION['user_id'] = $end_user_id;
      header("Location: vms.php");
    } else {
      $_SESSION['errors'] = "Username or password were invalid.";
      header("Location: login.php");
    }
    exit;
  } else {

?>

<html>
  <head>
    <title>VPS Control Panel :: Login</title>
    <link rel="stylesheet" type="text/css" href="styles/screen.css"/>
  </head>
  <body>
    <center>
      <div class="login_box">
      <img src="<?= $CONFIG['logo_url'] ?>"/><br/>
  <? if ($errors) { ?>
        <span class="errors"><?= $errors ?></span>
  <? } ?>
        <form method="post">
          <center>
            <span class="field_name">Username:</span><input type="text" name="username"/><br/>
            <span class="field_name">Password:</span><input type="password" name="password"/><br/>
            <input type="submit" name="do_login" value="Log in"/>
          </center>
        </form>
      </div>
    </center>
  </body>
</html>
<?php
  }
?>