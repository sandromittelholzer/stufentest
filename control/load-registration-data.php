<?php
  require '../include/config.php';

  function executeStatement($pdo, $sql) {
    for ($i = 0; $i < 2; $i++) {
      $statement = $pdo->prepare($sql);
      $statement->execute(['authentication' => $_POST['authentication']]);

      $result[] = $statement->fetch();

      if (!$result[0]['bestaetigt'] && $i === 0) {
        $sql = "UPDATE tbl_anmeldungen SET bestaetigt = 1 WHERE authentifizierungsschluessel = :authentication";
      } else {
        return $result[0];
      }
    }
  }

  $sql = "SELECT * FROM tbl_anmeldungen WHERE authentifizierungsschluessel = :authentication";
  $result = executeStatement($pdo, $sql);

  if ($result) {
    $result['geburtsdatum'] = date('d.m.Y', strtotime($result['geburtsdatum']));
    echo json_encode($result);
  }
?>
