<?php

/**
 * Main plugin class
 * */
class OPS_Email
{

    public static function ops_master_update_email()
    {
        if(OPS_PREMIUM == false){
            return;
        }

        // based on https://litmus.com/builder/fb42d09
        ob_start();

        // get email header
        self::ops_email_header();

        // get email body
        do_action('ops/ops_update_email_body');

        // get email footer
        self::ops_email_footer();

        $message = ob_get_contents();
        ob_end_clean();

        $settings = Off_Page_SEO::ops_get_settings();
        $subject = 'SEO Report - ' . get_bloginfo('name');

        $headers = 'MIME-Version: 1.0' . "\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\n";
        $headers .= "X-Mailer: PHP \n";
        $headers .= 'From: Off Page SEO <noreply@offpageseoplugin.com>' . "\n";


        // customer
        $to = isset($settings['core']['notification_email']) ? $settings['core']['notification_email'] : '';
        $mail = wp_mail($to, $subject, $message, $headers);
        if ($mail != 1) {
            // try to send with php function
            $second_mail = mail($to, $subject, $message, $headers);
            echo "<pre>"; print_r($second_mail); echo "</pre>";
        }
    }


    public static function ops_backlink_was_deleted($blid)
    {
        $backlink = Off_Page_SEO::ops_get_backlink($blid);

        $keyword = Off_Page_SEO::ops_get_keyword($backlink[0]['keyword_id']);

        // based on https://litmus.com/builder/fb42d09
        ob_start();

        // get email header
        self::ops_email_header();
        ?>
        <div class="block">
            <!-- fulltext -->
            <table width="100%" bgcolor="#f1f1f1" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="fulltext">
                <tbody>
                <tr>
                    <td>
                        <table bgcolor="#ffffff" width="680" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth" modulebg="edit">
                            <tbody>
                            <!-- Spacing -->
                            <tr>
                                <td width="100%" height="20"></td>
                            </tr>
                            <!-- Spacing -->
                            <tr>
                                <td>
                                    <table width="640" align="center" cellpadding="0" cellspacing="0" border="0" class="devicewidthinner">
                                        <tbody>
                                        <!-- Title -->
                                        <tr>
                                            <td style="font-family: Helvetica, arial, sans-serif; font-size: 16px; color: #333333; text-align:center;line-height: 20px;" st-title="fulltext-title">
                                                <b><?php _e('The following backlink was not found', 'off-page-seo') ?></b>

                                                <p>
                                                    <a href="<?php echo $keyword['url'] ?>" target="_blank"><?php echo $keyword['url'] ?></a>
                                                </p>

                                                <p style="padding-top: 30px;">
                                                    <?php _e('... on the website:', 'off-page-seo') ?>
                                                </p>

                                                <p>
                                                    <a href="<?php echo $backlink[0]['url'] ?>" target="_blank"><?php echo $backlink[0]['url'] ?></a>
                                                </p>
                                            </td>
                                        </tr>
                                        <!-- End of spacing -->
                                        <!-- content -->
                                        <tr>
                                            <td style="font-family: Helvetica, arial, sans-serif; font-size: 14px; color: #95a5a6; text-align:center;line-height: 30px;" st-content="fulltext-paragraph">

                                            </td>
                                        </tr>
                                        <!-- End of content -->
                                        <!-- Spacing -->
                                        <tr>
                                            <td width="100%" height="20"></td>
                                        </tr>
                                        <!-- Spacing -->
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
            <!-- end of fulltext -->
        </div>
        <?php
        // get email footer
        self::ops_email_footer();


        $message = ob_get_contents();
        ob_end_clean();
//        echo $message;

        $settings = Off_Page_SEO::ops_get_settings();
        $subject = 'Backlink not found - ' . get_bloginfo('name');

        $headers = 'MIME-Version: 1.0' . "\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\n";
        $headers .= "X-Mailer: PHP \n";
        $headers .= 'From: Off Page SEO <noreply@offpageseoplugin.com>' . "\n";

        // customer
        $to = isset($settings['core']['notification_email']) ? $settings['core']['notification_email'] : '';
        $mail = wp_mail($to, $subject, $message, $headers);
        // try to send with php function

        if ($mail != 1) {
            // try to send with php function
            mail($to, $subject, $message, $headers);
        }
    }

