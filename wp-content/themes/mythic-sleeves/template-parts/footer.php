<footer id="footer" class="py-2 footer">
    <div class="container">
        <div class="row justify-content-start">

            <div class="order-2 col-sm-6 col-md-4 py-3 text-center text-sm-start">
                <h3>Information</h3>
                <ul>
                    <li><a title="About Alter Sleeves" href="/about">About</a></li>
                    <li><a title="Contact Alter Sleeves" href="https://help.mythicgaming.com/portal/en/newticket">Contact Us</a></li>
                    <li><a title="The Alter Sleeves privacy policy" href="/privacy-policy">Privacy
                            Policy</a></li>
                    <li><a title="The Alter Sleeves End User License Agreement" href="https://www.mythicgaming.com/end-user-license-agreement">End
                            User License
                            Agreement</a>
                    </li>
                    <li><a title="The Alter Sleeves Wall of Heroes" href="/wall-of-heroes">Wall Of Heroes</a></li>
                </ul>
            </div>
            <div class="order-1 order-sm-3 col-sm-6 col-md-4 py-3 text-center text-sm-start">
                <h3>Follow Us</h3>
                <div class="d-inline-block w-auto">
                    <a href="https://www.instagram.com/altersleeves" target="_blank" title="The Alter Sleeves Instagram"><i
                                class="fab fa-instagram"></i></a>
                </div>
                <div class="d-inline-block w-auto px-2">
                    <a href="https://twitter.com/altersleeves" target="_blank" title="The Alter Sleeves Twitter Feed"><i
                                class="fab fa-twitter"></i></a>
                </div>
                <div class="d-inline-block w-auto">
                    <a href="https://www.facebook.com/altersleeves" target="_blank" title="The Alter Sleeves
                    Facebook page"><i class="fab fa-facebook"></i></a>
                </div>
                <?php if( \Mythic_Core\Objects\MC_User::isArtist() ) : ?>
                    <div class="d-inline-block w-auto px-2">
                        <a href="https://discord.gg/4KHwZhn" target="_blank" title="Join the Mythic Gaming Discord"><i class="fab fa-discord"></i></a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="footer__disclaimer">
            Magic: The Gathering, its logo, the planeswalker symbol, the <span
                    class="cas-mana-symbol cas-mana-symbol--inline mana medium sg "></span>
            <span class="cas-mana-symbol cas-mana-symbol--inline mana medium su "></span>
            <span class="cas-mana-symbol cas-mana-symbol--inline mana medium sr "></span>
            <span class="cas-mana-symbol cas-mana-symbol--inline mana medium sw "></span>
            <span class="cas-mana-symbol cas-mana-symbol--inline mana medium sb "></span>
            symbols, the pentagon of colors, and all characters’ names and distinctive likenesses are property of Wizards of the Coast LLC in the
            USA and other countries. All Rights Reserved.
        </div>

        <div class="footer__copyright">© 2021 Sleeve Alters LLC - Alter Sleeves (Created using US Patent 1065530 )<br>
            All Rights Reserved
        </div>
    </div>
</footer>
