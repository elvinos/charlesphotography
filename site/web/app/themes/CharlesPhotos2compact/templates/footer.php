<footer class="footer">
    <div class="container-fluid" id="footCont">
            <div class="row">
                <div class="col-sm-4" id= "leftFoot"><span class="footerFont" >Images by Steve, Engineered by Alex. </span></div>

                <div class="col-xs-6 col-sm-2" id="buttonFootLeft"> <button type="button" class="buttonFoot donateButton" data-text="Donate" data-toggle="modal" data-target="#donateForm">
                     Donate
                  </button> </div>
                  <div class="col-xs-6 col-sm-2" id="buttonFootRight"> <button type="button" data-text="Contact" class="buttonFoot " data-toggle="modal" data-target="#contactForm">
                      Contact
                  </button> </div>
                <div class="col-xs-12 col-sm-4" id= "rightFoot"><span class="footerFont" id= "rightFoot">&copy; <?php bloginfo('name'); ?> <?php echo date('Y'); ?></div>
            </div>

            <div class="row">
                <div class="col-sm-6"></div>

                <div class="col-sm-6" id= "rightFoot"><span class="footerFont">Site by <a href="https://www.elvinos.uk">Elvinos</a></span></div>
            </div>

        </div>
  </footer>

  <!--Modal Contact Us Form  -->
  	<div class="modal fade" id="contactForm">
  	  <div class="modal-dialog">
  	    <div class="modal-content">
  	      <div class="modal-header">
  	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  	        <h4 class="modal-title">Contact Us</h4>
  	      </div>
  	      <div class="modal-body">
  	        <?php
  	        	if( function_exists( 'ninja_forms_display_form' ) ){ ninja_forms_display_form( 1 ); }
  	        ?>
  	      </div>
  	      <div class="modal-footer">
  	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
  	      </div>
  	    </div><!-- /.modal-content -->
  	  </div><!-- /.modal-dialog -->
  	</div><!-- /.modal -->

    <div class="modal fade" id="donateForm">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h3 class="modal-title">Like the Site?</h3>
          </div>
          <div class="modal-body">
            <div class="container-fluid">
              <row class="text-center">
              <h3>Make a Small Donation to Keep the Site Going</h3>
              <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
              <input type="hidden" name="cmd" value="_s-xclick">
              <input type="hidden" name="hosted_button_id" value="DAN48ZWQLAQKL">
              <input type="image" src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online!">
              <img alt="" border="0" src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1">
              </form> </div></row>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</footer>