    public static function ops_email_header()
    {
        ?>
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php _e('Off Page SEO Rank Report', 'off-page-seo') ?></title>
            <style type="text/css">
                /* Client-specific Styles */
                #outlook a {
                    padding: 0;
                }

                /* Force Outlook to provide a "view in browser" menu link. */
                body {
                    width: 100% !important;
                    -webkit-text-size-adjust: 100%;
                    -ms-text-size-adjust: 100%;
                    margin: 0;
                    padding: 0;
                    font-size: 14px;
                    font-family: sans-serif;
                }

                /* Prevent Webkit and Windows Mobile platforms from changing default font sizes, while not breaking desktop design. */
                .ExternalClass {
                    width: 100%;
                }

                /* Force Hotmail to display emails at full width */
                .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {
                    line-height: 100%;
                }

                /* Force Hotmail to display normal line spacing.  More on that: http://www.emailonacid.com/forum/viewthread/43/ */
                #backgroundTable {
                    margin: 0;
                    padding: 0;
                    width: 100% !important;
                    line-height: 100% !important;
                }

                img {
                    outline: none;
                    text-decoration: none;
                    border: none;
                    -ms-interpolation-mode: bicubic;
                }

                a img {
                    border: none;
                }

                .image_fix {
                    display: block;
                }

                p {
                    margin: 0px 0px !important;
                }

                table td {
                    border-collapse: collapse;
                }

                table {
                    border-collapse: collapse;
                    mso-table-lspace: 0pt;
                    mso-table-rspace: 0pt;
                }

                /*a {color: #e95353;text-decoration: none;text-decoration:none!important;}*/
                /*STYLES*/
                table[class=full] {
                    width: 100%;
                    clear: both;
                }

                table.ops-report td, table.ops-report th {
                    padding: 5px;
                }

                /*################################################*/
                /*IPAD STYLES*/
                /*################################################*/
                @media only screen and (max-width: 640px) {
                    a[href^="tel"], a[href^="sms"] {
                        text-decoration: none;
                        color: #ffffff; /* or whatever your want */
                        pointer-events: none;
                        cursor: default;
                    }

                    .mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
                        text-decoration: default;
                        color: #ffffff !important;
                        pointer-events: auto;
                        cursor: default;
                    }

                    table[class=devicewidth] {
                        width: 640px !important;
                        text-align: center !important;
                    }

                    table[class=devicewidthinner] {
                        width: 620px !important;
                        text-align: center !important;
                    }

                    table[class="sthide"] {
                        display: none !important;
                    }

                    img[class="bigimage"] {
                        width: 620px !important;
                        height: 219px !important;
                    }

                    img[class="col2img"] {
                        width: 620px !important;
                        height: 258px !important;
                    }

                    img[class="image-banner"] {
                        width: 640px !important;
                        height: 106px !important;
                    }

                    td[class="menu"] {
                        text-align: center !important;
                        padding: 0 0 10px 0 !important;
                    }

                    td[class="logo"] {
                        padding: 10px 0 5px 0 !important;
                        margin: 0 auto !important;
                    }

                    img[class="logo"] {
                        padding: 0 !important;
                        margin: 0 auto !important;
                    }

                }

                /*##############################################*/
                /*IPHONE STYLES*/
                /*##############################################*/
                @media only screen and (max-width: 480px) {
                    a[href^="tel"], a[href^="sms"] {
                        text-decoration: none;
                        color: #ffffff; /* or whatever your want */
                        pointer-events: none;
                        cursor: default;
                    }

                    .mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
                        text-decoration: default;
                        color: #ffffff !important;
                        pointer-events: auto;
                        cursor: default;
                    }

                    table[class=devicewidth] {
                        width: 280px !important;
                        text-align: center !important;
                    }

                    table[class=devicewidthinner] {
                        width: 260px !important;
                        text-align: center !important;
                    }

                    table[class="sthide"] {
                        display: none !important;
                    }

                    img[class="bigimage"] {
                        width: 260px !important;
                        height: 136px !important;
                    }

                    img[class="col2img"] {
                        width: 260px !important;
                        height: 160px !important;
                    }

                    img[class="image-banner"] {
                        width: 280px !important;
                        height: 68px !important;
                    }

                }
            </style>


        </head>
        <body>
        <div class="block">
            <!-- Start of preheader -->
            <table width="100%" bgcolor="#f1f1f1" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="preheader">
                <tbody>
                <tr>
                    <td width="100%">
                        <table width="680" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
                            <tbody>
                            <!-- Spacing -->
                            <tr>
                                <td width="100%" height="5"></td>
                            </tr>
                            <!-- Spacing -->
                            <!--                            <tr>-->
                            <!--                                <td align="right" valign="middle" style="font-family: Helvetica, arial, sans-serif; font-size: 10px;color: #999999" st-content="preheader">-->
                            <!--                                    If you cannot read this email, please-->
                            <!--                                    <a href="--><?php //echo get_home_url();
                            ?><!--/wp-admin/admin.php?page=ops" target="_blank" style="text-decoration: none; color: #0db9ea">view-->
                            <!--                                        the results in your dashboard.</a>-->
                            <!--                                </td>-->
                            <!--                            </tr>-->
                            <!-- Spacing -->
                            <tr>
                                <td width="100%" height="5"></td>
                            </tr>
                            <!-- Spacing -->
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
            <!-- End of preheader -->
        </div>
        <div class="block">
            <!-- start of header -->
            <table width="100%" bgcolor="#f1f1f1" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="header">
                <tbody>
                <tr>
                    <td>
                        <table width="680" bgcolor="#0073aa" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth" hlitebg="edit" shadow="edit">
                            <tbody>
                            <tr>
                                <td>
                                    <!-- logo -->
                                    <table width="280" cellpadding="0" cellspacing="0" border="0" align="left" class="devicewidth">
                                        <tbody>
                                        <tr>
                                            <td valign="middle" width="270" style="padding: 15px 0 10px 20px;" class="logo">
                                                <a href="http://www.offpageseoplugin.com/" target="_blank" style="color:#fff; text-decoration: none; line-height: 14px; font-size: 14px; padding-top: 0px;">
                                                    <?php _e('Off Page SEO Plugin', 'off-page-seo') ?>
                                                </a>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <!-- End of logo -->
                                    <!-- menu -->
                                    <table width="380" cellpadding="0" cellspacing="0" border="0" align="right" class="devicewidth">
                                        <tbody>
                                        <tr>
                                            <td width="270" valign="middle" style="font-family: Helvetica, Arial, sans-serif;font-size: 13px; color: #ffffff;line-height: 24px; padding: 10px 0;" align="right" class="menu" st-content="menu">
                                                <a href="<?php echo admin_url(); ?>admin.php?page=ops" style="text-decoration: none; color: #ffffff;"><?php _e('View dashboard', 'off-page-seo') ?></a>
                                            </td>
                                            <td width="20"></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <!-- End of Menu -->
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
            <!-- end of header -->
        </div>
        <?php
    }

    public static function ops_email_footer()
    {
        ?>
        <div class="block">
            <!-- Start of preheader -->
            <table width="100%" bgcolor="#f1f1f1" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="postfooter">
                <tbody>
                <tr>
                    <td width="100%">
                        <table width="680" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
                            <tbody>
                            <!-- Spacing -->
                            <tr>
                                <td width="100%" height="5"></td>
                            </tr>
                            <!-- Spacing -->
                            <tr>
                                <td align="center" valign="middle" style="font-family: Helvetica, arial, sans-serif; font-size: 10px;color: #999999" st-content="preheader">
                                    <?php _e("If you don't want to receive this email, please change your ", "off-page-seo") ?>
                                    <a class="hlite" target="_blank" href="<?php echo admin_url(); ?>admin.php?page=ops_settings" style="text-decoration: none; color: #0073aa">
                                        <?php _e('settings', 'off-page-seo') ?>
                                    </a>.
                                </td>
                            </tr>
                            <!-- Spacing -->
                            <tr>
                                <td width="100%" height="85"></td>
                            </tr>
                            <!-- Spacing -->
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
            <!-- End of preheader -->
        </div>

        </body>
        </html>
        <?php
    }

}
