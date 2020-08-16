$('.menu .enable-click').click(function() {
  if ($(this).hasClass('close')) {
    close();
  } else {
    $('.' + $(this).parent().attr('class').split(' ')[1]).attr('tabindex', 1).focus();
    $('.' + $(this).parent().attr('class').split(' ')[1] + ' .enable-click').addClass('close');

    $('.' + $(this).parent().attr('class').split(' ')[1] + ' ul').show();
  }
});

$('.student .menu, .school .menu').focusout(function() {
  close();
});

$('.menu ul li').click(function() {
  $('.' + $(this).parent().parent().attr('class').split(' ')[1] + ' input').val($(this).text());
  close();
});

function close() {
  $('.menu .enable-click').removeClass('close');
  $('.menu ul').hide();
}
