<?php
header('Content-Type: application/json');
session_start();
$ip = $_SERVER['REMOTE_ADDR'];
if((time()-($_SESSION['last_booking']??0))<60){echo json_encode(['success'=>false,'error'=>'Attendez 1 min entre 2 réservations']);exit;}
$_SESSION['last_booking']=time();
if(!empty($_POST['honeypot'])){echo json_encode(['success'=>false,'error'=>'Requête invalide']);exit;}
$req=['service','date','time','name','phone','address','consent'];
foreach($req as $f)if(empty($_POST[$f])){echo json_encode(['success'=>false,'error'=>"Champ $f requis"]);exit;}
$b=['id'=>'RDV-'.strtoupper(substr(md5(uniqid()),0,8)),'service'=>htmlspecialchars($_POST['service']),'date'=>htmlspecialchars($_POST['date']),'time'=>htmlspecialchars($_POST['time']),'name'=>htmlspecialchars($_POST['name']),'phone'=>preg_replace('/[^0-9+]/','',$_POST['phone']),'address'=>htmlspecialchars($_POST['address']),'consent'=>true,'created'=>date('c')];
$dir=__DIR__.'/../data/bookings';if(!is_dir($dir))mkdir($dir,0750,true);
file_put_contents($dir.'/'.$b['id'].'.json',json_encode($b,JSON_PRETTY_PRINT));
file_put_contents($dir.'/consent.log',date('c')." | IP:$ip | ID:{$b['id']}
",FILE_APPEND);
echo json_encode(['success'=>true,'id'=>$b['id']]);
?>