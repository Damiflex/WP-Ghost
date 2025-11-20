#Ghosts Users for Wordpress
unmaintained

⚠️ This repository has been archived and is no longer actively maintained.
What this means:
No further updates, bug fixes, or security patches will be provided.
Issues and pull requests will not be addressed.
Use this code at your own risk.
This set of functions allow to create hidden and uneditable users for wordpress.

#Description
You can create how many users (ghosts) that you want with custom details: Username, Role, Password and email. For this users will be:

hidden visibility on the users list in WP backend
disabled password reset ability
disabled password edit ability
automatically recreate all users profiles when deleted

#Usage
Three steps to use it:

Put the file in your WP theme folder.
Include it in functions.php file pasting this command at the end of file:
   include_once('ghosts-functions.php');
Populate the Ghosts array in first lines of ghosts-functions.php
