$(function(){
  if (!ensureAuthOrRedirect()) return;

  const loadProfile = () => {
    $.ajax({
      url: API_BASE + '/get_profile.php',
      method: 'GET',
      headers: authHeaders(),
      dataType: 'json'
    }).done(resp => {
      if (!resp.success) {
        showToast('Session expired. Please login again.');
        setToken(null);
        return setTimeout(()=> window.location.href = '/login.html', 800);
      }
      const u = resp.user;
      $('#infoName').text(u.name);
      $('#infoEmail').text(u.email);
      $('#infoDob').text(u.dob || '—');
      $('#infoContact').text(u.contact || '—');
      $('#infoAge').text(u.age ? ('Age: ' + u.age) : '');
      $('#profileName').text(u.name);
      // prefill modal form
      const f = $('#editForm');
      f.find('[name=name]').val(u.name);
      f.find('[name=contact]').val(u.contact);
      f.find('[name=age]').val(u.age);
      f.find('[name=dob]').val(u.dob);
    }).fail(()=> {
      showToast('Unable to get profile');
    });
  };

  loadProfile();

  $('#editForm').on('submit', function(e){
    e.preventDefault();
    const payload = {
      name: $(this).find('[name=name]').val(),
      contact: $(this).find('[name=contact]').val(),
      age: $(this).find('[name=age]').val(),
      dob: $(this).find('[name=dob]').val(),
    };
    $('#saveProfileBtn').attr('disabled', true);
    ajaxPost(API_BASE + '/update_profile.php', payload)
      .done(resp => {
        if (resp.success) {
          showToast('Profile updated');
          loadProfile();
          $('#editModal').modal ? $('#editModal').modal('hide') : null;
        } else {
          showToast(resp.message || 'Update failed');
        }
      })
      .fail(()=> showToast('Update failed'))
      .always(()=> $('#saveProfileBtn').attr('disabled', false));
  });

  // logout (with modal confirmation)
  $('#logoutBtn, #logoutModalBtn').on('click', function(){
    if (!confirm('Are you sure you want to logout?')) return;
    const token = getToken();
    $.ajax({
      url: API_BASE + '/logout.php',
      method: 'POST',
      contentType: 'application/json',
      data: JSON.stringify({token}),
      dataType: 'json'
    }).always(()=> {
      setToken(null);
      window.location.href = '/login.html';
    });
  });
});
