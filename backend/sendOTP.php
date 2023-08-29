<?php
    require 'db.php';

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: Access-Control-Allow-Origin,X-Requested-With,Content-Type,Accept");

    function sendOTPmail($to,$otp){
        
        $apikey=getenv("API_KEY");
        
        $message="<p>YOUR OTP<br><b>$otp</b><br>If you dont recognize it you may ignore it.</p>";

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
            "subject"=>"UNSUBSCRIBE OTP",
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
            $email=$data->email;
            $sql = "SELECT id FROM users WHERE user_email = ?";
            $stmt=$conn->prepare($sql);
            $stmt->bind_param("s",$email);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($result);
            
            $rows=$stmt->num_rows;

            if($rows>0){

                $otp = rand(100000,999999);

                $stmt->free_result();
                $sql = "SELECT id FROM unsubscribers WHERE user_email = ?";
                $stmt=$conn->prepare($sql);
                $stmt->bind_param("s",$email);
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($result);

                $rows=$stmt->num_rows;
                
                if($rows>0){
                    $stmt->free_result();
                    $sql = "UPDATE unsubscribers SET OTP = ? WHERE user_email = ?";
                    $stmt=$conn->prepare($sql);
                    $stmt->bind_param("is",$otp,$email);
                    $stmt->execute();
                    sendOTPmail($email,$otp);
                }
                else{
                    $stmt->free_result();
                    $sql = "INSERT INTO unsubscribers(user_email,OTP) VALUES (?,?)";
                    $stmt=$conn->prepare($sql);
                    $stmt->bind_param("si",$email,$otp);
                    $stmt->execute();
                    sendOTPmail($email,$otp);
                }

                echo 'OTP has been sent to your email. Check Spam and Inbox';
            }
            else{
                echo 'Please Register Yourself First!!';
            }
            
            $stmt->close();
        }
    }
    catch(Exception $e){
        echo 'Error: ' . $e->getMessage();
    }


?>