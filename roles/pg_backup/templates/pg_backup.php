<?php

function logError ($file, $type, $msg) {
  echo date("M d H:i:s  ") . "[$type] $file: $msg" . PHP_EOL;
  if ($type == "fatal") exit(1);
}

$dt = date("Y-m-d-H:i");
$fileName = "backup_{{ moodle_db_name }}_$dt";

$cmd = "pg_dump -h {{ moodle_db_server }} -U {{ db_users[0] }} {{ moodle_db_name }} > /mnt/data/backups/$fileName";


$pgdump = shell_exec($cmd);
echo "The pgdump return is " . $pgdump . PHP_EOL;


