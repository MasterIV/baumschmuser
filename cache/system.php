<?php

return array (
  'name' => 
  array (
    0 => 'MMO Raid Planner',
    1 => 'Content Management System',
    2 => 'CodeMirror',
    3 => 'Community Tools',
    4 => 'Systemkern',
    5 => 'Default page style',
    6 => 'Bootstrap Adminstyle',
    7 => 'Twig Template Engine',
    8 => 'Update System',
    9 => 'Userverwaltung',
  ),
  'description' => 
  array (
    0 => 'Package description',
    1 => 'Package description',
    2 => 'CodeMirror is a versatile text editor implemented in JavaScript for the browser. It is specialized for editing code, and comes with a number of language modes and addons that implement more advanced editing functionality.',
    3 => 'A set of extensions for the Frontend to build a small Community Page.',
    4 => 'Der Kern des IV Entertainment Frameworks. Enthält alle wichtigen Bibliotheken sowie das Grundgerüst für das Adminpanel.',
    5 => 'Style for the if demo page',
    6 => 'Einfacher Adminstyle, welcher auf der Bootstrap library basiert.',
    7 => 'Template engine used in the cms. Learn more about it on: http://twig.sensiolabs.org/',
    8 => 'Grundlegende Funktionen für das Update System.',
    9 => 'User management',
  ),
  'autor' => 
  array (
    0 => 'Master IV',
    1 => 'Master IV',
    2 => 'kekub',
    3 => 'Master IV',
    4 => 'Master IV',
    5 => 'Master IV',
    6 => 'Master IV',
    7 => 'Master IV',
    8 => 'Master IV',
    9 => 'Master IV',
  ),
  'scripts' => 
  array (
    'raid_chars' => 
    array (
      'name' => 'Characterverwaltung',
      'script' => 'baum.chars.script',
    ),
    'raidevents' => 
    array (
      'name' => 'Eventliste',
      'script' => 'baum.events.script',
    ),
    'php' => 
    array (
      'name' => 'PHP-Panel',
      'script' => 'iv.php.script',
      'editor' => 'iv.source.editor',
    ),
    'html' => 
    array (
      'name' => 'HTML-Panel',
      'script' => 'iv.html.script',
      'editor' => 'iv.source.editor',
    ),
    'menu' => 
    array (
      'name' => 'Menü-Panel',
      'script' => 'iv.menu.script',
    ),
    'login' => 
    array (
      'name' => 'Login-Panel',
      'script' => 'iv.login.script',
    ),
    'registration' => 
    array (
      'name' => 'Registration-Panel',
      'script' => 'iv.registration.script',
      'editor' => 'iv.registration.editor',
    ),
    'avatar' => 
    array (
      'name' => 'Avatar Upload',
      'script' => 'iv.avatar.script',
      'editor' => 'iv.avatar.editor',
    ),
    'changepw' => 
    array (
      'name' => 'Passwort ändern',
      'script' => 'iv.changepw.script',
    ),
  ),
  'moduls' => 
  array (
    0 => 
    array (
      'file' => 'baum.events',
      'icon' => 'index.png',
      'name' => 'Raid-Verwaltung',
    ),
    1 => 
    array (
      'file' => 'iv.content',
      'icon' => 'index.png',
      'name' => 'Content Manager',
    ),
    2 => 
    array (
      'file' => 'iv.options',
      'icon' => 'wrench.png',
      'name' => 'Optionen',
    ),
    3 => 
    array (
      'file' => 'iv.packages',
      'icon' => 'package.png',
      'name' => 'Paketverwaltung',
    ),
    4 => 
    array (
      'file' => 'iv.user',
      'icon' => 'users.png',
      'name' => 'Userverwaltung',
    ),
    5 => 
    array (
      'file' => 'iv.groups',
      'icon' => 'lock.png',
      'name' => 'Rechteverwaltung',
    ),
    6 => 
    array (
      'file' => 'iv.changepw',
      'icon' => 'key.png',
      'name' => 'Passwort ändern',
    ),
    7 => 
    array (
      'file' => 'iv.profil',
      'icon' => 'user_information.png',
      'name' => 'Profil',
    ),
    8 => 
    array (
      'file' => 'iv.profilmanager',
      'icon' => 'user_monitor.png',
      'name' => 'Profilverwaltung',
    ),
  ),
  'panelstatus' => 
  array (
    0 => 'Aktiv',
    1 => 'Inaktiv',
    2 => 'Unregistered only',
    3 => 'Registered Only',
    4 => 'Keine Vererbung',
    5 => 'Admin Only',
  ),
  'layerstatus' => 
  array (
    0 => 'Aktiv',
    1 => 'Inaktiv',
    2 => 'Unregistered only',
    3 => 'Registered Only',
    4 => 'Admin Only',
  ),
  'php_editor' => 
  array (
    'none' => 'None',
    'codemirror' => 'CodeMirror',
  ),
  'html_editor' => 
  array (
    'none' => 'None',
    'codemirror' => 'CodeMirror',
  ),
  'directories' => 
  array (
    'style/default/layer/' => 777,
    'style/default/panel/' => 777,
    'cache/' => 777,
    'upload/' => 777,
  ),
  'rights' => 
  array (
    'script' => 
    array (
      'provider' => 'rights_scripts',
      'caption' => 'Scripts',
      'always' => 
      array (
      ),
      'arguments' => 
      array (
      ),
    ),
    'modul' => 
    array (
      'provider' => 
      array (
        0 => 'rights_modul',
        1 => 'rights_modul',
      ),
      'caption' => 
      array (
        0 => 'Module',
        1 => 'Module',
      ),
      'always' => 
      array (
        0 => 'iv.nav',
      ),
      'arguments' => 
      array (
        'iv.user' => 
        array (
          'profil' => 'Profil bearbeiten',
          'password' => 'Passwörter ändern',
          'rights' => 'Rechte vergeben',
          'edit' => 'User bearbeiten',
        ),
        'iv.profilmanager' => 
        array (
          'delete' => 'Feld löschen',
          'options' => 'Optionen Konfigurieren',
        ),
      ),
    ),
  ),
  'useroptions' => 
  array (
    'system' => 
    array (
      'caption' => 'Systemeinstellungen',
      'items' => 
      array (
        'html_editor' => 
        array (
          'type' => 'enum',
          'caption' => 'Html-Editor',
          'options' => 'html_editor',
        ),
        'php_editor' => 
        array (
          'type' => 'enum',
          'caption' => 'PHP-Editor',
          'options' => 'php_editor',
        ),
      ),
    ),
  ),
  'options' => 
  array (
    'system' => 
    array (
      'caption' => 
      array (
        0 => 'Systemeinstellungen',
        1 => 'Systemeinstellungen',
      ),
      'items' => 
      array (
        'html_editor' => 
        array (
          'type' => 'enum',
          'caption' => 'Html-Editor',
          'options' => 'html_editor',
          'value' => 'none',
        ),
        'php_editor' => 
        array (
          'type' => 'enum',
          'caption' => 'PHP-Editor',
          'options' => 'php_editor',
          'value' => 'none',
        ),
        'theme' => 
        array (
          'type' => 'glob',
          'pattern' => 'theme/*',
          'caption' => 'Admin-Theme',
          'value' => 'bootstrap',
        ),
      ),
    ),
    'page' => 
    array (
      'caption' => 'Seiteneinstellungen',
      'items' => 
      array (
        'title' => 
        array (
          'type' => 'text',
          'caption' => 'Seitentitel',
          'value' => 'IV CMS Page',
        ),
        'name' => 
        array (
          'type' => 'text',
          'caption' => 'Name der Seite',
          'value' => 'IV Content Management System',
        ),
        'url' => 
        array (
          'type' => 'text',
          'caption' => 'Url der Seite',
          'value' => 'http://iv-cms.de',
        ),
        'style' => 
        array (
          'type' => 'glob',
          'pattern' => 'style/*',
          'caption' => 'Style',
          'value' => 'default',
        ),
        'startpage' => 
        array (
          'type' => 'hidden',
          'caption' => 'Startseite',
          'value' => 1,
        ),
        'force_rewrite' => 
        array (
          'type' => 'checkbox',
          'caption' => 'Force URL Rewrite',
          'value' => 0,
        ),
      ),
    ),
  ),
  'js' => 
  array (
    0 => 'assets/js/jquery.min.js',
    1 => 'assets/js/jquery-ui.min.js',
    2 => 'assets/js/bootstrap.min.js',
    3 => 'assets/js/system.js',
  ),
  'css' => 
  array (
    0 => 'assets/css/jquery-ui.min.css',
    1 => 'assets/css/bootstrap.min.css',
    2 => 'assets/css/bootstrap-responsive.min.css',
    3 => 'assets/css/system.css',
  ),
  'enum' => 
  array (
    'input_type' => 
    array (
      0 => 'text',
      1 => 'select',
      2 => 'date',
      3 => 'textarea',
      4 => 'checkbox',
    ),
    'gender' => 
    array (
      0 => 'Männlich',
      1 => 'Weiblich',
    ),
  ),
  'template' => 
  array (
    'filter' => 
    array (
      0 => 
      array (
        'name' => 'dateformat',
        'callback' => 
        array (
          0 => 'template_filter_date',
          1 => 'format',
        ),
      ),
      1 => 
      array (
        'name' => 'datefancy',
        'callback' => 
        array (
          0 => 'template_filter_date',
          1 => 'fancy',
        ),
      ),
      2 => 
      array (
        'name' => 'markdown',
        'callback' => 
        array (
          0 => 'template_filter_markdown',
          1 => 'transform',
        ),
      ),
      3 => 
      array (
        'name' => 'bbcode',
        'callback' => 
        array (
          0 => 'template_filter_bbcode',
          1 => 'bbcode2html',
        ),
      ),
    ),
  ),
);