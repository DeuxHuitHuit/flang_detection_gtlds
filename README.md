# FLang detection gTLDs

Adds frontend language detection through .htaccess file.

- Version: 1.1
- Date: 2012-05-14
- Requirements:
	* Symphony 2.3
	* [Frontend Localisation](http://github.com/vlad-ghita/frontend_localisation)
- Author: [Vlad Ghita](vlad_micutul@yahoo.com)
- GitHub Repository: <http://github.com/vlad-ghita/flang_detection_gtlds>

Credits to [@klaftertief](http://github.com/klaftertief) for `.htaccess` manipulation.


## 1. About

This extension adds language detection to .htaccess file.


## 2 Installation

1. Upload the `flang_detection_gtlds` folder found in this archive to your Symphony `extensions` folder.
2. Enable it by selecting `FLang detection gTLDs` under `System -> Extensions`, choose `Enable/Install` from the with-selected menu, then click Apply.
3. Everything is done behind the scenes.


## 3 Usage

An URL must follow this convention:

    www.site.com/$LANGUAGE_CODE$/...

    $LANGUAGE_CODE$ = $LANGUAGE$-$REGION$
    $LANGUAGE$ = mandatory
    $REGION$ = optional

Valid URLs:

    www.site.com/en/
    www.site.com/en-us/
