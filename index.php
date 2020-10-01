<?php 
  try { // DB接続
    $dsn = "mysql:dbname=test;host=localhost;charset=utf8";
    $db = new PDO($dsn, "root", '');
  } catch (PDOException $e) {
    echo 'DB接続エラー：'.$e->getMessage();
  }

  if (!isset($_COOKIE['isFirst'])) { // 初回アクセスならば
    setcookie('isFirst', 'login', time() + 300);
    //$db->query('delete from sportbuttons');
  }
  $error = array();
  if (isset($_POST)) { 
    if (!empty($_POST['newButton'])) {// 新規登録処理
      $st = $db->prepare("select * from sportbuttons where name=:nm");
      $st->bindparam(":nm", $_POST['newButton']);
      $st->execute();
      if ($st->fetch()) { // すでに同じ名前の競技が追加されていたら
        $arraytemp = array('nameerror' => true);
        $error += $arraytemp;
      } else { 
        $query = "insert into sportbuttons (name, votes) value (:value, 1)";
        $statement = $db->prepare($query);
        $statement->bindParam(":value", $_POST['newButton']);
        $statement->execute();
      }
    }
 
    foreach($_POST as $key => $value) {
      if (($key != 'newButton') && ($key != 'isdelete')) {
        $sID = substr($key, 5); // sportのID取り出し
        $sql = "";
        if (!empty($_POST['isdelete'])) { // 削除処理
          $sql = "delete from sportbuttons where id=:pID";
          $st = $db->prepare($sql);
          $st->bindparam(':pID', $sID);
          $st->execute();
        } else { // 得票数更新
          $sql = "select * from sportbuttons where id=:pID";
          $st = $db->prepare($sql);
          $st->bindparam(':pID', $sID);
          $st->execute();
          $fst = $st->fetch();
          $currentVote = $fst['votes']; // 現在の得票数取得
          $currentVote++;
          $sql = "update sportbuttons set votes=:vote where id=:pID";
          $st = $db->prepare($sql);
          $st->bindparam(':pID', $sID);
          $st->bindparam(':vote', $currentVote);          
          $st->execute(); // 更新
        }
      } 
    }
  }
  $sports = $db->query("select * from sportButtons");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>基礎からの PHP</title>
</head>
<body>
    <h1>this is php practice</h1>
    <form action="" method="post">
<?php 
  while ($sport = $sports->fetch()): 
?>
    <div class="sports">
      <input type="radio" name="sport<?php echo $sport['id']; ?>"><?php echo $sport['name'].' 得票数:'.$sport['votes']; ?>
    </div>
<?php 
  endwhile; 
?>
    <div class="textform">
      <input type="text" name="newButton" placeholder="新規作成">
    </div>
    <div class="deletebox">
      <input type="checkbox" name="isdelete">選んだものを削除
    </div>
    <button type="submit">送信</button>
    <div class="error">
<?php
  if (!empty($error)) {
    if (isset($error['nameerror'])) {
      if ($error['nameerror']) {
        echo 'すでに同じ競技が登録されています。';
      }
    }
  }
?>
    </div>
<?php
  var_dump($_POST);
?>
</form>
</body>
</html>