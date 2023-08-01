<?php
defined('C5_EXECUTE') or die(_("Access Denied"));
// ANZGO-3727 Modified by Maryjes Tanada 2018-06-05 Referrer-Policy
Header('Referrer-Policy: no-referrer');

$af = Loader::helper('form/go_contact_us_attribute', 'go_contents');
$countriesHelper = Loader::helper('lists/countries', 'go_contents');
$countries = $countriesHelper->getCountries();

// Since we are using preset attributes and we need them in a certain order for this page,
// we will declare arrays of attributes that we need for this form.
// Attributes we need from uContactDetails set.
$ucd = array('uFirstName', 'uLastName');

// Attributes we need from uTeacherContactDetails set.
$utcd = array('uPositionTitle', 'uSchoolAddress');

// Attributes we need from uLoginDetails set.
$uld = array('uEmail');

// Attributes we need from user_billing set.
$ub = array('billing_phone');
?>
<!-- SB-102 added by mabrigos 20190321 moved header spacer to specific pages -->
<div class="header-spacer">
    &nbsp;
</div>
<?php if ($success) { ?>
<div class="container">
    <div class="row">
        <div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2 col-xs-12">
            <h3>Enquiry sent</h3>
            <p>Thank you for your interest in Cambridge University Press.</p>
            <p>You will be contacted within 48 hours by one of our consultants.</p>
        </div>
    </div>
</div>
<?php } else { ?>
    <div class="container-fluid resources-container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-col">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2
                        col-xs-12">
                            <h1>
                                <svg class="svg-lg">
                                <use xlink:href="#icon-contact"></use>
                                </svg>
                                <span class="svg-text-lg">Contact Us</span>
                            </h1>
                            <p>Phone or fax customer service, or complete the contact form below and an Education
                                Resource Consultant will contact you promptly.
                            </p>
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <h4>
                                        <svg>
                                        <use xlink:href="#icon-phone"></use>
                                        </svg>
                                        <span class="svg-text">Australia</span>
                                    </h4>
                                    <p>Phone <a class="contact-link" href="#">+61 (0)3 8671 1400</a></p>
                                    <p>FreePhone <a class="contact-link" href="#">1800 005 210</a></p>
                                    <p>FreeFax <a class="contact-link" href="#">1800 110 521</a></p>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <h4>
                                        <svg>
                                        <use xlink:href="#icon-phone"></use>
                                        </svg>
                                        <span class="svg-text">New Zealand</span>
                                    </h4>
                                    <p>Phone <a class="contact-link" href="#">+61 (0)3 8671 1400</a></p>
                                    <p>FreePhone <a class="contact-link" href="#">0800 023 520</a></p>
                                    <p>FreeFax <a class="contact-link" href="#">0800 501 016</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    <br />
                </div>
            </div>
        </div>
    </div>
    <br />
    <br />
    <style>
        .form-group-contact {
            margin: 5px;
            padding: 0px !important;

        }
    </style>

    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2 col-xs-12">
                <div class="text-center">
                    <label>Contact Details</label>
                </div>
                <?php // SB-199 modified by jbernardez 20190617 ?>
                <form class="form-horizontal" id="contactUsForm"
                      action="/go/contact/confirmation/" method="post">
                    <input type="hidden" name="oid" value="00D2000000000hs">
                    <input type="hidden"
                           name="retURL"
                           value="<?php echo BASE_URL . $this->url("/go/contact/confirmation"); ?>">
                    <p>* - Required field</p>
                    <div class="form-group form-group-contact row">
                        <div class="col-lg-12">
                            <select class="form-control go-input" name="salutation" no-required>
                                <option>Title</option>
                                <option value="Mr.">Mr.</option>
                                <option value="Ms.">Ms.</option>
                                <option value="Mrs.">Mrs.</option>
                                <option value="Dr.">Dr.</option>
                                <option value="Prof.">Prof.</option>
                                <option value="Father">Father</option>
                                <option value="Brother">Brother</option>
                                <option value="Sister">Sister</option>
                                <option value="Mother">Mother</option>
                            </select>
                        </div>
                    </div>
                    <?php
                    $ucdSet = AttributeSet::getByHandle('uContactDetails');
                    $attrKeys = $ucdSet->getAttributeKeys();
                    foreach ($attrKeys as $ak) {
                        if (in_array($ak->akHandle, $ucd)) {
                            echo $af->display($ak, false, false, 'composer', '', true);
                        }
                    }
                    ?>

                    <?php
                    $utcdSet = AttributeSet::getByHandle('uTeacherContactDetails');
                    $attrKeys = $utcdSet->getAttributeKeys();
                    foreach ($attrKeys as $ak) {
                        if (in_array($ak->akHandle, $utcd)) {
                            echo $af->display($ak, false, false, 'composer', '', true);
                        }
                    }
                    ?>

                    <!-- Hack to forego concrete5 default display of address. Better way to render default address
                    should be done here. -->
                    <div class="form-group form-group-contact row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-col">
                            <textarea class="form-control go-input" placeholder="Address *" rows="2" name="street">
                            </textarea>
                        </div>
                    </div>
                    <div class="form-group form-group-contact row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-col">
                            <input type="text" class="form-control go-input" placeholder="City *" name="city"/>
                        </div>
                    </div>
                    <div class="form-group form-group-contact row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-col">
                            <select class="form-control go-input contact-country-select" name="country"
                                    url="<?php echo $this->url('/go/contact/getStatesProvinces') ?>">
                                <option value="">Country *</option>
                                <?php foreach ($countries as $ck => $cv) { ?>
                                <option value="<?php echo $ck ?>"><?php echo $cv; ?></option>
                                <?php } ?>
                            </select>
                            <input type="hidden" value="<?php echo $this->url('/go/signup/getStatesProvinces') ?>" />
                        </div>
                    </div>
                    <div class="form-group form-group-contact row">
                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7 form-col" id="contact-state-selector">
                            <input type="text"
                                   class="form-control go-input"
                                   placeholder="State/Province *"
                                   name="state"/>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-5 form-col">
                            <input type="text" class="form-control go-input" placeholder="Postcode *" name="zip"/>
                        </div>
                    </div>
                    <div class="form-group form-group-contact row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-col">
                            <input type="text" class="form-control go-input" placeholder="Phone number *" name="phone"/>
                        </div>
                    </div>
                    <div class="form-signup form-group row form-group-contact">
                        <div class="col-lg-12 form-col">
                            <input type="email"
                                   placeholder="Email Address *"
                                   id="email"
                                   name="email"
                                   class="form-control go-input" no-check>
                        </div>
                    </div>
                    <br />
                    <br />
                    <div class="text-center">
                        <label>Enquiry</label>
                    </div>
                    <div class="form-group form-group-contact row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-col">
                            <select class="form-control go-input" name="00N20000000BALL">
                                <option>Subject area</option>
                                <optgroup label="-------------------------"></optgroup>
                                <option value="All">All titles</option>
                                <optgroup label="-------------------------"></optgroup>
                                <optgroup label="Arts">
                                    <option value="Arts: Drama">Drama</option>
                                    <option value="Arts: Media">Media</option>
                                    <option value="Arts: Music">Music</option>
                                    <option value="Arts: Visual Arts">Visual Arts</option>
                                    <option value="Arts: Other">Other</option>
                                </optgroup>
                                <optgroup label="Business / Commerce">
                                    <option value="Bus/Com: Accounting">Accounting</option>
                                    <option value="Bus/Com: Business">Business</option>
                                    <option value="Bus/Com: Commerce">Commerce</option>
                                    <option value="Bus/Com: Economics">Economics</option>
                                    <option value="Bus/Com: Legal">Legal</option>
                                </optgroup>
                                <optgroup label="ELT">
                                    <option value="ELT">All</option>
                                </optgroup>
                                <optgroup label="English">
                                    <option value="English">All</option>
                                </optgroup>
                                <optgroup label="Geography">
                                    <option value="Geography">All</option>
                                </optgroup>
                                <optgroup label="Health &amp; PE">
                                    <option value="Health &amp; PE: Junior">Junior</option>
                                    <option value="Health &amp; PE: Senior">Senior</option>
                                </optgroup>
                                <optgroup label="History">
                                    <option value="History: Junior">Junior</option>
                                    <option value="History Senior">Senior</option>
                                </optgroup>
                                <optgroup label="Hum/HSIE">
                                    <option value="Hum/HSIE: Humanites Jnr">Humanites Jnr</option>
                                    <option value="Hum/HSIE: International">International</option>
                                    <option value="Hum/HSIE: Philosophy">Philosophy</option>
                                    <option value="Hum/HSIE: Politics">Politics</option>
                                    <option value="Hum/HSIE: Religion">Religion</option>
                                    <option value="Special Needs">Special Needs</option>
                                </optgroup>
                                <optgroup label="ICT">
                                    <option value="ICT: Junior">Junior</option>
                                    <option value="ICT: Senior">Senior</option>
                                </optgroup>
                                <optgroup label="Languages">
                                    <option value="Languages: French">French</option>
                                    <option value="Languages: German">German</option>
                                    <option value="Languages: Italian">Italian</option>
                                    <option value="Languages: Latin">Latin</option>
                                    <option value="Languages: Other">Other</option>
                                </optgroup>
                                <optgroup label="Mathematics">
                                    <option value="Maths: Junior">Junior</option>
                                    <option value="Maths: Senior">Senior</option>
                                </optgroup>
                                <optgroup label="Science">
                                    <option value="Science: Biology">Biology</option>
                                    <option value="Science: Chemistry">Chemistry</option>
                                    <option value="Science: Environmental">Environmental</option>
                                    <option value="Science: General">General</option>
                                    <option value="Science: Physics">Physics</option>
                                    <option value="Science: Psychology">Psychology</option>
                                    <option value="Science Junior">Science Junior</option>
                                </optgroup>
                                <optgroup label="Technology">
                                    <option value="Technology: Design">Design</option>
                                    <option value="Technology: Food">Food</option>
                                    <option value="Technology: Other">Other</option>
                                </optgroup>
                                <optgroup label="VET">
                                    <option value="VET: Careers">Careers</option>
                                    <option value="VET: Hospitality">Hospitality</option>
                                    <option value="VET: VCAL">VCAL</option>
                                </optgroup>
                                <optgroup label="-------------------------"></optgroup>
                                <option value="Other">Other titles</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group form-group-contact row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-col">
                            <textarea class="form-control go-input"
                                       placeholder="Your query *"
                                      rows="2"
                                      name="description">
                            </textarea>
                        </div>
                    </div>
                    <div class="form-group form-group-contact row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-col">
                            <select class="form-control go-input" name="00N20000000BALM">
                                <option>Year Level</option>
                                <option value="Lower Primary">Lower Primary</option>
                                <option value="Middle Primary">Middle Primary</option>
                                <option value="Upper Primary">Upper Primary</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                                <option value="9">9</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                                <option value="International Baccalaureate">International Baccalaureate</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <p class="form-control-static" style="font-size: 12px;">
                                Cambridge University Press and its affiliate, Cambridge HOTmaths, may occasionally send
                                you additional product information. Cambridge University Press and Cambridge HOTmaths
                                respect your privacy and will not pass your details on to any third party, in
                                accordance with our privacy policy. This policy also contains information about how to
                                access and seek correction to your personal data, or to complain about a breach of
                                Australian Privacy Principles.
                            </p>
                        </div>
                    </div>
                    <div class="form-group row"  style="margin-bottom: 10px;">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-col">
                            <input type="checkbox" name="emailOptOut" />
                            I would like to receive product information by email
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-col">
                            <!-- ANZGO-3811 added by jbernardez 20180801 -->
                            <input type="checkbox" name="" />
                            <!-- ANZGO-3827 modified by jbernardez 20180813 -->
                            <input type="hidden" name="HardCopyOptOut__c" value="0" />
                            I would like to receive product information by regular post
                        </div>
                    </div>
                     <!-- ANZGO-3812 added by jdchavez 08/01/2018 recaptcha-->
                    <div class="fade in field-remove">
                        <div id="captcha" align="center"></div>
                            <div class="reCaptcha" align="center">
                                <?php // SB-266 modified by machua 20190724 to get the correct site key ?>
                                <div class="g-recaptcha"
                                    data-sitekey=<?php echo RECAPTCHA_CONTACT_US_SITE_KEY; ?>
                                    data-callback="recaptchaCallback2">
                                </div>
                            </div>
                    </div>
                    <div class="form-group form-group-contact row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-col">
                            <!-- ANZGO-3812 modified by jdchavez 08/01/2018 -->
                            <a class="contact-link btn btn-success btn-block btn-lg go-btn contactCaptcha" 
                               id="submit-contact" 
                               onclick="return btnClick();"> Submit </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php }
