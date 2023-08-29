<?php

    require 'db.php';

    function getRandomcomicId(){
        $ch=curl_init();
        $url='https://c.xkcd.com/random/comic/';
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_HEADER, true); 
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); 
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $http_data = curl_exec($ch); 
        $curl_info = curl_getinfo($ch);
        $headers = substr($http_data, 0, $curl_info["header_size"]);
        $matches=strpos($headers,'location:');
        $matstr=substr($headers,$matches+10);
        $mixedurl=explode(" ",$matstr)[0];
        $finurl=substr($mixedurl,0,strlen($mixedurl)-6);
        curl_close($ch);
        $comicid=substr($finurl,16);
        $comicid=rtrim($comicid,'/');
        return $comicid;
    }

    function getComic($comicid){
        $url="https://xkcd.com/".$comicid."/info.0.json";
        $ch=curl_init($url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $data = curl_exec($ch);
        $data=json_decode($data,true);
        return $data;
    }

    function generatecomicHTML($comicdata){
        $title=$comicdata['title'];
        $htmlstr="<center><h1>$title</h1>";
        $imgurl=$comicdata['img'];
        
        $altstr=htmlspecialchars($comicdata['alt']);
        $htmlstr.='<img src="cid:imageinline" alt ="'.$altstr.'" />';
        
        return [$htmlstr,$imgurl];
    }

    function sendcomicmail($to,$imgurl,$message){
        $apikey=getenv("API_KEY");
        $dom=getenv("FRONT_DOMAIN");
        $imgdata=file_get_contents($imgurl);

        $encdata=base64_encode($imgdata);
        
        $unsublink=$dom . "unsubscribe/?email=$to";
        $message.='<h3>You can Unsubscribe by clicking on the below link <br><a href=\''.$unsublink.'\'>Unsubscribe</a></h3>';
        
        $postdata=array(
            "personalizations"=>array(
                array(
                    "to" => array(
                        array(
                            "email"=>$to,
                        )
                    )
                )
            ),
            "from"=>array(
                "email"=>"comicssender@protonmail.com",
                "name"=>"XKCD Comics Sender",
            ),
            "subject"=>"XKCD COMIC",
            "content"=>array(
                array(
                    "type"=> "text/html",
                    "value"=> $message,
                )
            ),
            "attachments"=>array(
                array(
                    "content" => $encdata,
                    "type" => "image/jpeg",
                    "filename" => "imageinline",
                    "disposition" => "inline",
                    "content_id" => "imageinline",
                ),
                array(
                    "content" => $encdata,
                    "type" => "image/jpeg",
                    "filename" => "comic-image",
                    "disposition" => "attachment",
                    "content_id" => "imageattachment",
                ),
            )
        );

        $ch = curl_init();
        
        curl_setopt($ch,CURLOPT_URL,"https://api.sendgrid.com/v3/mail/send");
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($postdata));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: ' . 'Bearer ' . $apikey)
        );

        curl_exec($ch);
        curl_close($ch);
        return 'Done';
    }

    if(isset($_GET['pass']) && !empty($_GET['pass'])){
        
        $cronpass=getenv('CRON_PASSWORD');
        if($_GET['pass']==$cronpass){

            try{
                $sql='SELECT user_email FROM users';
                $stmt=$conn->prepare($sql);
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($result);
                $rows=$stmt->num_rows;
                
                if($rows>0){
        
                    while($stmt->fetch()){
                        $email=$result;
                        $id=getRandomcomicId();
                        $comicdata=getComic($id);
                        [$htmlstr,$imgurl]=generatecomicHTML($comicdata);
                        sendcomicmail($email,$imgurl,$htmlstr);
                    }

                }

                echo 'Job Done';
            }
            
            catch(Exception $e){
                echo 'Error: ' . $e->getMessage();
            }
        }
    }
?>