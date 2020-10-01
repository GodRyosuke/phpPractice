<?php
    try {
        $dsn = "mysql:dbname=test;host=localhost;charset=utf8";
        $db = new PDO($dsn, "root", "");
    } catch(PDOException $e) {
        echo "DB接続エラー: ".$e->getMessage;
    }
    $error = array();
    if (isset($_POST)) {
        $name = "";
        $email = "";
        $password = "";
        if (!empty($_POST["username"])) {
            $name = $_POST["username"];
        }
        if (!empty($_POST["email"])) {
            $email = $_POST["email"];
            $pattern = "[a-zA-Z-]+@(gmail.com|ezweb.ne.jp)";
            $output = array();
            $byte = mb_ereg($pattern, $email, $output);
            //echo $email.'<br>'.$output[0];
            if (empty($output)) {
                $error += array("emailerror" => true);
            }elseif ($email != $output[0]) {
                $error += array("emailerror" => true);
            }
        }
        if (!empty($_POST["password"])) {
            $password = $_POST["password"];
        }
        if (empty($error)) {
            $state = $db->prepare("insert into auth (username, email, password) value (:name, :email, :password)");
            $state->bindparam(":name", $name);
            $state->bindparam(":email", $email);
            $state->bindparam(":password", $password);
            $state->execute();
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登録</title>
</head>
<body>
    <h1>新規登録</h1>
    <form action="" method="post">
        <div class="name">
            名前: <input type="text" name="username" placeholder="名前">
        </div>
        <div class="mail">
            メール: <input type="text" name="email" placeholder="メール">
            <span>
                <?php 
                    if (!empty($error)):
                        if (!empty($error["emailerror"])):
                ?>
                <p>メールアドレスを正しく入力してください</p>
                <?php endif; endif; ?>
            </span>
        </div>
        <div class="password">
            パスワード：<input type="password" name="password" placeholder="パスワード">
        </div>
        <button type="submit">登録する</button>
    </form>
</body>
</html>