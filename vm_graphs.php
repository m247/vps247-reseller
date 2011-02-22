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
  $t = $_GET['t'];
  if (!$t) { $t = "net"; }
?>
<html>
 <head>
  <title>VPS Control Panel :: VM :: <?= $vm->name ?> :: Graphs</title>
  <link rel="stylesheet" type="text/css" href="styles/screen.css"/>
 </head>
 <body>
  <div class="title"><img src="<?= $CONFIG['logo_url'] ?>"/>Graphs</div><hr/>
  <?= $user_info ?>
  <a href="vms.php">VMs</a> &gt; <a href="vm.php?id=<?= $_GET['id'] ?>"><?= $vm->name ?></a> &gt; Graphs<br/>
  <hr/>
  <p><b><a href="vm_graphs.php?id=<?= $_GET['id'] ?>&t=net">Network</a> | <a href="vm_graphs.php?id=<?= $_GET['id'] ?>&t=disk">Disk</a></b></p>
  <img src="vm_graph.php?id=<?= $_GET['id'] ?>&p=day&t=<?= $t ?>"/>
  <img src="vm_graph.php?id=<?= $_GET['id'] ?>&p=week&t=<?= $t ?>"/>
  <img src="vm_graph.php?id=<?= $_GET['id'] ?>&p=month&t=<?= $t ?>"/>
  <img src="vm_graph.php?id=<?= $_GET['id'] ?>&p=year&t=<?= $t ?>"/>
 </body>
</html>