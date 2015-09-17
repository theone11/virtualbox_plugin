<?PHP
shell_exec("/etc/rc.d/rc.virtualbox getonlineversion");
shell_exec("/etc/rc.d/rc.virtualbox getlocalversion");
shell_exec("/etc/rc.d/rc.virtualbox getplgversions");

$virtualbox_cfg = parse_ini_file("/boot/config/plugins/virtualbox/virtualbox.cfg");
$virtualbox_status = parse_ini_file("/usr/local/emhttp/plugins/virtualbox/virtualbox.status");

$start_vms_on_start = $virtualbox_cfg['START_VMS_ON_START'];

$unraid_krnl_ver = $virtualbox_status['UNRAID_KERNEL_VER'];
$unraid_os_bits = $virtualbox_status['UNRAID_OS_BITS'];

$org_server = $virtualbox_status['VBOX_ORG_HOSTING_SERVER_EXISTS'];
$org_online_ver = $virtualbox_status['VBOX_ORG_ONLINE_VER'];

$pkg_server = $virtualbox_status['VBOX_PKG_HOSTING_SERVER_EXISTS'];
$pkg_online_exist = $virtualbox_status['VBOX_PKG_ONLINE_EXIST'];
$pkg_online_ver = $virtualbox_status['VBOX_PKG_ONLINE_VER'];
$pkg_latest_online_ver = $virtualbox_status['VBOX_PKG_LATEST_ONLINE_VER'];
$pkg_latest_online_krnl_ver = $virtualbox_status['VBOX_PKG_LATEST_ONLINE_KERNEL_VER'];
$pkg_loc_ver = $virtualbox_status['VBOX_PKG_LOCAL_VER'];
$pkg_inst_ver = $virtualbox_status['VBOX_PKG_INSTALLED_VER'];
$pkg_loc_krnl_ver = $virtualbox_status['VBOX_PKG_LOCAL_KERNEL'];

$ext_server = $virtualbox_status['VBOX_EXT_HOSTING_SERVER_EXISTS'];
$ext_online_exist = $virtualbox_status['VBOX_EXT_ONLINE_EXIST'];
$ext_online_ver = $virtualbox_status['VBOX_EXT_ONLINE_VER'];
$ext_loc_ver = $virtualbox_status['VBOX_EXT_LOCAL_VER'];
$ext_inst_ver = $virtualbox_status['VBOX_EXT_INSTALLED_VER'];

$gad_server = $virtualbox_status['VBOX_GAD_HOSTING_SERVER_EXISTS'];
$gad_online_exist = $virtualbox_status['VBOX_GAD_ONLINE_EXIST'];
$gad_online_ver = $virtualbox_status['VBOX_GAD_ONLINE_VER'];
$gad_loc_ver = $virtualbox_status['VBOX_GAD_LOCAL_VER'];

$plg_server = $virtualbox_status['VBOX_PLG_HOSTING_SERVER_EXISTS'];
$plg_online_exist = $virtualbox_status['VBOX_PLG_ONLINE_EXIST'];
$plg_online_ver = $virtualbox_status['VBOX_PLG_ONLINE_VER'];
$plg_loc_ver = $virtualbox_status['VBOX_PLG_LOCAL_VER'];

$virtualbox_running = ((file_exists("/var/run/vboxwebsrv/vboxwebsrv.pid")) || ($vm_run_cnt > 0)) ? "yes" : "no";

if (file_exists("/boot/config/plugins/virtualbox/vms_session.cfg"))
{
  $vm_session = file("/boot/config/plugins/virtualbox/vms_session.cfg", FILE_IGNORE_NEW_LINES);
  $vm_session_cnt = count($vm_session);
}
else
{
  $vm_session = null;
  $vm_session_cnt = 0;
}  

if (file_exists("/opt/VirtualBox/VBoxManage"))
{
  shell_exec("/opt/VirtualBox/VBoxManage list vms > /tmp/vm_avl.txt 2>&1");
  shell_exec("/opt/VirtualBox/VBoxManage list runningvms > /tmp/vm_run.txt 2>&1");
  $vm_avl = file("/tmp/vm_avl.txt", FILE_IGNORE_NEW_LINES);
  $vm_run = file("/tmp/vm_run.txt", FILE_IGNORE_NEW_LINES);
  shell_exec("rm --force /tmp/vm_avl.txt > /dev/null 2>&1");
  shell_exec("rm --force /tmp/vm_run.txt > /dev/null 2>&1");
  $vm_avl_cnt = count($vm_avl);
  $vm_run_cnt = count($vm_run);
}
else
{
  $vm_avl = null;
  $vm_run = null;
  $vm_avl_cnt = 0;
  $vm_run_cnt = 0;
}

