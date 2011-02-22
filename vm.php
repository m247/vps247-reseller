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
  $vm = $api->get_vm($_GET['id']);

  if (!$vm || $vm->{'end-user-id'} != $_SESSION['user_id']) {
    header("Location: vms.php");
    exit;
  }
?>
<html>
 <head>
  <title>VPS Control Panel :: VM :: <?= $vm->name ?></title>
  <link rel="stylesheet" type="text/css" href="styles/screen.css"/>
  <meta http-equiv="refresh" content="<?= $vm->status == 'Shutting down' || $vm->status == 'Starting' ? '2' : '10' ?>"/>
  <script type="text/javascript">
  <!--
    function showConsole() {
      window.open( "vm_console.php?id=<?= $_GET['id'] ?>","VM Console","status=0,height=680,width=850,resizable=1" )
    }
  //-->
  </script>
 </head>
 <body>
  <div class="title"><img src="<?= $CONFIG['logo_url'] ?>"/>VM: <?= $vm->name ?></div><hr/>
  <?= $user_info ?>
  <a href="vms.php">VMs</a> &gt; <a href="vm.php?id=<?= $_GET['id'] ?>"><?= $vm->name ?></a>
  <h3>Information about this VM</h3>
  <table cellspacing="0" width="500px;" class="vertical">
   <tr><th>Name:</th><td><?= $vm->name ?></td></tr>
   <tr><th>Status:</th><td><?= $vm->status ?></td></tr>
   <tr><th>Operating System:</th><td><?= $vm->{'os-name'} ?></td></tr>
   <tr><th>Memory:</th><td><?= $vm->{'memory-mb'} ?> MB</td></tr>
   <tr><th>Disk space:</th><td><?= $vm->{'disk-space'} ?> GB</td></tr>
   <tr><th>Ip address(es):</th><td><?= $vm->ips ?></td></tr>
  </table>
  <hr/>
  <div class="actions_box">
   <h3>Power control</h3>
   <form action="vm_power.php" method="post">
    <input type="hidden" name="id" value="<?= $vm->id ?>"/>
    <input type="hidden" name="action" value=""/>
    <input<?= $vm->status == "Halted" ? '' : ' disabled' ?> type="submit" name="do_start" value="Start this VM" onclick="this.disabled=true; this.form.action.value='start'; this.form.submit(); return true;"/>
    <input<?= $vm->status == "Running" ? '' : ' disabled' ?> type="submit" name="do_stop" value="Stop this VM" onclick="if (confirm('Are you sure you want to stop this VM?')) { this.disabled=true; this.form.action.value='stop'; this.form.submit(); return true; } else { return false; }"/>
    <input<?= $vm->status == "Running" || $vm->status == "Shutting down" ? '' : ' disabled' ?> type="submit" name="do_force_stop" value="Force Stop this VM" onclick="if (confirm('Are you sure you want to stop this VM?')) { this.disabled=true; this.form.action.value='force_stop'; this.form.submit(); return true; } else { return false; }"/>
   </form>
  </div>
  <div class="actions_box">
   <h3>Console</h3>
    <button<?= $vm->status == "Halted" ? ' disabled' : '' ?> onclick="showConsole()">Show console</button>
   </form>
  </div>
  <hr/>
  <a href="vm_private_network.php?id=<?= $_GET['id'] ?>">Manage Private Networks</a><br/>
  <a href="vm_graphs.php?id=<?= $_GET['id'] ?>">Show Graphs</a>
 </body>
</html>