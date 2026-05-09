<?php
header('Content-Type: application/json');
if(!empty($_POST['honeypot'])) { echo json_encode(['success'=>false,'error'=>'Spam détecté']); exit; }
$required = ['name','phone','message'];
foreach($required as $f) if(empty($_POST[$f])) { echo json_encode(['success'=>false,'error'=>"Champ manquant: $f"]); exit; }
$phone = preg_replace('/[^0-9+]/','',$_POST['phone']);
if(strlen($phone)<10) { echo json_encode(['success'=>false,'error'=>'Téléphone invalide']); exit; }
$data = ['id'=>'REQ-'.substr(md5(uniqid()),0,6),'name'=>htmlspecialchars($_POST['name']),'phone'=>$phone,'message'=>htmlspecialchars($_POST['message']),'date'=>date('Y-m-d H:i:s')];
$dir = __DIR__.'/../data/requests';
if(!is_dir($dir)) mkdir($dir,0750,true);
file_put_contents($dir.'/'.$data['id'].'.json', json_encode($data,JSON_PRETTY_PRINT));
echo json_encode(['success'=>true,'id'=>$data['id']]);
?>