for ($i=0; $i<$vm_session_cnt; $i++)
{
$vm_session_uuid[$i] = substr($vm_session[$i], (strpos($vm_session[$i], "{")+1), (strpos($vm_session[$i], "}")-strpos($vm_session[$i], "{")-1));
}
for ($i=0; $i<$vm_avl_cnt; $i++)
{
$vm_avl_name[$i] = substr($vm_avl[$i], 1, (strpos($vm_avl[$i], " {")-2));
$vm_avl_uuid[$i] = substr($vm_avl[$i], (strpos($vm_avl[$i], "{")+1), (strpos($vm_avl[$i], "}")-strpos($vm_avl[$i], "{")-1));
}
for ($i=0; $i<$vm_run_cnt; $i++)
{
$vm_run_uuid[$i] = substr($vm_run[$i], (strpos($vm_run[$i], "{")+1), (strpos($vm_run[$i], "}")-strpos($vm_run[$i], "{")-1));
}

$control_actions_exist = "false";
$version_actions_exist = "false";

if ($unraid_krnl_ver < "4.1.5")
{
  $path_prefix = "/usr/local/emhttp";
}
else
{
  $path_prefix = "";
}

?>

<HTML>
<HEAD></HEAD>
<BODY>

<div style="width: 49%; float:left; border: 0px solid black;">
  <div id="title">
    <span class="left">Status</span>
  </div>

  <div style="border: 0px solid black;">
    <span class="left">
      <p>
        Latest VirtualBox version on <u><a href="https://www.virtualbox.org/" target="_blank">VirtualBox.org</a></u>: 
        <?if ($org_server=="0"):?>
          <span class="green-text"><b>v<?=$org_online_ver;?></b></span>
        <?else:?>
          <span class="red-text"><b>VirtualBox Site Down</b></span>
        <?endif;?>
      </p>
      <p>
        Currently running unRAID Kernel: <span class="green-text"><b>v<?=$unraid_krnl_ver;?> <?=$unraid_os_bits;?>bit</b></p></span>
      <p>
        Latest VirtualBox for unRAID <?=$unraid_os_bits;?>bit on <u><a href="http://vbox.a1aina.com/" target="_blank">vbox.a1aina.com</a></u>: 
        <?if ($pkg_server=="0"):?>
          <?if (($pkg_latest_online_ver=="") || ($pkg_latest_online_krnl_ver=="")):?>
            <span class="red-text"><b>No <?=$unraid_os_bits;?>bit version available</b></span>
          <?else:?>
            VirtualBox <span class="green-text"><b>v<?=$pkg_latest_online_ver;?></b></span> for unRAID Kernel <span class="green-text"><b>v<?=$pkg_latest_online_krnl_ver;?></b></span>
          <?endif;?>
        <?else:?>
          <span class="red-text"><b>a1aina Site Down</b></span>
        <?endif;?>
      </p>
      <p>
        <b><u>Note:</u></b> Do not upgrade unRAID if it uses a newer kernel version, unless you make sure that there exists a matching
        compiled VirtualBox package for this kernel version at <u><a href="http://vbox.a1aina.com/" target="_blank">a1aina</a></u>.
        (hosting server courtesy of <b>lainie</b>)
      </p>
      <p>unRAID forum thread for <u><a href="http://lime-technology.com/forum/index.php?topic=25715.0" target="_blank">VirtualBox Plugin for unRAID v5 and v6</a></u></p>
    </span>
  </div>

  <br></br>

  <div style="border: 0px solid black;">
    <table>
      <tr style="font-weight:bold; color:#333333; background:#F0F0F0; text-shadow:0 1px 1px #FFFFFF;">
        <td>Package</td>
        <td>Online Version</td>
        <td>Local Version</td>
        <td>Installed Version</td>
      </tr>
      <tr style="font-weight:bold; background:#FFFFFF;">
        <td>VirtualBox</td>
        <td>
          <?if ($pkg_server=="0"):?>
            <?if ($pkg_online_exist=="0"):?>
              <span class="green-text">v<?=$pkg_online_ver;?></span>
            <?else:?>
              <span class="red-text">No online package</span>
            <?endif;?>
          <?else:?>
            <span class="red-text">OFFLINE</span>
          <?endif;?>
        </td>
        <td>
          <?if ($pkg_loc_ver!=""):?>
            <?if ($pkg_loc_krnl_ver==$unraid_krnl_ver):?>
              <span class="green-text">v<?=$pkg_loc_ver;?></span>
            <?else:?>
              <span class="red-text">Kernel v<?=$pkg_loc_krnl_ver;?></span>
            <?endif;?>
          <?else:?>
            <span class="red-text">No local package</span>
          <?endif;?>
        </td>
        <td>
          <?if ($pkg_inst_ver!="not_installed"):?>
            <span class="green-text">v<?=$pkg_inst_ver;?></span>
          <?else:?>
            <span class="red-text">Not Installed</span>
          <?endif;?>
        </td>
      </tr>
      <tr style="font-weight:bold; background:#FFFFFF;">
        <td>VirtualBox Extension</td>
        <td>
          <?if ($ext_server=="0"):?>
            <?if ($ext_online_exist=="0"):?>
              <span class="green-text">v<?=$ext_online_ver;?></span>
            <?else:?>
              <span class="red-text">No online package</span>
            <?endif;?>
          <?else:?>
            <span class="red-text">OFFLINE</span>
          <?endif;?>
        </td>
        <td>
          <?if ($ext_loc_ver!=""):?>
            <span class="green-text">v<?=$ext_loc_ver;?></span>
          <?else:?>
            <span class="red-text">No local package</span>
          <?endif;?>
        </td>
        <td>
          <?if ($ext_inst_ver!="not_installed"):?>
            <span class="green-text">v<?=$ext_inst_ver;?></span>
          <?else:?>
            <span class="red-text">Not Installed</span>
          <?endif;?>
        </td>
      </tr>
      <tr style="font-weight:bold; background:#FFFFFF;">
        <td>VirtualBox Guest Additions</td>
        <td>
          <?if ($gad_server=="0"):?>
            <?if ($gad_online_exist=="0"):?>
              <span class="green-text">v<?=$gad_online_ver;?></span>
            <?else:?>
              <span class="red-text">No online package</span>
            <?endif;?>
          <?else:?>
            <span class="red-text">OFFLINE</span>
          <?endif;?>
        </td>
        <td>
          <?if ($gad_loc_ver!=""):?>
            <span class="green-text">v<?=$gad_loc_ver;?></span>
          <?else:?>
            <span class="red-text">No local package</span>
          <?endif;?>
        </td>
        <td>
        </td>
      </tr>
      <tr style="font-weight:bold; background:#FFFFFF;">
        <td>VirtualBox Plugin</td>
        <td>
          <?if ($plg_server=="0"):?>
            <?if ($plg_online_exist=="0"):?>
              <span class="green-text">v<?=$plg_online_ver;?></span>
            <?else:?>
              <span class="red-text">No online plugin</span>
            <?endif;?>
          <?else:?>
            <span class="red-text">OFFLINE</span>
          <?endif;?>
        </td>
        <td>
          <?if ($plg_loc_ver!="no_local_plg"):?>
            <span class="green-text">v<?=$plg_loc_ver;?></span>
          <?else:?>
            <span class="red-text">No local plugin</span>
          <?endif;?>
        </td>
        <td>
        </td>
      </tr>
    </table>
  </div>

  <div id="title">
    <span class="left">Actions</span>
  </div>

  <br></br>

  <div>
    <table>
      <tr style="font-weight:bold; color:#333333; background:#F0F0F0; text-shadow:0 1px 1px #FFFFFF;">
        <td colspan="2">Control Actions</td>
      </tr>
      <?if ((($pkg_inst_ver == "not_installed") || ($ext_inst_ver == "not_installed")) && ($pkg_loc_krnl_ver==$unraid_krnl_ver) && ($pkg_loc_ver==$ext_loc_ver) && ($pkg_loc_ver!="")):?>
        <tr>
          <td width="30%">
            <form name="install" method="POST" action="/update.htm" target="progressFrame">
              <input type="hidden" name="cmd" value="<?=$path_prefix;?>/plugins/virtualbox/scripts/rc.virtualbox">
              <input type="hidden" name="arg1" value="install"/>
              <input type="submit" name="runCmd" value="Install">
            </form>
          </td>
          <td>Install VirtualBox and/or Virtual Extension packages</td>
        </tr>
        <?$control_actions_exist = "true"?>
      <?endif;?>
      <?if (($pkg_inst_ver != "not_installed") && ($ext_inst_ver != "not_installed") && (! (file_exists("/var/run/vboxwebsrv/vboxwebsrv.pid"))) && ($vm_session_cnt > 0) && ($vm_run_cnt == 0) && ($start_vms_on_start != "false")):?>
        <tr>
          <td width="30%">
            <form name="start_vboxwebsrv" method="POST" action="/update.htm" target="progressFrame">
              <input type="hidden" name="cmd" value="<?=$path_prefix;?>/plugins/virtualbox/scripts/rc.virtualbox">
              <input type="hidden" name="arg1" value="start"/>
              <input type="submit" name="runCmd" value="Start vboxwebsrv & VMs">
            </form>
          </td>
          <td>Start VirtualBox vboxwebsrv (needed for phpVirtualBox) and Virtual Machines</td>
        </tr>
        <?$control_actions_exist = "true"?>
      <?endif;?>
      <?if (($pkg_inst_ver != "not_installed") && ($ext_inst_ver != "not_installed") && (! (file_exists("/var/run/vboxwebsrv/vboxwebsrv.pid")))):?>
        <tr>
          <td width="30%">
            <form name="start_vboxwebsrv" method="POST" action="/update.htm" target="progressFrame">
              <input type="hidden" name="cmd" value="<?=$path_prefix;?>/plugins/virtualbox/scripts/rc.virtualbox">
              <input type="hidden" name="arg1" value="start_vboxwebsrv"/>
              <input type="submit" name="runCmd" value="Start vboxwebsrv">
            </form>
          </td>
          <td>Start VirtualBox vboxwebsrv (needed for phpVirtualBox)</td>
        </tr>
        <?$control_actions_exist = "true"?>
      <?endif;?>
      <?if (($pkg_inst_ver != "not_installed") && ($ext_inst_ver != "not_installed") && ($vm_session_cnt > 0) && ($vm_run_cnt == 0) && ($start_vms_on_start != "false")):?>
        <tr>
          <td width="30%">
            <form name="start_vms" method="POST" action="/update.htm" target="progressFrame">
              <input type="hidden" name="cmd" value="<?=$path_prefix;?>/plugins/virtualbox/scripts/rc.virtualbox">
              <input type="hidden" name="arg1" value="start_vms"/>
              <input type="submit" name="runCmd" value="Start VMs">
            </form>
          </td>
          <td>Start <?=(($start_vms_on_start == "previous") ? "previous Virtual Machines session" : "all available Virtual Machines");?></td>
        </tr>
        <?$control_actions_exist = "true"?>
      <?endif;?>
      <?if ((file_exists("/var/run/vboxwebsrv/vboxwebsrv.pid")) || ($vm_run_cnt > 0)):?>
        <tr>
          <td width="30%">
            <form name="restart" method="POST" action="/update.htm" target="progressFrame">
              <input type="hidden" name="cmd" value="<?=$path_prefix;?>/plugins/virtualbox/scripts/rc.virtualbox">
              <input type="hidden" name="arg1" value="restart"/>
              <input type="submit" name="runCmd" value="Restart">
            </form>
          </td>
          <td>Restart vboxwebsrv and all running Virtual Machines</td>
        </tr>
        <?$control_actions_exist = "true"?>
      <?endif;?>
      <?if ((file_exists("/var/run/vboxwebsrv/vboxwebsrv.pid")) || ($vm_run_cnt > 0)):?>
        <tr>
          <td width="30%">
            <form name="stop" method="POST" action="/update.htm" target="progressFrame">
              <input type="hidden" name="cmd" value="<?=$path_prefix;?>/plugins/virtualbox/scripts/rc.virtualbox">
              <input type="hidden" name="arg1" value="stop"/>
              <input type="submit" name="runCmd" value="Stop">
            </form>
          </td>
          <td>Stop vboxwebsrv and all running Virtual Machines</td>
        </tr>
        <?$control_actions_exist = "true"?>
      <?endif;?>
      <?if ($control_actions_exist=="false"):?>
        <tr>
          <td colspan="2" align="center">No Control Actions available</td>
        </tr>
      <?endif;?>
    </table>
  </div>

  <br></br>

  <div style="border: 0px solid black;">
    <table>
      <tr style="font-weight:bold; color:#333333; background:#F0F0F0; text-shadow:0 1px 1px #FFFFFF;">
        <td colspan="3">Version Actions</td>
      </tr>
      <?if ((($pkg_online_exist=="0") && ($ext_online_exist=="0")) && (($pkg_loc_krnl_ver!=$unraid_krnl_ver) || ($pkg_loc_ver!=$pkg_online_ver) || ($ext_loc_ver!=$ext_online_ver))):?>
        <tr>
          <td colspan="1">VirtualBox ONLINE version different than LOCAL version</td>
		      <td>
            <form name="download" method="POST" action="/update.htm" target="progressFrame">
              <input type="hidden" name="cmd" value="<?=$path_prefix;?>/plugins/virtualbox/scripts/rc.virtualbox">
              <input type="hidden" name="arg1" value="download"/>
              <input type="submit" name="runCmd" value="Download Only">
            </form>
          </td>
          <td>
          <?if (($pkg_inst_ver=="not_installed") || ($ext_inst_ver=="not_installed")):?>
            <form name="install3" method="POST" action="/update.htm" target="progressFrame">
              <input type="hidden" name="cmd" value="<?=$path_prefix;?>/plugins/virtualbox/scripts/rc.virtualbox">
              <input type="hidden" name="arg1" value="update"/>
              <input type="submit" name="runCmd" value="Download & Install">
            </form>
          <?else:?>
            <form name="update3" method="POST" action="/update.htm" target="progressFrame">
              <input type="hidden" name="cmd" value="<?=$path_prefix;?>/plugins/virtualbox/scripts/rc.virtualbox">
              <input type="hidden" name="arg1" value="update"/>
              <input type="submit" name="runCmd" value="Update (will restart VirtualBox)">
            </form>
          <?endif;?>
          </td>
		    </tr>
        <?$version_actions_exist="true"?>
      <?endif;?>
      <?if (($gad_online_exist=="0") && ($gad_loc_ver!=$pkg_loc_ver)):?>
        <tr>
          <td colspan="2">Guest Additions ONLINE version different than LOCAL version</td>
		      <td>
            <form name="downloadgad" method="POST" action="/update.htm" target="progressFrame">
              <input type="hidden" name="cmd" value="<?=$path_prefix;?>/plugins/virtualbox/scripts/rc.virtualbox">
              <input type="hidden" name="arg1" value="downloadgad"/>
              <input type="submit" name="runCmd" value="Download Only">
            </form>
          </td>
		    </tr>
        <?$version_actions_exist="true"?>
      <?endif;?>
      <?if ((($pkg_inst_ver!="not_installed") && ($ext_inst_ver!="not_installed")) && (($pkg_online_exist=="0") && ($ext_online_exist=="0")) && (($pkg_inst_ver!=$pkg_online_ver) || ($ext_inst_ver!=$ext_online_ver))):?>
        <tr>
          <td colspan="2">VirtualBox ONLINE version different than INSTALLED version</td>
          <td>
            <form name="update2" method="POST" action="/update.htm" target="progressFrame">
              <input type="hidden" name="cmd" value="<?=$path_prefix;?>/plugins/virtualbox/scripts/rc.virtualbox">
              <input type="hidden" name="arg1" value="update"/>
              <input type="submit" name="runCmd" value="Update (will restart VirtualBox)">
            </form>
          </td>
		    </tr>
        <?$version_actions_exist="true"?>
      <?endif;?>
      <?if ((($pkg_inst_ver!="not_installed") && ($ext_inst_ver!="not_installed")) && ($pkg_loc_krnl_ver==$unraid_krnl_ver) && ($pkg_loc_ver==$ext_loc_ver) && ($pkg_loc_ver!="") && (($pkg_loc_ver!=$pkg_inst_ver) || ($ext_loc_ver!=$ext_inst_ver))):?>
        <tr>
          <td colspan="2">VirtualBox LOCAL version different than INSTALLED version</td>
          <td>
            <form name="install2" method="POST" action="/update.htm" target="progressFrame">
              <input type="hidden" name="cmd" value="<?=$path_prefix;?>/plugins/virtualbox/scripts/rc.virtualbox">
              <input type="hidden" name="arg1" value="update"/>
              <input type="submit" name="runCmd" value="Update (will restart VirtualBox)">
            </form>
          </td>
        </tr>
        <?$version_actions_exist="true"?>
      <?endif;?>
      <?if (($plg_online_exist=="0") && ($plg_online_ver!=$plg_loc_ver)):?>
        <tr>
          <td colspan= "2">Plugin ONLINE version different than Plugin LOCAL version</td>
          <td>
            <form name="updateplg" method="POST" action="/update.htm" target="progressFrame">
              <input type="hidden" name="cmd" value="<?=$path_prefix;?>/plugins/virtualbox/scripts/rc.virtualbox">
              <input type="hidden" name="arg1" value="updateplg"/>
              <input type="submit" name="runCmd" value="Update Plugin">
            </form>
          </td>
        </tr>
        <?$version_actions_exist="true"?>
      <?endif;?>
      <?if ($version_actions_exist=="false"):?>
        <tr>
          <td colspan="3" align="center">No Version Actions available</td>
        </tr>
      <?endif;?>
    </table>
  </div>

  <br></br>

  <?if (($pkg_inst_ver != "not_installed") && ($ext_inst_ver != "not_installed")):?>
    <div style="border: 0px solid black;">
      <table>
        <tr style="font-weight:bold; color:#333333; background:#F0F0F0; text-shadow:0 1px 1px #FFFFFF;">
          <td colspan="4">Virtual Machines</td>
        </tr>
        <?if ($vm_avl_cnt!=0):?>
          <tr style="font-weight:bold; color:#333333; background:#FFFFFF; text-shadow:0 1px 1px #FFFFFF;">
            <td><u>Available</u></td>
            <td align="center"><u>Running</u></td>
            <td align="center"><u>Action</u></td>
            <td align="center"><u>Part of Session</u></td>
          </tr>
          <?for ($i=0; $i<$vm_avl_cnt; $i++):?>
            <tr>
              <td><?=$vm_avl_name[$i];?></td>
              <?$already_running = "false";?>
              <?if ($vm_run_cnt!=0):?>
                <?foreach ($vm_run_uuid as &$vm_run_line):?>
                  <?if ($vm_avl_uuid[$i] == $vm_run_line):?>
                    <?$already_running = "true";?>
                  <?endif;?>
                <?endforeach?>
              <?endif;?>
              <?if ($already_running == "false"):?>
                <td align="center" class="orange-text"><b>&#10006</b></td>
                <td align="center">
                  <form name="start_vm<?=$i;?>" method="POST" action="/update.htm" target="progressFrame">
                    <input type="hidden" name="cmd" value="<?=$path_prefix;?>/plugins/virtualbox/scripts/rc.virtualbox">
                    <input type="hidden" name="arg1" value="start_vm"/>
                    <input type="hidden" name="arg2" value="<?=$vm_avl_uuid[$i];?>"/>
                    <input type="submit" name="runCmd" value="Start VM">
                  </form>
                </td>
              <?else:?>
                <td align="center" class="green-text"><b>&#10004</b></td>
                <td align="center">
                  <form name="savestate_vm<?=$i;?>" method="POST" action="/update.htm" target="progressFrame">
                    <input type="hidden" name="cmd" value="<?=$path_prefix;?>/plugins/virtualbox/scripts/rc.virtualbox">
                    <input type="hidden" name="arg1" value="savestate_vm"/>
                    <input type="hidden" name="arg2" value="<?=$vm_avl_uuid[$i];?>"/>
                    <input type="submit" name="runCmd" value="Savestate VM">
                  </form>
                </td>
              <?endif;?>
              <?$in_session = "false";?>
              <?if ($vm_session_cnt!=0):?>
                <?foreach ($vm_session_uuid as &$vm_session_line):?>
                  <?if ($vm_avl_uuid[$i] == $vm_session_line):?>
                    <?$in_session = "true";?>
                  <?endif;?>
                <?endforeach?>
              <?endif;?>
              <?if ($in_session == "false"):?>
                <td align="center" class="orange-text"><b>&#10006</b></td>
              <?else:?>
                <td align="center" class="green-text"><b>&#10004</b></td>
              <?endif;?>
            </tr>
          <?endfor;?>
        <?else:?>
          <tr>
            <td colspan="3" align="center">No available Virtual Machines</td>
          </tr>
        <?endif;?>
      </table>
    </div>
  <?endif;?>

  <br></br>
  <br></br>

