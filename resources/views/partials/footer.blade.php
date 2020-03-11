<footer class="page-footer grey darken-2">
    <div class="container">
        <div class="row">
            <div class="col l2 s12">
                <p class="white-text" style="font-size: 18px;"><strong>Yoken Online</strong></p>
                <ul>
                    <li><a class="grey-text" href="/about-us">About us</a></li>
                    <li><a class="grey-text" href="/contact-us">Contact us</a></li>
                    <li><a class="grey-text" href="/faqs">FAQs</a></li>
                    <li><a class="grey-text" href="/login">Login</a></li>
                </ul>
            </div>
            <div class="col l2 offset-l1 s12">
                <p class="white-text" style="font-size: 18px;"><strong>Legal</strong></p>
                <ul>
                    <li><a class="grey-text" href="/terms-and-conditions">Terms & Conditions</a></li>
                    <li><a class="grey-text" href="/privacy-policy">Privacy Policy</a></li>
                </ul>
            </div>
            <div class="col l2 offset-l5 s12 center-align">
                <img class="footer-logo responsive-img" src="/img/yoken-logo.png" alt="Yoken Logo" style="width: 150px; filter: grayscale(100%)">
                <br>
                <br>
                <div class="center-align"><i class="grey-text text-lighten-1" style="font-size: 18px;">Happy learning!</i></div>
            </div>
        </div>
    </div>
    <div class="footer-copyright">
        <div class="container">
            © 2017 All rights reserved.
            <span class="grey-text text-lighten-4 right valign-wrapper"><span>Follow us at: &nbsp;&nbsp;</span><a href="https://www.facebook.com/yokenonline/"><img style="width: 25px;
    height: 25px;" src="/img/facebook-circular-logo.svg" alt=""></a></span>
        </div>
    </div>
</footer>
<script src="/js/jquery-3.1.1.min.js"></script>
<script src="/js/materialize.min.js"></script>
<script>
    $(document).ready(function() {
        Materialize.updateTextFields();
        $(".button-collapse").sideNav();
        $(".dropdown-button").dropdown({
            belowOrigin: true,
            hover: true,
            constrainWidth: false,
            alignment: 'right',
        });
        $('.scrollspy').scrollSpy({
            scrollOffset: 90
        });
    });
</script>