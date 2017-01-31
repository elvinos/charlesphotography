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

                <div class="col-sm-6" id= "rightFoot"><span class="footerFont">Site by <a href="http://www.elvinos.wordpress.com">Elvinos Creations</a></span></div>
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
            <h4 class="modal-title">Please Donate To Our Charity <a href="http://www.parityfordisability.org.uk/">Parity For Disability</a></h4>
          </div>
          <div class="modal-body">
            <h3>Online</h3>

            <span>
              <a href="https://mydonate.bt.com/charities/parityfordisability">Click here to make a secure donation via our BT MyDonate page</a><br></span>

            <h3>By text</h3>

            <span>Donate by text using our unique code! Text PRTY21 plus the amount to 70070.<br>

            For example, PRTY21 £10</span>
            <h3>Or donate by bank transfer</h2>

  <span>No fees will be taken out of a bank transfer donation. Parity for Disability's bank details are:
  <br>
  CAF Bank Ltd<br>
  Account no. 00012044<br>
  Sort Code 40 52 40</span>



  <h3>Or by post</h3>

  <span>You can post your donation to:<br>

  94 Whetstone Road, <br>
  Cove, <br>
  Farnborough, <br>
  Hants, GU14 9SX<br>

  Cheques should be made out to "Parity for Disability".<br></span>




          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</footer>