</div>
    
<div style="width: 49%; float:right; border: 0px solid black;">

  <div id="title">
    <span class="left">Configuration</span>
  </div>

  <?if ($virtualbox_running == "yes"):?>
    <div><center><b>To access all configuration options - Press Stop in "Control Action"</b></center></div>
  <?endif;?>
  <br></br>

  <div>
    <form name="virtualbox_settings" method="POST" action="/plugins/virtualbox/php/virtualbox_submit.php" target="progressFrame" onsubmit="validateForm();">
      <input type="hidden" name="cmd" value="apply">
      <table>
        <tr>
          <td colspan="2" align="center">
            <input type="submit" name="runCmd" value="Save Below Configuration">
            <button type="button" onClick="done();">Return to unRAID Settings Page</button>
          </td>
        </tr>
        <tr style="font-weight:bold; color:#333333; background:#F0F0F0; text-shadow:0 1px 1px #FFFFFF;">
          <td colspan="2">Mount and Startup options</td>
        </tr>
        <tr>
          <td>Install VirtualBox during array mount:</td>
          <td>
            <select name="INSTALL_ON_BOOT" id="INSTALL_ON_BOOT" size="1">
              <?=mk_option($virtualbox_cfg['INSTALL_ON_BOOT'], "true", "Yes");?>
              <?=mk_option($virtualbox_cfg['INSTALL_ON_BOOT'], "false", "No");?>
            </select>
          </td>
        </tr>
        <tr>
          <td>Check & Update VirtualBox during array mount:</td>
          <td>
            <select name="UPGRADE_ON_BOOT" id="UPGRADE_ON_BOOT" size="1">
              <?=mk_option($virtualbox_cfg['UPGRADE_ON_BOOT'], "true", "Yes");?>
              <?=mk_option($virtualbox_cfg['UPGRADE_ON_BOOT'], "false", "No");?>
            </select>
          </td>
        </tr>
        <tr>
          <td>Check & Update Plugin during array mount:</td>
          <td>
            <select name="UPGRADE_PLG_ON_BOOT" id="UPGRADE_PLG_ON_BOOT" size="1">
              <?=mk_option($virtualbox_cfg['UPGRADE_PLG_ON_BOOT'], "true", "Yes");?>
              <?=mk_option($virtualbox_cfg['UPGRADE_PLG_ON_BOOT'], "false", "No");?>
            </select>
          </td>
        </tr>
        <tr>
          <td>Start VMs and vboxwebsrv during array mount:</td>
          <td>
            <select name="START_ON_ARRAY_MOUNT" id="START_ON_ARRAY_MOUNT" size="1">
              <?=mk_option($virtualbox_cfg['START_ON_ARRAY_MOUNT'], "true", "Yes");?>
              <?=mk_option($virtualbox_cfg['START_ON_ARRAY_MOUNT'], "false", "No");?>
            </select>
          </td>
        </tr>
        <tr>
          <td>Which Virtual Machines should be automatically started:</td>
          <td>
          <select name="START_VMS_ON_START" id="START_VMS_ON_START" size="1">
            <?=mk_option($virtualbox_cfg['START_VMS_ON_START'], "false", "None");?>
            <?=mk_option($virtualbox_cfg['START_VMS_ON_START'], "all", "All");?>
            <?=mk_option($virtualbox_cfg['START_VMS_ON_START'], "previous", "Previous Session");?>
          </select>
          </td>
        </tr>
        <tr>
          <td>VirtualBox symbolic link (part of install package) - Change from default /boot/custom/vbox:</td>
          <td><input type="text" name="VBOX_SYMLINK_LOCATION" id="VBOX_SYMLINK_LOCATION" style="width: 17em;" maxlength="255" value="<?=$virtualbox_cfg['VBOX_SYMLINK_LOCATION'];?>"></td>
        </tr>
        <tr style="font-weight:bold; color:#333333; background:#F0F0F0; text-shadow:0 1px 1px #FFFFFF;">
          <td colspan="2">VBOXWEBSRV Host and Port options</td>
        </tr>
        <tr>
          <td>vboxwebsrv Host IP:</td>
          <td><input type="text" name="VBOXWEBSRV_HOST_IP" id="VBOXWEBSRV_HOST_IP" style="width: 5em;" maxlength="15" value="<?=$virtualbox_cfg['VBOXWEBSRV_HOST_IP'];?>"></td>
        </tr>
        <tr>
          <td>vboxwebsrv Port:</td>
          <td><input type="text" name="VBOXWEBSRV_HOST_PORT" id="VBOXWEBSRV_HOST_PORT" style="width: 3em;" maxlength="5" value="<?=$virtualbox_cfg['VBOXWEBSRV_HOST_PORT'];?>"></td>
        </tr>
        <tr style="font-weight:bold; color:#333333; background:#F0F0F0; text-shadow:0 1px 1px #FFFFFF;">
          <td colspan="2">VBOXWEBSRV log file options (see <u><a href="http://download.virtualbox.org/virtualbox/SDKRef.pdf" target="_blank">Command line options of vboxwebsrv - Page 21</a></u>)</td>
        </tr>
        <tr>
          <td>vboxwebsrv log file location (must be valid location):</td>
          <td><input type="text" name="VBOXWEBSRV_LOGFILE" id="VBOXWEBSRV_LOGFILE" style="width: 17em;" maxlength="255" value="<?=$virtualbox_cfg['VBOXWEBSRV_LOGFILE'];?>"></td>
        </tr>
        <tr>
          <td>vboxwebsrv log file "--verbose" mode:</td>
          <td>
          <select name="VBOXWEBSRV_VERBOSE" id="VBOXWEBSRV_VERBOSE" size="1">
            <?=mk_option($virtualbox_cfg['VBOXWEBSRV_VERBOSE'], "true", "Yes");?>
            <?=mk_option($virtualbox_cfg['VBOXWEBSRV_VERBOSE'], "false", "No");?>
          </select>
          </td>
        </tr>
        <tr>
          <td>vboxwebsrv log file rotation "LOGROTATE" option:</td>
          <td><input type="text" name="VBOXWEBSRV_LOGROTATE" id="VBOXWEBSRV_LOGROTATE" style="width: 3em;" maxlength="10" value="<?=$virtualbox_cfg['VBOXWEBSRV_LOGROTATE'];?>"></td>
        </tr>
        <tr>
          <td>vboxwebsrv log file size "LOGSIZE" option:</td>
          <td><input type="text" name="VBOXWEBSRV_LOGSIZE" id="VBOXWEBSRV_LOGSIZE" style="width: 4em;" value="<?=$virtualbox_cfg['VBOXWEBSRV_LOGSIZE'];?>"></td>
        </tr>
        <tr>
          <td>vboxwebsrv log file interval "LOGINTERVAL" option:</td>
          <td><input type="text" name="VBOXWEBSRV_LOGINTERVAL" id="VBOXWEBSRV_LOGINTERVAL" style="width: 4em;" maxlength="10" value="<?=$virtualbox_cfg['VBOXWEBSRV_LOGINTERVAL'];?>"></td>
        </tr>
      </table>
    </form>
  </div>

  <br></br>
  <br></br>

