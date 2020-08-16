const authenticationParamter = 'authentication=' + new URLSearchParams(window.location.search).get('authentication');
const parameters = ['schueler_name', 'schueler_nachname', 'geburtsdatum', 'instrument', 'obligatorisches_stueck', 'selbstwahlstueck_titel', 'selbstwahlstueck_komponist', 'musikschule', 'lehrer_name', 'lehrer_nachname', 'lehrer_email', 'lehrer_telefonnummer', 'erziehungsberechtigte_r_name', 'erziehungsberechtigte_r_nachname', 'erziehungsberechtigte_r_telefonnummer', 'strasse', 'postleitzahl', 'ort', 'bemerkung'];

$.post('../control/load-registration-data.php', authenticationParamter, function(result) {
  if (result) {
    result = JSON.parse(result);
    form = $('form .input-fields :input').not('input[name="level"], input[name="form_of_address"]');

    form.each(function(index) {
      $(this).val(result[parameters[index]]);
    });

    $('#' + result[5]).attr('checked', true);

    if (result[14] === 'Frau') {
      $('#ms').attr('checked', true);
    } else {
      $('#mr').attr('checked', true);
    }

    calculatePrice(result[5]);
  } else {
    $('#note').text('Es konnten leider keine Benutzerdaten gefunden werden.').addClass('error-message');

    $(':input, button').attr('disabled', true);
    $('*').off();
  }
});

$('button').click(function() {
  returnPost(2).then(function(result) {
    errorOccurred(result);

    if (!error) {
      updateDatabase().then(function(result) {
        if (result) {
          $('#note').text('Ihre Eingaben wurden gespeichert.').addClass('success-message');
        } else {
          $('#note').text('Es gab leider ein Problem. Bitte laden Sie die Seite neu.').addClass('error-message');
        }

        // scroll to top
        $('html').scrollTop(0);
        $('form .input-fields').scrollTop(0);

        setTimeout(function () {
          $('#note').text('').removeClass();
        }, 5000);
      });
    }
  });
});
