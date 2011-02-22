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
  $vms = $api->get_vms();
?>
<html>
 <head>
  <title>VPS Control Panel :: VMs</title>
  <link rel="stylesheet" type="text/css" href="styles/screen.css"/>
 </head>
 <body>
  
  <div class="title"><img src="<?= $CONFIG['logo_url'] ?>"/>VMs</div><hr/>
  <?= $user_info ?>
  <table cellspacing="1">
  <tr><th>Name</th><th>Status</th><th>Operating System</th><th>Memory</th><th>Disk space</th><th>IP address(es)</th></tr>
  <?php
    foreach ($vms as $vm) {
      if ($vm->{'end-user-id'} == $_SESSION['user_id']) {
  ?>
  <tr>
  <td><a href="vm.php?id=<?= $vm->id ?>"><?= $vm->name ?></a></td>
  <td><?= $vm->status ?></td>
  <td><?= $vm->{'os-name'} ?></td>
  <td><?= $vm->{'memory-mb'} ?> MB</td>
  <td><?= $vm->{'disk-space'} ?> GB</td>
  <td><?= $vm->ips ?></td>
  </tr>
  <?php
      }
    }
    if ($vms->count() == 0) {
    ?>
   <tr><td colspan="6" style="padding: 20px; text-align: center;">There are currently no VMs in your account.</td></tr>
    <?php
    }
  ?>
  </table>
 </body>
</html>