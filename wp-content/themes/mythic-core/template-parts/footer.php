<footer class="wrapper">

    <div id="footer" class="inner" role="presentation">

        <?php

        do_action( 'mc_footer_top' );

        do_action( 'mc_footer_nav' );

        do_action( 'mc_footer_middle' );

        // https://www.lawdepot.com/blog/what-not-to-include-in-your-website-disclaimer/
        do_action( 'mc_disclaimer' );

        // https://www.nolo.com/legal-encyclopedia/when-do-you-need-copyright-notice-websites-and-where-do-you-place-it.html
        do_action( 'mc_copyright' );

        do_action( 'mc_footer_bottom' );

        ?>

    </div>

</footer>
