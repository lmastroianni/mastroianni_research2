<?php


function createuser($fname, $username, $email){
    
    $pdo = Database::getInstance()->getConnection();

     // check user existance
     $check_email_query = 'SELECT COUNT(user_name) AS num FROM tbl_user WHERE user_name = :username'; 
     $user_set = $pdo->prepare($check_email_query);
     $user_set->execute(
         array(
             ':username'=>$username
         )
     );
 ///// ASK PAN //////
     $row = $user_set->fetch(PDO::FETCH_ASSOC);

     if($row['num'] > 0){
        $message = 'username is already registered';
    }else{
        
        $password = md5(rand(0,1000)); 

        
        $mail = new PHPMailer\PHPMailer\PHPMailer();

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPSecure='ssl';
        $mail->Port = 587;
        $mail->SMTPAuth=true;

        $mail->Username='lmastroianni98@gmail.com';
        $mail->Password='Luckydouble7'; 

        $mail->addAddress($email);
        $mail->setFrom('lmastroianni98@gmail.com');
        

        $mail->isHTML(true);
        $mail->Subject='Created User!'; 
        $mail->Body='
        Thanks for signing up!<br><br>
        Your account has been Created!
        <br><br><br>
        ------------------------<br>
        Here is your login info!<br>
        Email: '.$username.'<br>
        Password: '.$password.'<br><br>
        Login at http://localhost:8888/movies_cms/admin/admin_login.php <br>
        
        ';

        if(!$mail->send()){
            $message= $mail->ErrorInfo;
            return 'user creation did not got through';
        }else{
            //creating user sql query from form details
            $create_user_query = "INSERT INTO tbl_user (user_id, f_name, user_name, user_email, user_pass, user_ip) VALUES (NULL, :fname, :username, :email, :password, 'no');";

            $user_signup = $pdo->prepare($create_user_query);
            $user_signup->execute(
                array(
                    ':fname'=>$fname,
                    ':username'=>$username,
                    ':email'=>$email,
                    ':password'=>$password
                )
            );
            
            redirect_to('index.php');
            $message = 'created user';
        }
    }
}