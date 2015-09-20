<?php

// This script backs up any EBS volumes attached to the EC2 instance on which it is run that
// have a backup=true tag. The script identifies the volumes, snapshots them, and takes care
// of any snapshot management required.

function logError ($file, $type, $msg) {
  echo date("M d H:i:s  ") . "[$type] $file: $msg" . PHP_EOL;
  if ($type == "fatal") exit(1);
}

function doBackup ($tagArray) {
  $doit = false;
  foreach ($tagArray as $tag) {
    if ($tag['Key'] == 'backup' && $tag['Value'] == 'true') $doit = true;
    if ($doit) break;
  }
  return $doit;
}

function getTag ($obj, $key) {
  $value = null;
  if (isset($obj) && isset($obj['Tags'])) {
    foreach($obj['Tags'] as $tag) {
      if ($tag['Key'] == $key) {
        $value = $tag['Value'];
      }
    }
  }
  return $value;
}

// Standard AWS IP for querying from an EC2 instance
$url = 'http://169.254.169.254/latest/meta-data/instance-id';

// Open the Curl session
// Don't return HTTP headers. Do return the contents of the call
$session = curl_init($url);
curl_setopt($session, CURLOPT_HEADER, false);
curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

// Make the call
$instanceId = curl_exec($session);

// Now use the AWS CLI to get the instance metadata
$meta = json_decode(shell_exec ("aws ec2 describe-instances --instance-ids $instanceId"), true);

if (! isset($meta)) logErr ('ebs_backup', "fatal", "No metadata returned for instance id $instanceId");

$blockDevices = $meta['Reservations'][0]['Instances'][0]['BlockDeviceMappings'];
if (! isset($blockDevices)) {
  logError ('ebs_backup', 'fatal', "Unable to access block devices");
}
// Get a list of all the snapshots
$snaps = json_decode(shell_exec("aws ec2 describe-snapshots --owner-ids {{ aws_owner_id}}"), true);
echo "Got the snaps: " . PHP_EOL . json_encode($snaps) . PHP_EOL . PHP_EOL;
$snaps = $snaps['Snapshots'];


foreach ($blockDevices as $device) {
  $volId = $device['Ebs']['VolumeId'];
  $devMeta = json_decode(shell_exec("aws ec2 describe-volumes --volume-ids $volId"), true);
  $backupTag = getTag($devMeta['Volumes'][0], 'backup');
  if (isset($backupTag) && $backupTag == 'true') {
    // First delete any snapshot with the name we're going to use
    $snapName = 'Monday'; // We need to generate the name based on backup scheme.
    foreach($snaps as $snap) {
      $nm = getTag($snap, 'Name');
      echo "Check snap " . $snap['SnapshotId'] . " with Nametag $nm". PHP_EOL;
      if ($nm == $snapName) {
        $sId = $snap['SnapshotId'];
        $ret = json_decode(shell_exec("aws ec2 delete-snapshot --snapshot-id $sId"),true);
        echo "  DELETE Snap " . $snap['SnapshotId'] . PHP_EOL;
        echo " Return value is " . json_encode($ret) . PHP_EOL;
      }
    }

    echo "Doing backup on device $volId" . PHP_EOL;
    $snapshot = json_decode(
      shell_exec('aws ec2 create-snapshot --volume-id ' . $volId . ' --description "This is my data volume snapshot."'),
      true);
    if (!isset($snapshot)) logErr('ebs_backup', 'fatal', 'Snapshot failed');
    else {
      $snapshotId = $snapshot['SnapshotId'];
      $tagCmd = json_decode(shell_exec("aws ec2 create-tags --resources $snapshotId --tags Key=Name,Value=$snapName"), true);
      echo ("And here's the tagCmd data:" . PHP_EOL . json_encode($tagCmd) . PHP_EOL);
    }
  }
  else {
    echo "No backup on device $volId" . PHP_EOL;

  }
}

// NOW - Pull out the volumes and for each one that has backup = true, do the backup.
// aws ec2 describe-volumes --volume-ids vol-e47b3f09

echo "The instance ID is $instanceId" . PHP_EOL;
//echo "The instance metadata is " . json_encode($blockDevices) . PHP_EOL;
curl_close($session);


