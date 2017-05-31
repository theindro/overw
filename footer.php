</div> <!-- /.container -->

<footer class="blog-footer">
    <p>© overwatch.ee 2017. All rights reserved.</p>
    <p style="color:#575757;">Kontakt: info@overwatch.ee</p>
    <p>
        <a href="#" id="return-to-top">Tagasi üles!</a>
    </p>
</footer>

<script>

    $('#return-to-top').click(function () {      // When arrow is clicked
        $('body,html').animate({
            scrollTop: 0                       // Scroll to top of body
        }, 500);
    });


    // Battletagi otsimine
    $('.btn-esita').click(function () {
        $('#uuenda-loading').css("display", "inline-block");
        $("#ajax_call_return").empty();
        $(".btn-esita").attr("disabled", "disabled")

        var battletag = $('.input-tag').val();
        $.post(ajaxurl, {action: 'battletag_from_api_to_database', battletag: battletag}, function (output) {
            if (output == 'Ok') {
                window.location = '<?= get_site_url();?>/profiil/' + battletag;
            } else {
                var alert_div = $("#ajax_call_return");
                alert_div.append("<p id='ajax_call_alert'> <i class='fa fa-exclamation-triangle' aria-hidden='true'></i> " + output + "</p>");
                alert_div.fadeIn("fast");
                $('#uuenda-loading').css("display", "none");
                $(".btn-esita").removeAttr("disabled")
            }
        });
    });

    // Profiili uuendamine
    $('#uuenda').on('click', function () {

        var battletag = $('.main-info-tag').html();
        var battletag_id = $('.main-info-tag').data('battletag_id');

        $('#uuenda-loading').css("display", "inline-block");
        $("#uuenda").attr("disabled", true);
        $.post(ajaxurl, {
            action: 'update_profile',
            data: {battle_tag: battletag, battle_tag_id: battletag_id}
        }, function (res) {
            if (res == 'Ok') {
                location.reload();
            } else {
                alert("Uuendamine ebaõnnestus!");
            }
        });
    });




</script>


<?php wp_footer(); ?>

</body>
</html>
