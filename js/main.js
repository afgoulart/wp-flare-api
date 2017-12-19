;(function($){
  $(document).ready(function () {
    $('[data-on-visible]').each(function(){
      var $this = $(this);
      var condition = $this.data('on-visible');
      $('[name="' + condition.input_name + '"]').on('change', function(e) {
        var value = $(e.target).val();
        if (value === condition.value) {
          $this.show();
        } else {
          $this.find('input').val('');
          $this.hide();
        }
      })
    });
  });
}(jQuery));