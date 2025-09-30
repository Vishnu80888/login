$(function(){
  const $form = $('#loginForm');
  const $btn = $('#loginBtn');
  const $spinner = $btn.find('.spinner-border');

  $form.on('submit', function(e){
    e.preventDefault();
    if (!this.checkValidity()) {
      $(this).addClass('was-validated');
      return;
    }
    const payload = {
      email: $form.find('[name=email]').val(),
      password: $form.find('[name=password]').val()
    };
    $spinner.removeClass('d-none');
    $btn.attr('disabled', true);

    ajaxPost(API_BASE + '/login.php', payload)
      .done(resp => {
        if (resp.success && resp.token) {
          setToken(resp.token);
          showToast('Login successful', {delay: 1200});
          // redirect to profile
          setTimeout(()=> window.location.href = '/profile.html', 600);
        } else {
          showToast(resp.message || 'Login failed');
        }
      })
      .fail((xhr) => {
        const r = xhr.responseJSON || {};
        showToast(r.message || 'Login failed');
      })
      .always(()=> {
        $spinner.addClass('d-none');
        $btn.attr('disabled', false);
      });
  });
});
