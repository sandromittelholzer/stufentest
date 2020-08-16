<?php
  // rules for input fields
  $rules = [
    [
      ['student_first_name', 'student_last_name', 'instrument', 'school', 'teacher_first_name', 'teacher_last_name'],
      ['birthdate'],
      ['level'],
      ['teacher_email'],
      ['teacher_telephone_number'],
      [],
      []
    ],
    [
      ['legal_guardian_first_name', 'legal_guardian_last_name', 'address_line', 'region'],
      [],
      ['form_of_address'],
      ['legal_guardian_email', 'legal_guardian_repeat_email'],
      ['legal_guardian_telephone_number'],
      ['postcode'],
      ['comment']
    ],
    [
      ['student_first_name', 'student_last_name', 'instrument', 'school', 'teacher_first_name', 'teacher_last_name', 'legal_guardian_first_name', 'legal_guardian_last_name', 'address_line', 'region'],
      ['birthdate'],
      ['level', 'form_of_address'],
      ['teacher_email', 'legal_guardian_email', 'legal_guardian_repeat_email'],
      ['teacher_telephone_number', 'legal_guardian_telephone_number'],
      ['postcode'],
      ['comment']
    ]
  ];
  $settings = $_POST['settings'];

  // determines if input is declared
  for ($i = 0; $i < count($rules[$settings]); $i++) {
    foreach ($rules[$settings][$i] as $name) {
      if (!isset($_POST[$name])) {
        $error[] = $name;
      }
    }
  }

  foreach ($_POST as $name => $data) {
    if (preg_match('/' . $name . '/', implode($rules[$settings][0]))) {
      if (strlen($_POST[$name]) === 0) {
        $error[] = $name;
      }
    } else if (preg_match('/' . $name . '/', implode($rules[$settings][1]))) {
      if (!preg_match('/^([0-9]{2})+\.([0-9]{2})+\.([0-9]{4})$/', $_POST[$name])) {
        $error[] = $name;
      }
    } else if (preg_match('/' . $name . '/', implode($rules[$settings][3]))) {
      if (!preg_match('/^[a-z0-9]+(?:[a-z0-9._-]+[a-z0-9]+)?@+[a-z0-9]+(?:[._-][a-z0-9]+)*\.[a-z0-9]+$/', strtolower($_POST[$name]))) {
        $error[] = $name;
      } else {
        $explode = [
          explode('@', $_POST[$name]),
          explode('.', explode('@', $_POST[$name])[1])
        ];

        if (strlen($_POST[$name]) > 254 || strlen($explode[0][0]) > 64) {
          $error[] = $name;
        } else {
          for ($i = 0; $i < count($explode[1]); $i++) {
            if (strlen($explode[1][$i]) > 64) {
              $error[] = $name;
              break;
            } else if ($i === count($explode[1]) - 1) {
              if (strlen($explode[1][$i]) < 2) {
                $error[] = $name;
              }
            }
          }
        }
      }

      // compare emails
      if (($name === 'legal_guardian_email' || $name === 'legal_guardian_repeat_email') && !preg_match('/' . $name . '/', implode($error)) && strtolower($_POST['legal_guardian_email']) !== strtolower($_POST['legal_guardian_repeat_email'])) {
        $error[] = $name;
      }
    } else if (preg_match('/' . $name . '/', implode($rules[$settings][4]))) {
      if (!preg_match('/^[0-9]{10}$/', preg_replace('/\s+/', '', $_POST[$name]))) {
        $error[] = $name;
      }
    } else if (preg_match('/' . $name . '/', implode($rules[$settings][5]))) {
      if (!preg_match('/^[0-9]{4}$/', $_POST[$name])) {
        $error[] = $name;
      }
    } else if (preg_match('/' . $name . '/', implode($rules[$settings][6]))) {
      if (strlen($_POST[$name]) > 500) {
        $error[] = $name;
      }
    }
  }
?>
