<?php
    require 'db.php';
    try{        
        if(isset($_GET['token']) && !empty($_GET['token'])){
            $token=$_GET['token'];
            $sql='SELECT user_email FROM registrations WHERE verification_token = ?';
            $stmt=$conn->prepare($sql);
            $stmt->bind_param("s",$token);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($result);
            $rows=$stmt->num_rows;
            
            if($rows>0){
                
                $email="";
    
                while($stmt->fetch()){
                    $email=$result;
                }
    
                $stmt->free_result();
                
                $sql='DELETE from registrations WHERE user_email = ?';
                $stmt=$conn->prepare($sql);
                $stmt->bind_param("s",$email);
                $stmt->execute();
                $stmt->free_result();
                
                $sql='INSERT INTO users (user_email) VALUES(?)';
                $stmt=$conn->prepare($sql);
                $stmt->bind_param("s",$email);
                $stmt->execute();
                echo "Your Email has been Verified";
                
                $stmt->close();
            }
            else{
                echo "Invalid Token";
            }
        }
    }
    catch(Exception $e){
        echo 'Error: ' . $e->getMessage();
    }
?>

