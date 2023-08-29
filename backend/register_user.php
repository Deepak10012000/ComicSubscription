<?php
    require 'db.php';
    
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: Access-Control-Allow-Origin,X-Requested-With,Content-Type,Accept");

    function sendVerification($email,$token){
        $apikey=getenv("API_KEY");
        $dom=getenv("DOMAIN");
        $link=$dom . "verifyEmail.php/?token=$token";
        $message="<p>To verify your Email Click on the below link<br><a href='$link'>Click Here</a></p>";

        $postdata=array(
            "personalizations"=>array(
                array(
                    "to" => array(
                        array(
                            "email"=>$email,
                        )
                    )
                )
            ),
            "from"=>array(
                "email"=>"comicssender@protonmail.com",
                "name"=>"XKCD Comics Sender",
            ),
            "subject"=>"EMAIL VERIFICATION",
            "content"=>array(
                array(
                    "type"=> "text/html",
                    "value"=> $message,
                )
            ),
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
    }

    try{
        $json=file_get_contents("php://input");
        $data=json_decode($json);
    
        if($data->email){
            
            $email=mysqli_real_escape_string($conn,$data->email);
            
            $sql='SELECT id FROM users WHERE user_email = ?';
            $stmt=$conn->prepare($sql);
            $stmt->bind_param("s",$email);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($result);
            $rows=$stmt->num_rows;
            
            if($rows==0){

                $stmt->free_result();
                $sql='SELECT id FROM registrations WHERE user_email = ?';
                $stmt=$conn->prepare($sql);
                $stmt->bind_param("s",$email);
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($result);
                $rows=$stmt->num_rows;

                if($rows==0){

                    $stmt->free_result();
                    $token=md5($email.time());
                    sendVerification($email,$token);

                    $sql='INSERT INTO registrations (user_email,verification_token) VALUES(?, ?)';
                    $stmt=$conn->prepare($sql);
                    $stmt->bind_param("ss",$email,$token);
                    $stmt->execute();
                    echo "Registered Successfully!!<br>Verification Mail has been Sent to you.<br>Check your Inbox and Spam both.To Receive the Verification mail again register again.";

                }
                else{

                    $stmt->free_result();
                    $token=md5($email.time());
                    sendVerification($email,$token);
                    $sql='UPDATE registrations SET verification_token = ? WHERE user_email = ?';
                    $stmt=$conn->prepare($sql);
                    $stmt->bind_param("ss",$token,$email);
                    $stmt->execute();
                    $stmt->close();
                    echo "Verification Mail has been sent again.<br>This Email has already been registered. Please verify your email by checking verification mail send to you.";
                }
            }
            else{
                echo "This Email has already been subscribed";
            }
        }
        else{
            echo "Please Enter All Field Values";
        }
    }
    catch(Exception $e){
        echo 'Error: ' . $e->getMessage();
    }
?>