# Setup

Create a file with inc/database.config.php and configure the database:
```
<?php

$db = new mysql_connection( 'localhost', '**sql user**', '**sql pass**', '**db name**');
$db->set_charset('utf8');

```
