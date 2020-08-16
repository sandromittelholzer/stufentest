<?php
  if ($error === 0) {
    // html headers
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-type: text/html; charset=utf-8';
    $headers[] = 'From: Stufentest Obersee-Linth <noreply@stufentest-oberseelinth.ch>';

    // legal guardian
    $to = $_POST['legal_guardian_email'];
    $subject = 'Zusammenfassung Ihrer Eingabe';
    $message = 'Guten Tag ' . $_POST['form_of_address'] . ' ' . $_POST['legal_guardian_last_name'] . '<br><br>' . $_POST['student_first_name'] . ' ' . $_POST['student_last_name'] . ' hat sich erfolgreich für den Stufentest angemeldet.' . '<br>' . 'Über diesen <a href="https://www.stufentest-oberseelinth.ch/anmeldung/zusammenfassung.html?authentication=' . $authentication . '">Link</a> können Sie jederzeit auf Ihre Eingaben zugreifen und ggf. Daten ändern. Bis Montag, 31.08.2020, sollten Sie zusätzlich das obligatorische und das Selbstwahlstück eintragen.' . '<br>' . 'Die Rechnung für die Teilnahmegebühr erhalten Sie mit separater Post und die Einladung zum Stufentest mit den genauen Informationen (Zeit und Raum) erhalten Sie vor dem Stufentest von der Musikschule des jeweiligen Durchführungsortes.' . '<br><br>' . 'Falls Sie in der Zwischenzeit Fragen haben, können Sie sich gerne an <a href="mailto:gaudenz.luegstenmann@rj.sg.ch">Gaudenz Lügstenmann</a> wenden.' . '<br><br>' . 'Freundliche Grüsse' . '<br>' . 'Musikschulen Obersee-Linth';

    mail($to, $subject, $message, implode("\r\n", $headers));

    // teacher
    $to = $_POST['teacher_email'];
    $subject = $_POST['student_first_name'] . ' ' . $_POST['student_last_name'] . ' hat sich für den Stufentest angemeldet';
    $message = 'Guten Tag' . '<br><br>' . 'Ihr(e) Schüler/-in hat sich für den Stufentest angemeldet. Die hinterlegten Daten finden sie unten. Besten Dank für Ihre Kenntnisnahme.' . '<br><br>' . 'Zusammenfassung:' . '<br>' . $_POST['student_first_name'] . ' ' . $_POST['student_last_name'] . '<br>' . $_POST['birthdate'] . '<br>' . $_POST['instrument'] . '<br>' . 'Stufe ' . $_POST['level'] . '<br><br>' . $_POST['form_of_address'] . '<br>' . $_POST['legal_guardian_first_name'] . ' ' . $_POST['legal_guardian_last_name'] . '<br>' . $_POST['legal_guardian_email'] . '<br>' . $_POST['legal_guardian_telephone_number'] . '<br>' . $_POST['address_line'] . '<br>' . $_POST['postcode'] . ' ' . $_POST['region'];

    mail($to, $subject, $message, implode("\r\n", $headers));
  }
?>
