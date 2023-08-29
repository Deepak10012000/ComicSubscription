<?php
    require 'db.php';

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: Access-Control-Allow-Origin,X-Requested-With,Content-Type,Accept");

    try{
        $json=file_get_contents("php://input");
        $data=json_decode($json);
    
        if($data->email && $data->OTP){
            $email=$data->email;
            $otp=$data->OTP;
            $sql='SELECT id FROM unsubscribers WHERE OTP = ? AND user_email = ?';
            $stmt=$conn->prepare($sql);
            $stmt->bind_param("is",$otp,$email);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($result);
            $rows=$stmt->num_rows;
            
            if($rows>0){
                $stmt->free_result();
                
                $sql = "DELETE FROM users WHERE user_email = ?";
                $stmt=$conn->prepare($sql);
                $stmt->bind_param("s",$email);
                $stmt->execute();
    
                $sql = "DELETE FROM unsubscribers WHERE user_email = ?";
                $stmt=$conn->prepare($sql);
                $stmt->bind_param("s",$email);
                $stmt->execute();
                $stmt->close();    
                echo 'Your Have been unsubscribed for Daily Comics. To Subscribe Again Please Register Again.';
            }
            else{
                echo 'Invalid OTP';
            }   
        }

    }
    catch(Exception $e){
        echo 'Error: ' . $e->getMessage();
    }

    
?>
