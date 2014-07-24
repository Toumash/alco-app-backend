$(document).ready(function () {
    var table = $(".alcohols-table");
    table.css("display", "none");
    table.fadeIn(400, 'swing');//, function() {
    //callback function after animation finished
    //$("#myButton").attr('value', 'fadeIn() is now Complete');
    /*
     $(".menu-main a").not("#logout").click(function(e) {
     var content = $(".content");
     $.ajax({
     url: "ajax.php",
     type: "POST",
     dataType: "JSON",
     data: JSON.stringify({"v": $(this).attr('data-v'),
     "a": $(this).attr('data-a')}),
     contentType: "application/json",
     converters: {
     'text json': true
     },
     beforeSend: function() {
     content.fadeOut(300, 'swing', function() {
     content.html('<br><br><center><img src="/images/loader.gif"></center>');
     content.show();
     });
     },
     success: function(data, textStatus) {
     content.hide();
     var htmlFiltered = $(data).filter(".content").html();
     //var htmlFiltered = data;
     content.stop(true, true).html(htmlFiltered).fadeIn(400, 'swing');
     },
     error: function() {
     content.stop(true,true);
     content.show();
     content.html("<p>Przepraszamy, ale strona jest chwilowo niedostępna. Prosimy spróbować ponownie</p>");
     }
     });
     e.preventDefault();
     });
     */
});