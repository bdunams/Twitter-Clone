<?php

    include("functions.php");

    if($_GET['action'] == 'loginSignup'){//if GET 'action' loginSignup exists, validate email and password
        
        $error = "";
        
        if(!$_POST['email']){
            $error = 'An email address is required';
        }
        else if(!$_POST['password']){
            $error = 'A password is required';
        }
        else if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false){
            $error = "Please enter a valid email address";
        }
        
        if($error != ""){//if error string isnt empty, display error
            echo $error;
            exit();
        }
        
        
        if($_POST['loginActive'] == "0"){//When signing up, searches db to see if email already exists
            
            $query = "SELECT * FROM `users` WHERE email = '".mysqli_real_escape_string($link, $_POST['email'])."' LIMIT 1";
            $result = mysqli_query($link, $query);
            if(mysqli_num_rows($result) > 0){//searches db to see if email already exists
                $error = "That email address is already taken.";
            }
            else{//else create new user
                
                $query = "INSERT INTO `users` (`email`,`password`) VALUES ('".mysqli_real_escape_string($link, $_POST['email'])."','".mysqli_real_escape_string($link, $_POST['password'])."')";
                
                if(mysqli_query($link, $query)){//if query successful, user info has been added to the database and hash password
                    
                    $_SESSION['id'] = mysqli_insert_id($link);
                    
                    $query = "UPDATE `users` SET password = '".md5(md5($_SESSION['id']).$_POST['password'])."' WHERE id = ".$_SESSION['id']." LIMIT 1";
                    mysqli_query($link, $query);
                    
                    echo 1;
                    
                }
                else{//else user info could not be added
                    $error = "Couldn't create user - please try again later";
                }
            }
            
        }
        else{//Log in user, searches db for existing email
            
            $query = "SELECT * FROM `users` WHERE email = '".mysqli_real_escape_string($link, $_POST['email'])."' LIMIT 1";
            $result = mysqli_query($link, $query);
            $row = mysqli_fetch_assoc($result);
                
                if($row['password'] == md5(md5($row['id']).$_POST['password'])){//compare saved with entered password
                    echo 1;
                    $_SESSION['id'] = $row['id'];
                }
                else{
                    $error = "Could not find that username/password combination. Please try again";
                }
            }
            
        
        if($error != ""){//if error string isnt empty, display error
            echo $error;
            exit();
        }
    }

    if ($_GET['action'] == 'toggleFollow'){//if GET 'action' toggleFollow exists, follow or unfollow user
        
        $query = "SELECT * FROM isFollowing WHERE follower = '".mysqli_real_escape_string($link, $_SESSION['id'])."' AND isFollowing = '".mysqli_real_escape_string($link, $_POST['userId'])."' LIMIT 1";
        $result = mysqli_query($link, $query);
        if(mysqli_num_rows($result) > 0){//if true, user is already following and will now unfollow 
            
            $row = mysqli_fetch_assoc($result);//id of entry
            mysqli_query($link, "DELETE FROM isFollowing WHERE id = '".mysqli_real_escape_string($link, $row['id'])."' LIMIT 1");
            
            echo "1";
        }
        else{//else user will begin following
            
            mysqli_query($link, "INSERT INTO isFollowing (follower, isFollowing) VALUES ('".mysqli_real_escape_string($link, $_SESSION['id'])."','".mysqli_real_escape_string($link, $_POST['userId'])."')");
            
            echo "2";
        }
    }

    if ($_GET['action'] == 'postTweet'){//if GET 'action' postTweet exists, process user tweet to post
        
        if(!$_POST['tweetContent']){//if there isnt any content
            
            echo "Your tweet is empty";
        }
        else if (strlen($_POST['tweetContent']) > 140){
            
            echo "Your tweet is too long";
        }
        else{//else post tweet to database
            mysqli_query($link, "INSERT INTO `tweets` (`tweet`, `userid`, `datetime`) VALUES ('".mysqli_real_escape_string($link, $_POST['tweetContent'])."', ".mysqli_real_escape_string($link, $_SESSION['id']).", NOW())");
            
            echo "1";
        }
    }
?>