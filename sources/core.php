<?php

    error_reporting(0);

    function clearRequest ($request)
    {
        if (isset ($_REQUEST [$request]))
        {
            $var = mysql_real_escape_string (htmlentities ($_REQUEST [$request]));
            return $var;
        }
    }

    //login ->

    function login($username,$password){

        $password = md5(sha1($password));
        $query    = "SELECT * FROM users WHERE username = '{$username}' AND password = '{$password}'";
        $res      = mysql_query($query) or die ("SQL error:".mysql_error());
        $rows     = mysql_num_rows($res);
        $ris      = mysql_fetch_array($res);
        $level    = $ris['level'];

        if($rows != 1)
        {
            print "Wrong username or password\n";
        }

        else
        {
            if($level != 'admin')
            {
                   print "Login lates with success";
                   setcookie('biscotto',$password,time()+2000,'/');
            }
            else
            {
                   print "Login lates with success,hi admin";
                   setcookie('biscotto',$password,time()+2000,'/');
                   header("Location: index.php");
            }
        }
    }

    //is_admin ->

    function is_admin()
    {

        $biscotto = $_COOKIE['biscotto'];
        $query    = "SELECT * FROM users WHERE password = '{$biscotto}' AND level = 'admin'";
        $res      = mysql_query($query) or die ("SQL error:".mysql_error());
        $rows     = mysql_num_rows($res);

        if($rows != 1)
        {
            return false;
        }

        else
        {
            return true;
        }
    }

    //register ->

    function register($username,$password,$email,$level){
        $password = md5(sha1($password));
        $control  = "SELECT * FROM users WHERE password = '{$password}'";
        $res      = mysql_query($control) or die ("SQL error:".mysql_error());
        $rows     = mysql_num_rows($res);

        if($rows != 1)
        {
            $query    = "INSERT INTO users (username,password,email,level) VALUES('$username','$password','$email','$level');";

            $res      = mysql_query($query) or die ("SQL error:".mysql_error());

            if ($res)
            {
                print "User registration = TRUE :), yep";
            }

            else
            {
                print "Trouble registering";
            }
        }

        else
        {
            print "Username or password that is' present";
        }
    }

    //is_login ->

    function is_logged ()
    {
        if (isset ($_COOKIE ['biscotto']))
        {
            return true;
        }

        else
        {
            return false;
        }
    }

    //write_menu ->

    function write_menu()
    {
        $query = "SELECT * FROM pages";
        $res   = mysql_query($query) or die ("SQL error:".mysql_error());


        while($ris = mysql_fetch_array($res,MYSQL_ASSOC))
        {
            print "<td class='menu1'><a href='".$ris['id']."'><b>".$ris['name']."</b></a></td>";
        }
    }

    //write_pages ->

    function write_pages($id)
    {
        if($id == NULL)
        {
            //home by blog :3
            pagination();
        }
        else
        {
            $query = "SELECT * FROM pages WHERE  id = '{$id}'";
            $res   = mysql_query($query) or die ("SQL error:".mysql_error());

            while($ris = mysql_fetch_array($res,MYSQL_ASSOC))
            {
                   if(is_admin() == TRUE)
                    {
                        print "<a href='admin?mode=edit_page&edit=".$ris['id']."'>[edit]</a> ";
                        print "<a href='admin?mode=delete_page&delete=".$ris['id']."'>[x]</a>";
                        print "<br>";
                    }
            print $ris['content'];

            }
        }
    }

    //new_page ->

    function new_page($name,$content)
    {
        $query = "INSERT INTO pages (name,content) VALUES ('$name','$content')";
        $res   = mysql_query($query) or die ("SQL error:".mysql_error());
        if($res)
        {
            print "This page inserted with success :D\n";
        }
        else
        {
            print "This page is not included :(\n";
        }
    }

    //post ->

    function post($author,$name,$content,$hour,$date)
    {
        $query = "INSERT INTO articles(author,name,content,hour,date) VALUES ('$author','$name','$content','$hour','$date')";
        $res   = mysql_query ($query) or die ("Errore nell'esecuzione della query: ".mysql_error());

        if($res)
        {
            print "Post inserted with success :D\n";
        }

        else
        {
            print "NOOO!!!, error :( :(\n";
        }
    }

    //write_post ->

    function write_post($id)
    {
        $query = "SELECT * FROM articles WHERE id = '{$id}'";
        $res   = mysql_query($query) or die ("SQL error:".mysql_error());

        while($ris = mysql_fetch_array($res,MYSQL_ASSOC))
        {
            print "<div class='articles'>";
            print "<center><h3><b>".$ris['name']."</b></h3></center><br>";
            print $ris['content']."<br>";
            print "<p align='right'>Posted by <b>".$ris['author']."</b> :: ".$ris['date']." at ".$ris['hour']."</p>";
            print "</div>";

        }
    }

    //pagination ->

    function pagination()
    {
        $query = "SELECT * FROM articles";
        $res   = mysql_query($query) or die ("SQL error:".mysql_error());
        $num   = mysql_num_rows($res);

        if($num % 5 > 0)
        {
            $pages = (int) ($num / 5) + 1;
        }
        else
        {
            $pages = (int) ($num / 5);
        }

        if (isset ($_GET ['page']))
        {
            $id = intval ($_GET ['page']);
            $from = abs ($id - 1) * 5;
            $to = 5;
        }
        else
        {
            $from = 0;
            $to   = 5;
        }

        $print = "SELECT * FROM articles ORDER BY id DESC LIMIT {$from},{$to}";
        $res   = mysql_query ($print) or die ("SQL error:".mysql_error());

        while($ris = mysql_fetch_array($res,MYSQL_ASSOC))
        {
            $article = "";
            $size    = strlen($ris['content']);

            if($size > 200)
            {
                $size = 200;
            }

            for($i = 0;$i < $size; $i++)
            {
                $article .= $ris['content'][$i];
            }

            print "<div class='article'>";
            print "<center><a href='post-".$ris['id']."'>".$ris['name']."</a></center>";
            if(is_admin() == TRUE)
            {
                print "<a href='admin?mode=edit&edit=".$ris['id']."'>[edit]</a> ";
                print "<a href='admin?mode=delete&delete=".$ris['id']."'>[x]</a>";
                print "<br>";
            }
            print $article;
            print "</div>";


        }
        $stat = (int) $_GET['page'];
        print "<table>";
        print "     <tr>";

        if($stat >= 2)
        {
            $stat --;
            print " <td><a href='page-".$stat."'><= </a></td>";
        }

        for($c = 1; $c <= $pages; $c++)
        {

            print " <td class = 'pages'><a href='page-".$c."'>".$c."</a></td>";

        }

        if(end_posts($stat) == TRUE)
        {
            $stat ++;
            print "     <td><a href='page-".$stat."'> =></a></td>";
        }
        print "     </tr>";
        print "</table>";

    }

    //end_posts ->

    function end_posts($id)
    {
        $query = "SELECT * FROM articles WHERE id = '{$id}'";
        $res   = mysql_query($query) or die ("SQL error:".mysql_error());
        $num   = mysql_num_rows($res);
        if($num == 1)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    //delte_article ->

    function delete_article($id)
    {
        $query = "DELETE FROM articles WHERE id = '{$id}'";
        $res   = mysql_query($query) or die ("SQL error:".mysql_error());
        if($res)
        {
            print "Article deleted with success\n";
        }
        else
        {
            print "Article not eliminated :(\n";
        }
    }

    //delte_page ->
    function delete_page($id)
    {
        $query = "DELETE FROM pages WHERE id = '{$id}'";
        $res   = mysql_query($query) or die ("SQL error:".mysql_error());
        if($res)
        {
            print "Page deleted with success\n";
        }
        else
        {
            print "Error deleting page :(\n";
        }
    }

    //edit ->

    function edit($id)
    {
        $query = "SELECT * FROM articles WHERE id = '{$id}'";
        $res   = mysql_query($query) or die ("SQL error:".mysql_error());
        while($ris = mysql_fetch_array($res,MYSQL_ASSOC))
        {
            print "<form action = 'admin.php?mode=edit&edit={$id}' method = 'POST'>";
            print "<input type = 'text' name = 'name' value = '".$ris['name']."'><br>";
            print "<textarea name = 'content' >".$ris['content']."</textarea><br>";
            print "<input type = 'text' name = 'date' value = '".$ris['date']."'> <input type = 'text' name = 'hour' value = '".$ris['hour']."'><br>";
            print "<input type = 'submit' value = 'edit'> <input type = 'reset' value = 'reset'>";
            print "</form>";
        }

        if(!empty($_POST['name']) || !empty($_POST['content']) || !empty($_POST['date']) || !empty($_POST['hour']))
        {

            $content = clearRequest ('content');
            $name = clearRequest ('name');
            $date = date ("d:m:y");
            $hour = date ("H:i:s");

            $edit   = "UPDATE articles SET name = '{$name}',content = '{$content}',date = '{$date}',hour = '{$hour}' WHERE id = '{$id}'";
            $result = mysql_query($edit) or die ("SQL error:".mysql_error());
            if($result)
            {
                print "Edited articole :)\n";
                header("Refresh: 4; URL=post-{$id}");
            }
            else
            {
                print "Articole not edited :(\n";
                header("Refresh: 4; URL=post-{$id}");
            }

        }
    }

    function edit_page($id)
    {
        $query = "SELECT * FROM pages WHERE id = '{$id}'";
        $res   = mysql_query($query) or die ("SQL error:".mysql_error());
        while($ris = mysql_fetch_array($res,MYSQL_ASSOC))
        {
            print "<form action = 'admin.php?mode=edit_page&edit={$id}' method = 'POST'>";
            print "<input type = 'text' name = 'name' value = '".$ris['name']."'><br>";
            print "<textarea name = 'content' >".$ris['content']."</textarea><br>";
            print "<input type = 'submit' value = 'edit'> <input type = 'reset' value = 'reset'>";
            print "</form>";
        }

        if(!empty($_POST['name']) || !empty($_POST['content']))
        {

            $content = clearRequest ('content');
            $name    = clearRequest ('name');


            $edit   = "UPDATE pages SET name = '{$name}',content = '{$content}' WHERE id = '{$id}'";
            $result = mysql_query($edit) or die ("SQL error:".mysql_error());
            if($result)
            {
                print "page edited :)\n";
                header("Refresh: 2; URL={$id}");
            }
            else
            {
                print "Articole not edited :(\n";
                header("Refresh: 4; URL={$id}");
            }

        }
    }

    //edit_username ->

    function edit_username()
    {
        print "<form method = 'POST' action='admin.php?mode=edit_username'>";
        print "new username: <input type = 'text' name = 'username'><br>";
        print "password: <input type = 'password' name = 'password'><br>";
        print "<input type = 'submit' value = 'edit'> <input type = 'reset' value = 'reset'><br>";
        print "</form>";

        if(!empty($_POST['username']) && !empty($_POST['password']))
        {
            $password = md5(sha1($_POST['password']));
            $username = htmlentities($_POST['username']);

            $query = "SELECT * FROM users WHERE password = '{$password}'";
            $res   = mysql_query($query) or die ("SQL error:".mysql_error());
            $num   = mysql_num_rows($res);
            if($num != 1)
            {
                print "Wrong password\n";
            }
            else
            {
                $update = "UPDATE users SET username = '{$username}' WHERE password = '{$password}'";
                $result = mysql_query($update) or die ("SQL error:".mysql_error());
                if($result)
                {
                    print "Username changed with success";
                }
                else
                {
                    print "Amended to problems in the username";
                }
            }
        }
    }

    //edit_password ->

    function edit_password()
    {
        print "<form method = 'POST' action='admin.php?mode=edit_password'>";
        print "password: <input type = 'text' name = 'password'><br>";
        print "new password: <input type = 'password' name = 'new_password'><br>";
        print "<input type = 'submit' value = 'edit'> <input type = 'reset' value = 'reset'><br>";
        print "</form>";

        if(!empty($_POST['password']) && !empty($_POST['new_password']))
        {
            $password     = md5(sha1($_POST['password']));
            $new_password = md5(sha1($_POST['new_password']));

            $query = "SELECT * FROM users WHERE password = '{$password}'";
            $res   = mysql_query($query) or die ("SQL error:".mysql_error());
            $num   = mysql_num_rows($res);
            if($num != 1)
            {
                print "Existing user\n";
            }
            else
            {
                $update = "UPDATE users SET password = '{$new_password}' WHERE password = '{$password}'";
                $result = mysql_query($update) or die ("SQL error:".mysql_error());
                if($result)
                {
                    print "Password changed with success\n";
                    setcookie('biscotto',$password,time()-20000,'/');
                    setcookie('biscotto',$new_password,time()+2000,'/');
                    if(is_logged() == TRUE)
                    {
                        print "setcookie ok\n";
                    }
                    else
                    {
                        print "cookie not set\n";
                        header("Refresh: 4; URL=/login");
                    }

                }
                else
                {
                    print "Error, password not changed\n";
                }
            }
        }
    }

?>
