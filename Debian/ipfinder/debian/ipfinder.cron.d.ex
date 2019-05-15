#
# Regular cron jobs for the ipfinder package
#
0 4	* * *	root	[ -x /usr/bin/ipfinder_maintenance ] && /usr/bin/ipfinder_maintenance