</div>

<script type="text/javascript">
function validateForm() {
  document.getElementById('INSTALL_ON_BOOT').disabled = false;
  document.getElementById('UPGRADE_ON_BOOT').disabled = false;
  document.getElementById('UPGRADE_PLG_ON_BOOT').disabled = false;
  document.getElementById('START_ON_ARRAY_MOUNT').disabled = false;
  document.getElementById('START_VMS_ON_START').disabled = false;
  document.getElementById('VBOX_SYMLINK_LOCATION').disabled = false;
  document.getElementById('VBOXWEBSRV_HOST_IP').disabled = false;
  document.getElementById('VBOXWEBSRV_HOST_PORT').disabled = false;
  document.getElementById('VBOXWEBSRV_LOGFILE').disabled = false;
  document.getElementById('VBOXWEBSRV_VERBOSE').disabled = false;
  document.getElementById('VBOXWEBSRV_LOGROTATE').disabled = false;
  document.getElementById('VBOXWEBSRV_LOGSIZE').disabled = false;
  document.getElementById('VBOXWEBSRV_LOGINTERVAL').disabled = false;
}

function checkRUNNING(form) {
  if ("<?=$virtualbox_running?>" == "yes") {
    form.VBOX_SYMLINK_LOCATION.disabled = true;
    form.VBOXWEBSRV_HOST_IP.disabled = true;
    form.VBOXWEBSRV_HOST_PORT.disabled = true;
    form.VBOXWEBSRV_LOGFILE.disabled = true;
    form.VBOXWEBSRV_VERBOSE.disabled = true;
    form.VBOXWEBSRV_LOGROTATE.disabled = true;
    form.VBOXWEBSRV_LOGSIZE.disabled = true;
    form.VBOXWEBSRV_LOGINTERVAL.disabled = true;
  }
}

checkRUNNING(document.virtualbox_settings);
</script>

</BODY>
</HTML>
