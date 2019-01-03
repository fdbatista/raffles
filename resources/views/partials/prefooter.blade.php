<!-- BEGIN PRE-FOOTER -->
<div class="pre-footer">
  <div class="container">
    <div class="row">
      <!-- BEGIN BOTTOM ABOUT BLOCK -->
      <div class="col-md-4 col-sm-12 pre-footer-col">
        <h2>About us</h2>
        <?php
            $config = App\Models\AppConfig::find(1);
        ?>
        <p>
            <?= nl2br($config->about_us) ?>
        </p>
      </div>
      <!-- END BOTTOM ABOUT BLOCK -->
      <!-- BEGIN BOTTOM INFO BLOCK -->
      <div class="col-md-4 col-sm-12 pre-footer-col">
        <!--<h2>Information</h2>
        <ul class="list-unstyled">
          <li><i class="fa fa-angle-right"></i> <a href="#">Delivery Information</a></li>
          <li><i class="fa fa-angle-right"></i> <a href="#">Customer Service</a></li>
          <li><i class="fa fa-angle-right"></i> <a href="#">Order Tracking</a></li>
          <li><i class="fa fa-angle-right"></i> <a href="#">Shipping & Returns</a></li>
          <li><i class="fa fa-angle-right"></i> <a href="contacts.html">Contact Us</a></li>
          <li><i class="fa fa-angle-right"></i> <a href="#">Careers</a></li>
          <li><i class="fa fa-angle-right"></i> <a href="#">Payment Methods</a></li>
        </ul>-->
      </div>
      <!-- END INFO BLOCK -->          
      <!-- BEGIN BOTTOM CONTACTS -->
      <div class="col-md-4 col-sm-12 pre-footer-col">
        <h2>Our Contacts</h2>
        <address class="margin-bottom-40">
          <?= nl2br($config->contact_us) ?>
        </address>
      </div>
      <!-- END BOTTOM CONTACTS -->
    </div>
    <hr>
  </div>
</div>
<!-- END PRE-FOOTER -->