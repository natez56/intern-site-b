Achieve Internet intern site.

Daniel Silverman  
Namratha Subramanya  
Tim Taing  
Ryan Kang  
Nathan Zimmerman

How to install and run with lando
1. git clone the repo
2. lando init inside the intern-site-b directory
 - Set webroot as ./web
3. lando start
4. composer install
5. Open site and install drupal
6. Go to configuration -> shortcuts ->list links in the newly installed drupal site and delete the two shortcuts
7. In your settings.php make sure your sync directory is set to ../config/sync
 - $config_directories['sync'] = '../config/sync';
8. Set you site uuid by running the command found in the intern-site-b/backup/site-id.txt
7. Run lando drush cim
8. Unzip the files .tar file in the intern-site-b/backup folder and replace the files folder located in web/sites/default (you may need to change the default folder file permissions to 775)
10. If you have a settings.php and custom service files in your default folder from another install you can paste them into this projects default folder.
11. Navigate to {yoursite}/admin/config  -> backup and migrate and restore the database using the /intern-site-b/backup database zip file.
