<?php
  ob_start();
  require '../include/config.php';
  require 'request.php';
  ob_end_clean(); // erase output buffer and turn off output buffering

  if ($settings == 2 && $error == null) {
    $parameters = ['student_first_name', 'student_last_name', 'birthdate', 'instrument', 'level', 'school', 'teacher_first_name', 'teacher_last_name', 'teacher_email', 'teacher_telephone_number', 'form_of_address', 'legal_guardian_first_name', 'legal_guardian_last_name', 'legal_guardian_email', 'legal_guardian_telephone_number', 'address_line', 'postcode', 'region', 'comment'];
    $_POST['birthdate'] = date('Y-m-d', strtotime($_POST['birthdate']));
    $authentication = md5(trim(preg_replace('/\s+/', ' ', $_POST['student_first_name'] . $_POST['student_last_name'] . $_POST['legal_guardian_email'])) . $_POST['instrument']);

    // check for duplicate entries
    $sql = "SELECT id FROM tbl_anmeldungen WHERE authentifizierungsschluessel = '$authentication'";
    $statement = $pdo->prepare($sql);
    $statement->execute();

    if (!$statement->fetch()) {
      // insert into databse
      $sql = "INSERT INTO tbl_anmeldungen (schueler_name, schueler_nachname, geburtsdatum, instrument, stufe, musikschule, lehrer_name, lehrer_nachname, lehrer_email, lehrer_telefonnummer, anrede, erziehungsberechtigte_r_name, erziehungsberechtigte_r_nachname, erziehungsberechtigte_r_email, erziehungsberechtigte_r_telefonnummer, strasse, postleitzahl, ort, bemerkung, authentifizierungsschluessel) VALUES (:student_first_name, :student_last_name, :birthdate, :instrument, :level, :school, :teacher_first_name, :teacher_last_name, :teacher_email, :teacher_telephone_number, :form_of_address, :legal_guardian_first_name, :legal_guardian_last_name, :legal_guardian_email, :legal_guardian_telephone_number, :address_line, :postcode, :region, :comment, '$authentication')";
      $statement = $pdo->prepare($sql);

      for ($i = 0; $i < count($parameters); $i++) {
        $temp = [
          $_POST[$parameters[$i]],
          preg_replace('/\s+/', '', $_POST[$parameters[$i]])
        ];

        if (preg_match('/telephone/', $parameters[$i])) {
          $_POST[$parameters[$i]] = substr($temp[1], 0, -3 -2 -2) . ' ' . substr($temp[1], 3, -2 -2) . ' ' . substr($temp[1], 6, -2) . ' ' . substr($temp[1], 8); // caching
        } else if (!preg_match('/email|comment/', $parameters[$i])) {
          $_POST[$parameters[$i]] = ucwords($temp[0]); // uppercase first character
        }

        $statement->bindParam(':' . $parameters[$i], $_POST[$parameters[$i]]);
      }

      $statement->execute();

      // send email
      $_POST['birthdate'] = date('d.m.Y', strtotime($_POST['birthdate']));
      $error = 0;

      require_once 'send-email.php';
    } else {
      echo $_POST['student_first_name'] . ' ' . $_POST['student_last_name'] . ' wurde bereits für den Stufentest angemeldet.';
    }
  } else {
    echo 'Es gab leider ein Problem. Bitte laden Sie die Seite neu.';
  }
?>
