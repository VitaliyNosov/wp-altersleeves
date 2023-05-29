<?php

$url = 'https://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$url = strtolower($url);

if (strpos($url,'ihateyourdeck') !== false) {
    wp_redirect('https://www.altersleeves.com/ihateyourdeck');
    die();
}

get_header();
?>
    <div id="page" class="page" style="width:100%;display:flex;">
        <div class="wing bg-white-wing"></div>
        <div class="container content bg-white-content">
            <h1>It's a hecking 404! :(</h1>
            <p>But <a href="https://www.instagram.com/missladynacho/" target="_blank" rel="noopener noreferrer">Nacho</a> wants to know if she can do
                anything to
                help :3</p>
            <p><img src="<?= MC_URI_IMG; ?>/front/nacho.jpg" /></p>
            <p>Why don't you try searching for a Magic: The Gathering card in the header? If there is a technical issue, then please contact us <a
                        href="/contact">here</a></p>
        </div>
        <div class="wing bg-white-wing"></div>
    </div>
<?php get_footer();