// rules for input fields
const rules = [
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

let settings;
let result;
let error;

let form;
let formData;

const pricing = [
  [1, 2],
  [3, 4],
  [5],
  [6]
];
let selected;
let price;

$('form .input-fields input').on('change', function() {
  if (error) {
    clientSide();
    errorOccurred(result);
  }
});

$('.menu ul li').on('click', function() {
  if (error) {
    clientSide();
    errorOccurred(result);
  }

  calculatePrice($('input[name="level"]:checked').attr('id'));
});

$('.student .radio-buttons label').on('click', function() {
  calculatePrice($(this).text());
});

$('form .input-fields textarea').on('input', function() {
  $('.remaining-characters').text('noch ' + (500 - $(this).val().length) + ' Zeichen'); // calculate remaining characters
});

function clientSide() {
  result = []; // reset

  if (settings !== 2) {
    form = $('form .input-fields .tab-' + tabs[1] + ' :input');

    if (settings === 0) {
      $.merge(form, $('input[name="level"]'));
    } else {
      $.merge(form, $('input[name="form_of_address"]'));
    }
  } else {
    form = $('input[name="level"], input[name="form_of_address"], form .input-fields .tab:not(.tab-1, .tab-2, .tab-4) :input');
  }

  form.each(function() {
    if (rules[settings][0].includes(this.name)) {
      if ($(this).val().length === 0) {
        result.push(this.name);
      }
    } else if (rules[settings][1].includes(this.name)) {
      if (!$(this).val().match(/^([0-9]{2})+\.([0-9]{2})+\.([0-9]{4})$/)) {
        result.push(this.name);
      }
    } else if (rules[settings][2].includes(this.name)) {
       if (!$('input[name=' + this.name + ']:checked').val() && !result.includes(this.name)) {
         result.push(this.name);
       }
    } else if (rules[settings][3].includes(this.name)) {
      if (!$(this).val().toLowerCase().match(/^[a-z0-9]+(?:[a-z0-9._-]+[a-z0-9]+)?@+[a-z0-9]+(?:[._-][a-z0-9]+)*\.[a-z0-9]+$/)) {
        result.push(this.name);
      } else {
        let explode = [
          $(this).val().split('@'),
          $(this).val().split('@')[1].split('.')
        ];

        if ($(this).val().length > 254 || explode[0][0].length > 64) {
          result.push(this.name);
        } else {
          for (let i = 0; i < explode[1].length; i++) {
            if (explode[1][i].length > 64) {
              result.push(this.name);
              break;
            } else if (i === explode[1].length - 1) {
              if (explode[1][i].length < 2) {
                result.push(this.name);
              }
            }
          }
        }
      }

      // compare emails
      if ((this.name === 'legal_guardian_email' || this.name === 'legal_guardian_repeat_email') && !result.includes(this.name) && $('.tab-' + tabs[1] + ' input[name="legal_guardian_email"]').val().toLowerCase() !== $('.tab-' + tabs[1] + ' input[name="legal_guardian_repeat_email"]').val().toLowerCase()) {
        result.push(this.name);
      }
    } else if (rules[settings][4].includes(this.name)) {
      if (!$(this).val().replace(/\s+/g, '').match(/^[0-9]{10}$/)) {
        result.push(this.name);
      }
    } else if (rules[settings][5].includes(this.name)) {
      if (!$(this).val().match(/^[0-9]{4}$/)) {
        result.push(this.name);
      }
    } else if (rules[settings][6].includes(this.name)) {
      if ($(this).val().length > 500) {
        result.push(this.name);
      }
    }
  });
}

function returnPost(s) {
  settings = s;
  error = false;

  clientSide();

  if (result.length === 0) {
    // prepare data for PHP
    $('.enable-click input').prop('disabled', false);
    formData = 'settings=' + settings + '&' + form.serialize(); // settings
    $('.enable-click input').prop('disabled', true);

    return $.post('../control/request.php', formData, function(result) {});
  } else {
    return $.Deferred().resolve(result);
  }
}

function errorOccurred(result) {
  form.each(function() {
    if (result.includes(this.name)) { // found in result
      error = true;
      $(this).addClass('error');
    } else {
      if (settings !== 2) {
        $('.tab-3 :input[name=' + this.name + ']').val($.trim($(this).val().replace(/\s+/g, ' '))); // add data to review
      }

      $(this).removeClass('error');
    }
  });
}

function insertIntoDatabase() {
  return $.post('../control/insert.php', formData, function(result) {});
}

function updateDatabase() {
  formData = formData + '&' + authenticationParamter;
  return $.post('../control/update-database.php', formData, function(result) {});
}

function calculatePrice(selected) {
  $.each(pricing, function(index, data) {
    if (data.includes(parseInt(selected))) {
      if (index !== 3) {
        price = 50 + (index * 20);

        if ($('input[name="school"]').val().toLowerCase().includes('andere') && index !== 3) {
          price = price + 10;
        }
      } else {
        price = 120;
      }

      return false;
    }
  });

  $('#pricing').text('CHF ' + price + '.-');
}
