<?php
  require("opendb.php");

  $query = mysql_query("SELECT * FROM users ORDER BY id");
  while($row = mysql_fetch_array($query)){ 
     $username = $row[1];
     echo $username;
     $repo_url = "https://api.github.com/users/" . $username . "/repos";
     $repos = json_decode(file_get_contents($repo_url));
     $messages = array();
     foreach($repos as $key => $repo) {
       $name = $repo->name;
       $commit_url = "https://api.github.com/repos/" . $username . "/" . $name . "/commits";
       $commits = json_decode(file_get_contents($commit_url));
       foreach($commits as $key => $commit) {
         $message = $commit->commit->message;
         $profanity_url = "http://www.wdyl.com/profanity?q=" . urlencode($message);
         $profanity = json_decode(file_get_contents($profanity_url));
           
           if($profanity->response == 'true') {
           $login = $commit->committer->login;
           $avatar = $commit->committer->avatar_url;
           $userurl = $commit->committer->url;
           $date = $commit->commit->committer->date;
           $commiturl = $commit->commit->url;
           $message = filter_that_shit($message);
           $insert = "INSERT INTO commits VALUES ('', '$login', '$message', '$avatar', '$commiturl', '$userurl', '$date')";
           $insert = mysql_query($insert);
           echo mysql_error($connection);
         } 
       }
     }
   }
?>