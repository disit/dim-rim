**Data Ingestion Manager and RDF Indexing Manager (DIM-RIM)**

Copyright (C) 2015 DISIT Lab http://www.disit.org - University of Florence

**Dependencies**
- Apache Web Server ver. 2 and url rewrite module on
- MySQL 5.x
- PHP 5.x
- Operating system tested: Linux Ubuntu, Windows and OSx
- Tested also with LAMP/XAMPP,  MAMP or WAMP suite.
- Browser tested: Chrome, Firefox, Safari and IE1x
- Don't modify the .htaccess in the root folder since it is required for the url rewrite mechanism

**Installation Guide**

Execute installer.php by typing it on the address bar URL of your apache instance of your browser and follow the wizard instructions:

1. Define MySQL user, password, host and schema Name
2. Install the system package by clicking on Install button
3. Define admin user and password
4. End installation and redirection to the login page.

Then login in the system package using admin credentials (user:admin and password: admin are preconfigured as default). 
Then in the system menu select "Applications" and then click "install" on the displayed boxes (RDFIndexManager and DataIngestionManager)
