<?php
/*
 ldapauth ResourceSpace Plugin
 author: Brian Adams
 email: wreality@gmail.com
 
 revision: 0.2

 *Configuration Information

LDAP (and Active Directory) authentication with this plugin relies on PHP being
compiled with LDAP support.  On Ubuntu this can be accomplished by running:

sudo apt-get install php5-ldap

This plugin implements very _basic_ LDAP authentication by referencing LDAP users
to ResourceSpace users.  

Currently this plugin is designed primarily for Active Directory authentication,
but support for other LDAP implementations could be easily added.  Contact the
author if this development is of interest to you.

As of current, this version requires at least revision 568 from the subversion
respository.  This should be fixed with the next official release.

 * Configuration options

$ldapauth['ldapserver']: IP or FQDN of the active directory server
$ldapauth['port']: Allows specifying non standard port for LDAP server (leave as NULL for AD)
$ldapauth['rootdn']: distinguishedName of a user allowed to browse LDAP (for non-anonymous binds)
$ldapauth['rootpass']: Password for 'rootdn'
$ldapauth['basedn']: DN suffix for all users who are to authenticate.
                     NOTE: for Windows 2003, this _cannot_ be the root DN.
$ldapauth['loginfield']: LDAP field name containing user name. 
                         NOTE: For Active Directory this is 'samaccountname', in most other LDAP
                         servers this will be 'uid'
$ldapauth['usersuffix']: Suffix to append to LDAP users to create RS username.  Allows for 
                         domain users and local RS users to coexist more easily.
$ldapauth['createusers']: true / false.  Determines whether plugin will create RS accounts
                          for users which authenticate on LDAP but do not have a corresponding
                          RS username.

 ***The following options only need to be configured if 'createusers' is set to true.

$ldapauth['groupbased']: true / false.  New users will be created based on group informaion stored
                         in LDAP if this option is set to true.

 *** If 'groupbased' is FALSE:
$ldapauth['newusergroup']: This should be set to the ref of the RS usegroup that new users will
                           be created as members of. (Default RS groups reference is below)

 *** If 'groupbased' is TRUE:
$ldapauth['ldapgroupfield']: for Active Directory this is 'memberof'

The following two options can be duplicated (incrementing the search index) for each group that
needs mapping.  The first matching search is applied.  The last search index should leave
'ldapgroup' set to '' to allow for a failsafe so that all new users will be assigned a RS usergroup.

$ldapauth['newusergroup'][0]['ldapgroup']: Name of the LDAP Group to match
$ldapauth['newusergroup'][0]['rsgroup']: REF of RS group the user will be a member of (Default RS
                                         usergroups reference is below)


*** Default RS Groups ***

ref   name
---   ----
1     Administrator
2     General Users
3     Super Admin
4     Archivists
5     Restricted User

 * Additional Information

As currently configured, LDAP authentication is attempted first, and if successful the plugin
passes login information to the RS login system.  If LDAP authentication fails, authentication
continues with normal RS routines. As such, if a user exists in both contexts the LDAP user
account will authenticate _every_time_ unless the passwords are different.


It is recommended to add new LDAP users to RS with the 'Suggest' password option as LDAP user
passwords will only be used internally to complete authentication.

 * Disclaimer

This plugin has _not_ been completely tested, and no guarentee is made in reference to funtion
or security.

 *** Example configuration for groupbased user creation ***
$ldapauth['ldapserver'] = 'localhost';
$ldapauth['port'] = NULL;
$ldapauth['rootdn'] = 'CN=administrator, CN=users,DC=mydomain,DC=net';
$ldapauth['rootpass'] = 'password';
$ldapauth['basedn']= 'CN=users, DC=mydomain, DC=net';
$ldapauth['loginfield'] = 'samaccountname';
$ldapauth['usersuffix'] = '.domain';
$ldapauth['createusers'] = true;
$ldapauth['ldapgroupfield'] = 'memberof';
$ldapauth['groupbased'] = true;
$ldapauth['newusergroup'][0]['ldapgroup'] = 'domain admin';
$ldapauth['newusergroup'][0]['rsgroup'] = '3';
$ldapauth['newusergroup'][1]['ldapgroup'] = 'users';
$ldapauth['newusergroup'][1]['rsgroup'] = '2';
$ldapauth['newusergroup'][2]['ldapgroup'] = '';
$ldapauth['newusergroup'][2]['rsgroup'] = '5';

*/
if (!isset($use_plugin_manager) || !$use_plugin_manager){
 
$ldapauth['ldapserver'] = 'localhost';
$ldapauth['port'] = NULL;
$ldapauth['rootdn']= 'CN=administrator,CN=users,DC=mydomain,DC=net';
$ldapauth['rootpass']= 'password';
$ldapauth['basedn']= 'CN=users,DC=mydomain,DC=net';
$ldapauth['loginfield'] = 'samaccountname';
$ldapauth['usersuffix'] = '.domain';
$ldapauth['createusers'] = false;
$ldapauth['ldapgroupfield'] = 'memberUid';

}
?>