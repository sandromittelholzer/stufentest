let tabs = [
  0, // previous
  1, // current
  2  // next
];

function changeTab(caller) {
  // scroll to top
  $('html').scrollTop(0);
  $('form .input-fields').scrollTop(0);

  animateProgressBar();

  if (caller === 0) {
    $('form .progress-bar .step:nth-child(' + (tabs[0] + 1) + ')').attr('class', 'step');
    $('form .progress-bar .step:nth-child(' + (tabs[1] + 1) + ')').attr('class', 'step current');
  } else {
    $('form .progress-bar .step:nth-child(' + (tabs[0] + 1) + ')').attr('class', 'step completed');
    $('form .progress-bar .step:nth-child(' + (tabs[1] + 1) + ')').attr('class', 'step current');
  }

  // change tab
  $('form .input-fields .tab-' + tabs[0]).hide();
  $('form .input-fields .tab-' + tabs[1]).show();

  // change buttons
  if (tabs[1] === 2) {
    $('form .navigation #previous').show();
  } else {
    $('form .navigation #previous').hide();

    if (tabs[1] === 3) {
      $('form .navigation #next').text('Senden');
    } else if (tabs[1] === 4) {
      $('form .navigation #next').hide();
    }
  }
}

function animateProgressBar() {
  $('form .progress-bar .line .progress').css('width', 'calc((100% - 100px) / 3 * ' + (tabs[1] - 1) + ' - 24px + ' + (tabs[1] - 2) * 50 + 'px)'); // animate progress bar
}

$('button').click(function() {
  if (this.id === 'previous') {
    if (tabs[1] === 2) {
      $('.tab-' + tabs[1] + ' :input').removeClass('error'); // remove error

      tabs[0] = tabs[1];
      tabs[1]--;
      tabs[2]--;

      changeTab(0);
    }
  } else if (tabs[1] !== 4) {
    returnPost(tabs[1] - 1).then(function(result) {
      errorOccurred(result);

      if (!error) {
        if (tabs[1] !== 3) {
          tabs[0] = tabs[1];
          tabs[1]++;
          tabs[2]++;

          changeTab(1);
        } else {
          insertIntoDatabase().then(function(result) {
            if (!result) {
              // personalized authentication message
              $('.authentication .student').text($('.tab-' + tabs[1] + ' input[name="student_first_name"]').val() + ' ' + $('.tab-' + tabs[1] + ' input[name="student_last_name"]').val());
              $('.authentication .email').text($('.tab-' + tabs[1] + ' input[name="legal_guardian_email"]').val());

              tabs[0] = tabs[1];
              tabs[1]++;
              tabs[2]++;

              changeTab(1);
            } else {
              // scroll to top
              $('html').scrollTop(0);
              $('form .input-fields').scrollTop(0);

              $('#error-message').text(result);
            }
          });
        }
      }
    });
  }
});
