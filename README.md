#VirtualBox plugin for unRAID v5 and v6

The plugin installs both VirtualBox package and the VirtualBox Extension package (it does not install phpVirtualBox).

***If this is your first plugin upgrade from version 0.5.7 or before to a newer version (supporting unRAID v6 plugin manager) then I suggest deleting the existing virtualbox.plg file at /boot/config/plugins and then install as described below for unRAID v6 - all your settings should remain intact.***

##To install under unRAID v6:
1. In the unRAID Plugin Manager under "Install Plugin" tab enter https://raw.githubusercontent.com/theone11/virtualbox_plugin/master/virtualbox.plg
2. Wait for installation to complete.
3. Go to plugin WEGUI and change initial settings

##To install under unRAID v5:
1. Initial Download of plugin at https://raw.githubusercontent.com/theone11/virtualbox_plugin/master/virtualbox.plg
2. Copy plugin to /boot/config/plugins on your flash drive.
3. Reboot unRAID server or Install from command line:
   - installplg /boot/config/plugins/virtualbox.plg
   - /etc/rc.d/rc.virtualbox boot
4. Go to plugin WEGUI and change initial settings

##To update the plugin:
* For WEBUI and functionality updates - Use the unRAID Plugin Manager or the VirtualBox Plugin WEBUI
* For new Virtualbox compiled packages - Use the VirtualBox Plugin WEBUI

##The WEBUI is divided into 3 parts:
1. Status Summary - Shows all versions of packages and plugin and there status (installed/local/online).
2. Actions - Shows all possible actions available to the user depending on the status of the user's server.
   - Start/Stop/Restart vboxwebsrv and Virtual Machines.
   - Download/Install/Update packages and Plugin.
   - Start and Savestate of individual Virtual Machines.
3. Configuration - Change settings of the plugin.

##Configuration Notes:
1. Boot and Startup options - Change what happens during boot or installplg.
2. Virtual Machines start options - Change what Virtual Machines to start once START command is given.
3. VBOXWEBSRV Host and Port options - Change Host and IP settings for vboxwebsrv
4. VBOXWEBSRV log file options - Change the vboxwebsrv log file options.
   - It is important to set the log file location accurately otherwise vboxwebsrv will not run properly.
   - You may want to place the log file outside of your flash drive.

Please comment on any problems encountered and any enhancements or missing features, that you would like added.
(Here if possible: https://github.com/theone11/virtualbox_plugin/issues)

Enjoy the plugin
