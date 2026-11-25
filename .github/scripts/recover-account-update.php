<?php
/** Actions-only scoped updater; no WordPress bootstrap or database writes. */
if(PHP_SAPI!=='cli')exit(1);
function recovery_fail($message){throw new RuntimeException($message);}
function recovery_hash($data){return hash('sha256',str_replace("\r\n","\n",$data));}
function recovery_paths(){return array(
 'wp-content/plugins/long-khanh-directory/includes/account.php',
 'wp-content/plugins/long-khanh-directory/templates/account.php',
 'wp-content/plugins/long-khanh-directory/assets/account.css',
 'wp-content/plugins/long-khanh-directory/long-khanh-directory.php',
 'wp-content/plugins/long-khanh-directory/includes/forms.php',
 'wp-content/themes/listingpro-longkhanh-child/assets/feedback2.js',
 'wp-content/themes/listingpro-longkhanh-child/includes/presentation.php'
);}
function recovery_path($root,$relative){
 $cursor=$root;
 foreach(explode('/',$relative) as $part){$cursor.='/'.$part;if(is_link($cursor))recovery_fail('Refusing a symlink in the recovery scope.');}
 if(!is_dir(dirname($cursor)))recovery_fail('Recovery destination directory is missing.');
 if(file_exists($cursor)&&!is_file($cursor))recovery_fail('Recovery destination is not a regular file.');
 return $cursor;
}
function recovery_plan($root,$stage,$manifest){
 $rows=$manifest['files']??array();
 if(array_column($rows,'path')!==recovery_paths())recovery_fail('Unexpected recovery allowlist.');
 $plan=array();
 foreach($rows as $index=>$row){
  foreach(array('after_sha256','preserve_sha256') as $field)if(!preg_match('/^[a-f0-9]{64}$/',$row[$field]??''))recovery_fail('Invalid recovery checksum.');
  if(($row['before_sha256']??null)!==null&&!preg_match('/^[a-f0-9]{64}$/',$row['before_sha256']))recovery_fail('Invalid baseline checksum.');
  $payload=$stage.'/files/'.$row['path'];
  if(!is_file($payload)||is_link($payload))recovery_fail('Recovery payload is missing.');
  $data=file_get_contents($payload);
  if(hash('sha256',$data)!==$row['after_sha256'])recovery_fail('Recovery payload integrity failed.');
  if(str_ends_with($row['path'],'.php'))token_get_all($data,TOKEN_PARSE);
  $target=recovery_path($root,$row['path']);
  $current=is_file($target)?file_get_contents($target):null;
  $hash=$current===null?null:recovery_hash($current);
  if(!in_array($hash,array($row['before_sha256'],$row['after_sha256'],$row['preserve_sha256']),true))recovery_fail('Destination differs from reviewed versions: '.$row['path']);
  $plan[]=array('path'=>$row['path'],'raw_before'=>$current===null?null:hash('sha256',$current),
                'write'=>!in_array($hash,array($row['after_sha256'],$row['preserve_sha256']),true));
 }
 return $plan;
}
try{
 $mode=$argv[1]??'';$root=$argv[2]??'';$stage=$argv[3]??'';
 if(!in_array($mode,array('check','apply'),true)||realpath($root)!==$root||realpath($stage)!==$stage||$root===$stage)recovery_fail('Invalid recovery arguments.');
 $manifest=json_decode(file_get_contents($stage.'/manifest.json'),true,512,JSON_THROW_ON_ERROR);
 $plan=recovery_plan($root,$stage,$manifest);
 if($mode==='check'){echo json_encode($plan,JSON_THROW_ON_ERROR);exit(0);}
 $checked=json_decode(file_get_contents($stage.'/plan.json'),true,512,JSON_THROW_ON_ERROR);
 if($plan!==$checked)recovery_fail('Destination changed after preflight; no files written.');
 $temps=array();$originals=array();$written=array();
 try{
  foreach($plan as $index=>$entry){
   if(!$entry['write'])continue;
   $target=recovery_path($root,$entry['path']);
   $originals[$index]=is_file($target)?file_get_contents($target):null;
   $temp=tempnam(dirname($target),'.lk-account-');
   if($temp===false)recovery_fail('Cannot prepare a recovery file.');
   $temps[$index]=$temp;
   $payload=file_get_contents($stage.'/files/'.$entry['path']);
   if(file_put_contents($temp,$payload)!==strlen($payload)||!chmod($temp,0644))recovery_fail('Cannot prepare recovery bytes or permissions.');
  }
  // Recheck all seven paths immediately before publishing any prepared file.
  if(recovery_plan($root,$stage,$manifest)!==$checked)recovery_fail('Destination changed during preparation; no files written.');
  foreach($temps as $index=>$temp){
   $target=recovery_path($root,$plan[$index]['path']);
   $now=is_file($target)?hash_file('sha256',$target):null;
   if($now!==$plan[$index]['raw_before'])recovery_fail('Destination changed before activation.');
   if(!rename($temp,$target))recovery_fail('Could not activate an account file.');
   $written[]=$index;
  }
  foreach($plan as $index=>$entry){
   $target=recovery_path($root,$entry['path']);$now=is_file($target)?hash_file('sha256',$target):null;
   $expected=$entry['write']?$manifest['files'][$index]['after_sha256']:$entry['raw_before'];
   if($now!==$expected)recovery_fail('Account file changed during activation.');
  }
 }catch(Throwable $error){
  foreach(array_reverse($written) as $index){
   $target=$root.'/'.$plan[$index]['path'];$row=$manifest['files'][$index];
   if(!is_file($target)||hash_file('sha256',$target)!==$row['after_sha256'])recovery_fail('Concurrent edit prevents automatic rollback; use the Actions backup.');
   if($originals[$index]===null){if(!unlink($target))recovery_fail('Cannot roll back a newly added recovery file.');}
   else{$temp=tempnam(dirname($target),'.lk-account-');if($temp===false||file_put_contents($temp,$originals[$index])!==strlen($originals[$index])||!chmod($temp,0644)||!rename($temp,$target))recovery_fail('Cannot restore an account file; use the Actions backup.');}
  }
  throw $error;
 }finally{foreach($temps as $temp)if(is_file($temp))unlink($temp);}
 echo 'Scoped account recovery applied; '.count($written)." files changed.\n";
}catch(Throwable $error){fwrite(STDERR,'Account recovery stopped: '.$error->getMessage()."\n");exit(1);}
