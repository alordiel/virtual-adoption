document.addEventListener('DOMContentLoaded', function () {
  document.getElementById('featured-image-button').addEventListener('click', function (e) {
    e.preventDefault();
    const aw_uploader = wp.media({
      title: 'Featured image',
      button: {
        text: 'Use this image'
      },
      multiple: false
    }).on('select', function () {
      const attachment = aw_uploader.state().get('selection').first().toJSON();
      document.getElementById('featured-image-id').value = attachment.id;
      document.getElementById('remove-image-button').style.display = 'block';
      const imageHolder = document.getElementById('featured-image-block');
      imageHolder.src = attachment.url;
      imageHolder.style.display = 'block'
    })
      .open();
  });

  document.getElementById('remove-image-button').addEventListener('click', function (e) {
    e.preventDefault();
    const imageHolder = document.getElementById('featured-image-block');
    imageHolder.src = '';
    imageHolder.style.display = 'none'
    document.getElementById('featured-image-id').value = '';
    document.getElementById('remove-image-button').style.display = 'none';
  });
});
