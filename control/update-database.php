<?php
  ob_start();
  require '../include/config.php';
  require 'request.php';
  ob_end_clean(); // erase output buffer and turn off output buffering

  if ($settings == 2 && count($error) === 2) {
    // update database
    $parameters = ['student_first_name', 'student_last_name', 'birthdate', 'instrument', 'level', 'part', 'optional_part', 'optional_composer', 'school', 'teacher_first_name', 'teacher_last_name', 'teacher_email', 'teacher_telephone_number', 'form_of_address', 'legal_guardian_first_name', 'legal_guardian_last_name', 'legal_guardian_telephone_number', 'address_line', 'postcode', 'region', 'comment', 'authentication'];
    $_POST['birthdate'] = date('Y-m-d', strtotime($_POST['birthdate']));

    $sql = "UPDATE tbl_anmeldungen SET schueler_name = :student_first_name, schueler_nachname = :student_last_name, geburtsdatum = :birthdate, instrument = :instrument, stufe = :level, obligatorisches_stueck = :part, selbstwahlstueck_titel = :optional_part, selbstwahlstueck_komponist = :optional_composer, musikschule = :school, lehrer_name = :teacher_first_name, lehrer_nachname = :teacher_last_name, lehrer_email = :teacher_email, lehrer_telefonnummer = :teacher_telephone_number, anrede = :form_of_address, erziehungsberechtigte_r_name = :legal_guardian_first_name, erziehungsberechtigte_r_nachname = :legal_guardian_last_name, erziehungsberechtigte_r_telefonnummer = :legal_guardian_telephone_number, strasse = :address_line, postleitzahl = :postcode, ort = :region, bemerkung = :comment WHERE authentifizierungsschluessel = :authentication";

    $statement = $pdo->prepare($sql);

    for ($i = 0; $i < count($parameters); $i++) {
      $temp = [
        $_POST[$parameters[$i]],
        preg_replace('/\s+/', '', $_POST[$parameters[$i]])
      ];

      if (preg_match('/telephone/', $parameters[$i])) {
        $_POST[$parameters[$i]] = substr($temp[1], 0, -3 -2 -2) . ' ' . substr($temp[1], 3, -2 -2) . ' ' . substr($temp[1], 6, -2) . ' ' . substr($temp[1], 8); // caching
      } else if (!preg_match('/email|comment|part|optional_part|optional_composer/', $parameters[$i])) {
        $_POST[$parameters[$i]] = strtolower(trim(preg_replace('/\s+/', ' ', ucwords($temp[0])))); // uppercase first character
      }

      $statement->bindParam(':' . $parameters[$i], $_POST[$parameters[$i]]);
    }

    $statement->execute();
    echo true;
  }
?>
