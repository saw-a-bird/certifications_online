$(document).ready(function () {
    function redirect_register() {
        window.location.href = "{{ path('security_registration') }}";
    }

    var isLogged = '{{logged_in}}';
            
    $('.like-button').click(function() {
        if (isLogged == '1') {
         if (($(this).hasClass("selected"))) {
             $(this).removeClass('selected');
             $.post("{{ path('exam_remove_coll') }}", {exam_id: $(this).attr("id")});
         } else {
             $(this).addClass('selected');
             $.post("{{ path('exam_add_coll') }}", {exam_id: $(this).attr("id")});
         }
        } else {
            redirect_register();
        }
     });
 });