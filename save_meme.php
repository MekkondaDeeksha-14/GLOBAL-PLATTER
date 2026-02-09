<?php
session_start();
include 'db.php';
$user_id = $_SESSION['user_id'] ?? 1;

if(!isset($_POST['image'])){http_response_code(400); echo "No image data"; exit;}

$data = $_POST['image'];
$data = str_replace('data:image/png;base64,','',$data);
$data = str_replace(' ','+',$data);
$imageData = base64_decode($data);

$folder = "saved_memes/";
if(!is_dir($folder)){mkdir($folder,0777,true);}
$filename = $folder."meme_".time()."_".rand(1000,9999).".png";

if(file_put_contents($filename,$imageData)){
    $stmt=$conn->prepare("INSERT INTO memes (user_id, meme_path) VALUES (?,?)");
    $stmt->bind_param("is",$user_id,$filename);
    $stmt->execute();
    $stmt->close();
    echo "success";
}else{
    http_response_code(500); echo "File save failed";
}
