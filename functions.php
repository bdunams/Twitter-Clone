<?php

    session_start();

    $link = mysqli_connect("shareddb1b.hosting.stackcp.net","twitter-35b289","SU0nK2zl+eRc","twitter-35b289");

    if(mysqli_connect_errno()){
        
        print_r(mysqli_connect_error());
        exit();
    }
    
    if((array_key_exists('function', $_GET)) AND ($_GET['function'] == 'logout')){//if logout exists in GET array, end session
        session_unset();
    }

    function time_since($since) {//post time formatting
        $chunks = array(
            array(60 * 60 * 24 * 365 , 'year'),
            array(60 * 60 * 24 * 30 , 'month'),
            array(60 * 60 * 24 * 7, 'week'),
            array(60 * 60 * 24 , 'day'),
            array(60 * 60 , 'hour'),
            array(60 , 'minute'),
            array(1 , 'second')
        );

        for ($i = 0, $j = count($chunks); $i < $j; $i++) {
            $seconds = $chunks[$i][0];
            $name = $chunks[$i][1];
            if (($count = floor($since / $seconds)) != 0) {
                break;
            }
        }

        $print = ($count == 1) ? '1 '.$name : "$count {$name}s";
        return $print;
    }


    function displayTweets($type){
        global $link;
        
        if($type == 'public'){
            $whereClause = "";
        }
        else if($type == 'isFollowing'){//finds all the users you follow and query their tweets to display
            
            if((array_key_exists('id',$_SESSION)) AND $_SESSION['id']){//to avoid error check if there is a session id
                $query = "SELECT * FROM isFollowing WHERE follower = ".mysqli_real_escape_string($link, $_SESSION['id']);
                $result = mysqli_query($link, $query);
                if(mysqli_num_rows($result) != 0){//if you follow more than 0 users, query all
                    $whereClause = "";
                    while ($row = mysqli_fetch_assoc($result)){//loop through results
                        if($whereClause == ""){
                            $whereClause = "WHERE ";
                        }
                        else{
                            $whereClause .= " OR ";
                        }
                        $whereClause .= "userid = ".$row['isFollowing'];
                    }
                }
                else{//else return zero tweets
                    $whereClause = "WHERE userid = 0";
                    echo "<p>Follow some users to see their tweets on your timeline!</p>";
                }
            }
            else{//else return zero tweets
                $whereClause = "WHERE userid = 0";
                echo "<p>Log in to view your followers tweets</p>";
            }
        }
        else if($type == 'yourtweets'){//finds all your tweets
            if((array_key_exists('id',$_SESSION)) AND $_SESSION['id']){//to avoid error check if there is a session id
                $whereClause = "WHERE userid = ".mysqli_real_escape_string($link, $_SESSION['id']);
            }
            else{//else return zero tweets
                $whereClause = "WHERE userid = 0";
                echo "<p>Log in to view your tweets</p>";
            } 
        }
        else if($type == 'search'){//finds results from search bar is db
            echo "<p>Showing search results for '".mysqli_real_escape_string($link, $_GET['q'])."':</p>";
            $whereClause = "WHERE tweet LIKE '%".mysqli_real_escape_string($link, $_GET['q'])."%'";
        }
        else if(is_numeric($type)){//finds tweets for public profile
            
            $userQuery = "SELECT * FROM users WHERE id = ".mysqli_real_escape_string($link, $type)." LIMIT 1";
            $userQueryResult = mysqli_query($link, $userQuery);
            $user = mysqli_fetch_assoc($userQueryResult);
            
            echo "<h2>".mysqli_real_escape_string($link, $user['email'])."'s tweets</h2>";
            
            $whereClause = "WHERE userid = ".mysqli_real_escape_string($link, $type);
        }
        
        
        $query = "SELECT * FROM tweets ".$whereClause." ORDER BY `datetime` DESC LIMIT 10";
        $result = mysqli_query($link, $query);
        if(mysqli_num_rows($result) == 0){
            
            echo "<p>There aren't any tweets to display</p>";
        }
        else{
            
            while ($row = mysqli_fetch_assoc($result)){//loop to display the last 10 queried tweets
                
                $userQuery = "SELECT * FROM users WHERE id = ".mysqli_real_escape_string($link, $row['userid'])." LIMIT 1";
                $userQueryResult = mysqli_query($link, $userQuery);
                $user = mysqli_fetch_assoc($userQueryResult);
                
                echo "<div class='tweet'><p><a href='?page=publicprofiles&userid=".$user['id']."'>".$user['email']."</a> <span class='time'>".time_since(time()-strtotime($row['datetime']))." ago </span>:</p>";
                
                echo "<p>".$row['tweet']."</p>";
                
                echo "<p><a class='toggleFollow' href='' data-userId='".$row['userid']."'>";
                
                if((array_key_exists('id', $_SESSION)) AND ($_SESSION['id'] > 0)){//if already follow show unfollow
                    $isFollowingQuery = "SELECT * FROM isFollowing WHERE follower = '".mysqli_real_escape_string($link, $_SESSION['id'])."' AND isFollowing = '".mysqli_real_escape_string($link, $row['userid'])."' LIMIT 1";
                    $isFollowingQueryResult = mysqli_query($link, $isFollowingQuery);
                    if(mysqli_num_rows($isFollowingQueryResult) > 0){
                        echo "Unfollow";
                    }
                    else {
                        echo "Follow";
                    }
                }
                echo "</a></p></div>";
            }
        }
        
    }
    
    function displaySearch(){//for searching tweets
        echo '<form class="form-inline">
                <div class="form-group">
                <input type="hidden" name="page" value="search">
                <input type="text" name="q" class="form-control mb-2 mr-sm-2 mb-sm-0" id="search" placeholder="Search">

              <button type="submit" class="btn btn-primary">Search Tweets</button>
              </div>
            </form>';
    }
    
    function displayTweetBox(){//for posting tweets
        if((array_key_exists('id', $_SESSION)) AND ($_SESSION['id'] > 0)){
            echo '<div id="tweetSuccess" class="alert alert-success">Your tweet was posted successfully.</div>
            <div id="tweetFail" class="alert alert-danger"></div>
            <div class="form">
                <div class="form-group">
              <textarea type="text" class="form-control mb-2 mr-sm-2 mb-sm-0" id="tweetContent"></textarea>
              </div>
              <button id="postTweetButton" class="btn btn-primary">Post Tweets</button>
              
            </div>';
        }
    } 

    function displayUsers(){
        
        global $link;
        
        $query = "SELECT * FROM users LIMIT 10";
        $result = mysqli_query($link, $query);
        
        while ($row = mysqli_fetch_assoc($result)){
            
            echo "<p><a href='?page=publicprofiles&userid=".$row['id']."'>".$row['email']."</a></p>";
        }
        
    }
        
?>