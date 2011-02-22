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
  $private_mac = implode(":",str_split("22f8" . str_pad(dechex($_GET['id']),8,"0",STR_PAD_LEFT),2));

  if (!$vm || $vm->{'end-user-id'} != $_SESSION['user_id']) {
    header("Location: vms.php");
    exit;
  }

  if ($_POST['do_delete']) {
    $private_vlan = $api->get_private_vlan($_POST['private_vlan_id']);
    if ($private_vlan && $private_vlan->{'end-user-id'} == $_SESSION['user_id']) {
      if ($api->delete_private_vlan($_POST['private_vlan_id'])) {
        $vm = $api->get_vm($_GET['id']);
        $_SESSION['notice'] = "The VLAN was successfully deleted.";
      } else {
        $_SESSION['errors'] = "The VLAN could not be deleted at this time.";
      }
    } else {
      $_SESSION['errors'] = "The VLAN could not be deleted at this time.";
    }
    header("Location: vm_private_network.php?id={$_GET['id']}");
    exit;
  }

  if ($_POST['do_create']) {
      if (strlen($_POST['private_vlan_name']) < 3) {
        $_SESSION['errors'] = "The VLAN name must be at least 3 characters long.";
      } else if ($api->create_private_vlan($_POST['private_vlan_name'],$_SESSION['user_id'])) {
        $vm = $api->get_vm($_GET['id']);
        $_SESSION['notice'] = "The VLAN was successfully created.";
      } else {
        $_SESSION['errors'] = "The VLAN could not be created at this time.";
      }
      if (!$_SESSION['errors']) {
        header("Location: vm_private_network.php?id={$_GET['id']}");
        exit;
      }
  }

  if ($_POST['do_assign']) {
    $private_vlan = $api->get_private_vlan($_POST['private_vlan_id']);
    if ($_POST['private_vlan_id'] == "0" || ($private_vlan && $private_vlan->{'end-user-id'} == $_SESSION['user_id'])) {
      if ($api->set_vm_private_vlan($_GET['id'],$_POST['private_vlan_id'])) {
        $vm = $api->get_vm($_GET['id']);
        $_SESSION['notice'] = "The VLAN was successfully changed.";
      } else {
        $_SESSION['errors'] = "The VLAN could not be changed at this time.";
      }
    } else {
      $_SESSION['errors'] = "The VLAN could not be changed at this time.";
    }
    header("Location: vm_private_network.php?id={$_GET['id']}");
    exit;
  }

  $private_vlan_id = (string)$vm->{'private-vlan-id'};
  if ($private_vlan_id != "")
    $current_private_vlan = $api->get_private_vlan($private_vlan_id);
  $private_vlans = $api->get_private_vlans();
?>
<html>
 <head>
  <title>VPS Control Panel :: VM :: <?= $vm->name ?> :: Private Network</title>
  <link rel="stylesheet" type="text/css" href="styles/screen.css"/>
 </head>
 <body>
  <div class="title"><img src="<?= $CONFIG['logo_url'] ?>"/>VM: <?= $vm->name ?></div><hr/>
  <?= $user_info ?>
  <a href="vms.php">VMs</a> &gt; <a href="vm.php?id=<?= $_GET['id'] ?>"><?= $vm->name ?></a> &gt; Private Network<br/>
  <? require('lib/flash.inc.php'); ?>
  <h3>Private Network</h3>
  <? if ($private_vlan_id == "" || $private_vlan_id == "0") { ?>
  This VM is currently not assigned to a private network.
  <? } else { ?>  
  This VM is currently assigned to private VLAN <b>&quot;<?= $current_private_vlan->name ?>&quot;</b>
  <? } ?><br/>
  The MAC address for the private network interface is <b><?= $private_mac ?></b>.<br/>
  <i><b>NOTE:</b> The interface may not be present until the first time you assign it to a Private Network.</i>
  <hr/>
  <form method="post">
  Assign to Private Network: 
   <select name="private_vlan_id">
    <option value="0">(NONE)</option>
   <? foreach ($private_vlans as $private_vlan) { ?>
    <? if ($private_vlan->{'end-user-id'} == $_SESSION['user_id']) { ?>
    <option value="<?= $private_vlan->id ?>"><?= $private_vlan->name ?></option>
    <? } ?>
   <? } ?>
   </select>
   <input type="submit" name="do_assign" value="Assign"/>
  </form>
  <hr/>
  <h3>Private Networks</h3>
  <table cellspacing="1">
   <tr><th>Name</th><th>Member VMs</th><th></th></tr>
    <? $count = 0; 
      foreach ($private_vlans as $private_vlan) { 
        $vms = $api->get_private_vlan_vms($private_vlan->id);
        $vm_list = array();
        foreach ($vms as $pvm) {
          array_push($vm_list, "<a href=\"vm.php?id={$pvm->id}\">{$pvm->name}</a>");
        }
    ?>
    <? if ($private_vlan->{'end-user-id'} == $_SESSION['user_id']) { ?>
     <? $count++; ?>
    <tr><td><?= $private_vlan->name ?></td><td><?= implode(", ",$vm_list) ?></td><td><form method="post"><input type="submit" name="do_delete" value="Delete" onclick="return confirm('Are you sure you want to delete this Private VLAN?');"/><input type="hidden" name="private_vlan_id" value="<?= $private_vlan->id ?>"/></form></td></tr>
    <? } ?>
  <? } ?>
  <? if ($count == 0) { ?>
  <tr><td colspan="3" style="padding: 20px;">There are currently no private networks defined.</td></tr>
  <? } ?>
  </table>
  <h3>Create new Private Network</h3>
  <form method="post">
   Name: <input type="text" name="private_vlan_name" value="<?= $errors ? $_POST['private_vlan_name'] : "" ?>"/> <input type="submit" name="do_create" value="Create"/>
  </form>
 </body>
</html>