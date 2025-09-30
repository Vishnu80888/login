$(function(){
  const $form = $('#registerForm');
  const $btn = $('#registerBtn');
  const $spinner = $btn.find('.spinner-border');

  // bootstrap validation
  $form.on('submit', function(e){
    e.preventDefault();
    e.stopPropagation();
    if (!this.checkValidity()) {
      $(this).addClass('was-validated');
      return;
    }
    // collect
    const payload = {
      name: $form.find('[name=name]').val(),
      email: $form.find('[name=email]').val(),
      password: $form.find('[name=password]').val(),
      age: $form.find('[name=age]').val(),
      dob: $form.find('[name=dob]').val(),
      contact: $form.find('[name=contact]').val(),
    };
    $spinner.removeClass('d-none');
    $btn.attr('disabled', true);

    ajaxPost(API_BASE + '/register.php', payload)
      .done(resp => {
        if (resp.success) {
          showToast('Registration successful. Please login.', {delay:3000});
          setTimeout(()=> window.location.href = '/login.html', 1000);
        } else {
          showToast(resp.message || 'Registration failed');
        }
      })
      .fail((xhr)=> {
        const r = xhr.responseJSON || {};
        showToast(r.message || 'Server error');
      })
      .always(() => {
        $spinner.addClass('d-none');
        $btn.attr('disabled', false);
      });
  });

  // password strength (basic)
  $('#regPassword').on('input', function(){
    const v = $(this).val();
    const score = Math.min(100, v.length * 10 + (/[A-Z]/.test(v)?10:0) + (/[0-9]/.test(v)?10:0) + (/[^A-Za-z0-9]/.test(v)?10:0));
    $('#passwordStrength').css('width', score + '%').attr('aria-valuenow', score);
    if (score < 40) $('#passwordStrength').removeClass().addClass('progress-bar bg-danger');
    else if (score < 70) $('#passwordStrength').removeClass().addClass('progress-bar bg-warning');
    else $('#passwordStrength').removeClass().addClass('progress-bar bg-success');
  });

  // custom bootstrap validation: prevent form submit via Enter
  $form.on('keypress', function(e){
    if (e.key === 'Enter') e.preventDefault();
  });
});
