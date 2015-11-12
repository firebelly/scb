<footer class="site-footer content-info" role="contentinfo">
  <div class="wrap grid">
    <div class="container">
      <?php dynamic_sidebar('sidebar-footer'); ?>
    </div>
    
    <div class="grid-item one-half -left">
      <div class="container">
        <p class="email-link"><a href="mailto:<?= \Firebelly\SiteOptions\get_option( 'contact_email', 'hello@scb.com' ); ?>"><?= \Firebelly\SiteOptions\get_option( 'contact_email', 'hello@scb.com' ); ?></a></p>

        <ul class="social">
          <li><a target="_blank" href="https://www.facebook.com/<?php echo \Firebelly\SiteOptions\get_option( 'facebook_id', 'SolomonCordwellBuenz' ); ?>">Facebook</a></li>
          <li><a target="_blank" href="https://twitter.com/<?php echo \Firebelly\SiteOptions\get_option( 'twitter_id', 'SoloCordBuenz' ); ?>">Twitter</a></li>
          <li><a target="_blank" href="http://linkedin.com/company/<?php echo \Firebelly\SiteOptions\get_option( 'linkedin_id', 'solomon-cordwell-buenz' ); ?>">LinkedIn</a></li>
        </ul>
      </div>
    </div>

    <div class="grid grid-item one-half -right">
      <div class="contact-group grid-item one-third">
        <h3>Chicago</h3>
        <ul>
          <li class="address">
            <address class="vcard"> 
              <span class="street-address">625 N Michigan Ave <span>Suite 800</span></span>
              <span class="locality">Chicago, IL</span>
              <span class="postal-code">60611 USA</span>
            </address>
          </li>
          <li class="contact">
            <span class="tel"><b>T</b> +1 312 896 1100</span>
            <span class="tel"><b>F</b> +1 312 896 1200</span>
          </li>
        </ul>
      </div>

      <div class="contact-group grid-item one-third">
        <h3>San Francisco</h3>
        <ul>
          <li class="address">
            <address class="vcard"> 
              <span class="street-address">255 California Street <span>3rd Floor</span></span>
              <span class="locality">San Francisco, CA</span>
              <span class="postal-code">94111 USA</span>
            </address>
          </li>
          <li class="contact">
            <span class="tel"><b>T</b> +1 415 216 2450</span>
            <span class="tel"><b>F</b> +1 415 216 2451</span>
          </li>
        </ul>
      </div>

      <div class="contact-group grid-item one-third">
        <h3>Media</h3>
        <ul>
          <li class="address">
            <span>Brook Rosini</span>
            <span>Communications</span>
            <span>Specialist</span>
          </li>
          <li class="contact">
            <span class="tel"><b>T</b> +1 312 896 1190</span>
            <span class="tel"><b>F</b> +1 860 318 6161</span>
            <span class="email"><b>E</b> <a href="mailto:brook.rosini@scb.com">brook.rosini@scb.com</a></span>
          </li>
        </ul>
      </div>
    </div>

  </div><!-- /.wrap -->

</footer>
