</div> <!-- /.container -->

<footer class="blog-footer">
    <p>© overwatch.ee 2016. All rights reserved.</p>
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
</script>
<?php wp_footer(); ?>
</body>
</html>
