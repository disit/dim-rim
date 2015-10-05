**Data Ingestion Manager and RDF Indexing Manager (DIM-RIM)**

Copyright (C) 2015 DISIT Lab http://www.disit.org - University of Florence

**Dependencies**

- Apache Web Server ver. 2 and url rewrite module on
- MySQL 5.x
- PHP 5.x
- Operating system tested: Linux Ubuntu, Windows and OSx
- Tested also with LAMP/XAMPP,  MAMP or WAMP suite.
- Browser tested: Chrome, Firefox, Safari and IE1x
- The .htaccess in the root folder is required for the url rewrite mechanism

**Installation Guide**
Before start the installation you could need to edit the "structure.inc.php" to set "$baseUrl" and "$baseDir" variables. By default both
come with the value "/rim/" and if you define a "rim" folder in your apache server document root (htdocs) this is ready to use. If you are going to change this name or the path, you must edit those variables.

Execute installer.php by typing it on the address bar URL of your apache instance of your browser and follow the wizard instructions:

1. Define MySQL user, password, host and schema Name
2. Install the system package by clicking on Install button
3. Define admin user and password
4. End installation and redirection to the login page.

Login in the system package using admin credentials (user:admin and password: admin are preconfigured as default). 
Go in the Settings menu and select "Plugins" and install "WebTail" and "Virtuoso" or "Owlim" connector plugins.  
Finally in the Settings menu select "Applications" and then click "install" on the displayed boxes (RDFIndexManager and DataIngestionManager).

