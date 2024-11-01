<?php
$settings_key = $WebWeb_WP_NotesRemover_obj->get('plugin_settings_key');
$opts = $WebWeb_WP_NotesRemover_obj->get_options();
$plugin_file = dirname(__FILE__) . '/wp-notes-remover.php';

?>
    <div class="wrap">
        <h2>WP Notes Remover</h2>
        
        <?php if (!empty($_REQUEST['settings-updated'])) : ?>
            <div class="updated"><p><strong>Settings saved.</strong></p></div>
        <?php endif; ?>

        <div id="poststuff">

            <div id="post-body" class="metabox-holder columns-2">
                <!--<div class="submit" style="display: inline;border: 1px solid red;">
                                    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
                                </div>-->
                <!-- main content -->
                <div id="post-body-content">

                    <div class="meta-box-sortables ui-sortable">

                        <div class="postbox">
                            <div class="inside">
                                WP Notes Remover plugin removes unnesessary technical information notes e.g. below the WordPress comments, comments are closed etc.

                                <div class="message">
                                    <p><?php echo $WebWeb_WP_NotesRemover_obj->msg("The plugin is active and running.
                                        No additional action is required on your part.", 1); ?>

                                        <br/>
                                        The following css are targeted and hid: .form-allowed-tags, .nocomments, .nocomments2
                                    </p>
                                </div>
                            </div> <!-- .inside -->

                        </div> <!-- .postbox -->

                        <div class="postbox">

                            <h3><span>Tell Your Friends</span></h3>
                            <div class="inside">
                                <?php
                                    $plugin_data = get_plugin_data($plugin_file);

                                    $app_link = urlencode($plugin_data['PluginURI']);
                                    $app_title = urlencode($plugin_data['Name']);
                                    $app_descr = urlencode($plugin_data['Description']);
                                ?>
                                <p>
                                    <!-- AddThis Button BEGIN -->
                                    <div class="addthis_toolbox addthis_default_style addthis_32x32_style">
                                        <a class="addthis_button_facebook" addthis:url="<?php echo $app_link?>" addthis:title="<?php echo $app_title?>" addthis:description="<?php echo $app_descr?>"></a>
                                        <a class="addthis_button_twitter" addthis:url="<?php echo $app_link?>" addthis:title="<?php echo $app_title?>" addthis:description="<?php echo $app_descr?>"></a>
                                        <!--<a class="addthis_button_google_plusone" g:plusone:count="false" addthis:url="<?php //echo $app_link?>" addthis:title="<?php //echo $app_title?>" addthis:description="<?php //echo $app_descr?>"></a>-->
                                        <a class="addthis_button_linkedin" addthis:url="<?php echo $app_link?>" addthis:title="<?php echo $app_title?>" addthis:description="<?php echo $app_descr?>"></a>
                                        <a class="addthis_button_email" addthis:url="<?php echo $app_link?>" addthis:title="<?php echo $app_title?>" addthis:description="<?php echo $app_descr?>"></a>
                                        <a class="addthis_button_myspace" addthis:url="<?php echo $app_link?>" addthis:title="<?php echo $app_title?>" addthis:description="<?php echo $app_descr?>"></a>
                                        <a class="addthis_button_google" addthis:url="<?php echo $app_link?>" addthis:title="<?php echo $app_title?>" addthis:description="<?php echo $app_descr?>"></a>
                                        <a class="addthis_button_digg" addthis:url="<?php echo $app_link?>" addthis:title="<?php echo $app_title?>" addthis:description="<?php echo $app_descr?>"></a>
                                        <a class="addthis_button_delicious" addthis:url="<?php echo $app_link?>" addthis:title="<?php echo $app_title?>" addthis:description="<?php echo $app_descr?>"></a>
                                        <a class="addthis_button_stumbleupon" addthis:url="<?php echo $app_link?>" addthis:title="<?php echo $app_title?>" addthis:description="<?php echo $app_descr?>"></a>
                                        <a class="addthis_button_tumblr" addthis:url="<?php echo $app_link?>" addthis:title="<?php echo $app_title?>" addthis:description="<?php echo $app_descr?>"></a>
                                        <a class="addthis_button_favorites" addthis:url="<?php echo $app_link?>" addthis:title="<?php echo $app_title?>" addthis:description="<?php echo $app_descr?>"></a>
                                        <a class="addthis_button_compact"></a>
                                    </div>
                                    <!-- The JS code is in the footer -->

                                    <script type="text/javascript">
                                    var addthis_config = {"data_track_clickback":true};
                                    var addthis_share = {
                                      templates: { twitter: 'Check out {{title}} at {{lurl}}' }
                                    }
                                    </script>
                                    <!-- AddThis Button START part2 -->
                                    <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js"></script>
                                    <!-- AddThis Button END part2 -->
                                </p>
                            </div> <!-- .inside -->

                        </div> <!-- .postbox -->

                        <div class="postbox">
                            <h3><span>Curious about our other products?</span></h3>
                            <div class="inside">
                                Visit <a href="http://orbisius.com/products/" target="_blank">http://orbisius.com/products/</a>
                            </div> <!-- .inside -->
                        </div> <!-- .postbox -->
                    </div> <!-- .meta-box-sortables .ui-sortable -->

                </div> <!-- post-body-content -->

                <!-- sidebar -->
                <div id="postbox-container-1" class="postbox-container">

                    <div class="meta-box-sortables">
						<!-- Hire Us -->
                        <div class="postbox">
                            <h3><span>Hire Us</span></h3>
                            <div class="inside">
                                Hire us to create a plugin/web/mobile app
                                <br/><a href="http://orbisius.com/page/free-quote/?utm_source=<?php echo str_replace('.php', '', basename($plugin_file));?>&utm_medium=plugin-settings&utm_campaign=product"
                                   title="If you want a custom web/mobile app/plugin developed contact us. This opens in a new window/tab"
                                    class="button-primary" target="_blank">Get a Free Quote</a>
                            </div> <!-- .inside -->
                        </div> <!-- .postbox -->
                        <!-- /Hire Us -->

                        <!-- Newsletter-->
                        <div class="postbox">
                            <h3><span>Newsletter</span></h3>
                            <div class="inside">
                                <!-- Begin MailChimp Signup Form -->
                                <div id="mc_embed_signup">
                                    <?php
                                        $current_user = wp_get_current_user();
                                        $email = empty($current_user->user_email) ? '' : $current_user->user_email;
                                    ?>

                                    <form action="http://WebWeb.us2.list-manage.com/subscribe/post?u=005070a78d0e52a7b567e96df&amp;id=1b83cd2093" method="post"
                                          id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank">
                                        <input type="hidden" value="settings" name="SRC2" />
                                        <input type="hidden" value="<?php echo str_replace('.php', '', basename($plugin_file));?>" name="SRC" />

                                        <span>Get notified about cool plugins we release</span>
                                        <!--<div class="indicates-required"><span class="app_asterisk">*</span> indicates required
                                        </div>-->
                                        <div class="mc-field-group">
                                            <label for="mce-EMAIL">Email</label>
                                            <input type="email" value="<?php echo esc_attr($email); ?>" name="EMAIL" class="required email" id="mce-EMAIL">
                                        </div>
                                        <div id="mce-responses" class="clear">
                                            <div class="response" id="mce-error-response" style="display:none"></div>
                                            <div class="response" id="mce-success-response" style="display:none"></div>
                                        </div>	<div class="clear"><input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button-primary"></div>
                                    </form>
                                </div>
                                <!--End mc_embed_signup-->
                            </div> <!-- .inside -->
                        </div> <!-- .postbox -->
                        <!-- /Newsletter-->

                        <!-- support options -->
                        <div class="postbox">
                            <div class="inside">
                                <!-- Twitter: code -->
                                <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="http://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
                                <!-- /Twitter: code -->

                                <!-- Twitter: Orbisius_Follow:js -->
                                    <a href="https://twitter.com/orbisius" class="twitter-follow-button"
                                       data-align="right" data-show-count="false">Follow @orbisius</a>
                                <!-- /Twitter: Orbisius_Follow:js -->

                                &nbsp;

                                <?php
                                $plugin_data = get_plugin_data($plugin_file);
                                $descr = $plugin_data['Description'];
                                $descr = strlen($descr) > 50 ? substr($descr, 0, 50) . '...' : $descr;
                                ?>
                                <!-- Twitter: Tweet:js -->
                                <a href="https://twitter.com/share" class="twitter-share-button"
                                   data-lang="en" data-text="Checkout <?php echo $plugin_data['Name'];?> #WordPress #plugin <?php echo esc_attr($descr);?>"
                                   data-count="none" data-via="orbisius" data-related="orbisius,qsandbox"
                                   data-url="<?php echo $plugin_data['PluginURI'];?>">Tweet</a>
                                <!-- /Twitter: Tweet:js -->

                                <?php

                                $plugin_slug = str_replace('.php', '', basename($plugin_file));

                                ?>
                                <br/>
                                <span>
                                    <a target="_blank" title="[new window]" href="<?php
                                    $plugin_data = get_plugin_data($plugin_file);
                                    echo $plugin_data['PluginURI'];
                                    ?>">Product Page</a>
                                    |
                                    <a href="http://club.orbisius.com/forums/forum/community-support-forum/wordpress-plugins/<?php echo $plugin_slug; ?>/?utm_source=<?php
                                        echo $plugin_slug;?>&utm_medium=plugin-settings&utm_campaign=product"
                                    target="_blank" title="[new window]">Forums</a>
                                    |
                                    More <a href="http://club.orbisius.com/products//wordpress-plugins/<?php echo $plugin_slug; ?>/?utm_source=<?php
                                        echo $plugin_slug; ?>&utm_medium=plugin-settings-support&utm_campaign=product"
                                    target="_blank" title="[new window]">Products</a>

                                    <!--|
                                     <a href="http://docs.google.com/viewer?url=https%3A%2F%2Fdl.dropboxusercontent.com%2Fs%2Fwz83vm9841lz3o9%2FOrbisius_LikeGate_Documentation.pdf" target="_blank">Documentation</a>
                                    -->
                                </span>
                            </div>
                        </div> <!-- .postbox --> <!-- /support options -->

                        <div class="postbox"> <!-- quick-contact -->
                            <?php
                            $current_user = wp_get_current_user();
                            $email = empty($current_user->user_email) ? '' : $current_user->user_email;
                            $quick_form_action = is_ssl()
                                    ? 'https://ssl.orbisius.com/apps/quick-contact/'
                                    : 'http://apps.orbisius.com/quick-contact/';

                            if (!empty($_SERVER['DEV_ENV'])) {
                                $quick_form_action = 'http://localhost/projects/quick-contact/';
                            }
                            ?>
                            <h3><span>Quick Question or Suggestion</span></h3>
                            <div class="inside">
                                <div>
                                    <form method="post" action="<?php echo $quick_form_action; ?>" target="_blank">
                                        <?php
                                            global $wp_version;
                                            $plugin_data = get_plugin_data($plugin_file);

                                            $hidden_data = array(
                                                'site_url' => site_url(),
                                                'wp_ver' => $wp_version,
                                                'first_name' => $current_user->first_name,
                                                'last_name' => $current_user->last_name,
                                                'product_name' => $plugin_data['Name'],
                                                'product_ver' => $plugin_data['Version'],
                                                'woocommerce_ver' => defined('WOOCOMMERCE_VERSION') ? WOOCOMMERCE_VERSION : 'n/a',
                                            );
                                            $hid_data = http_build_query($hidden_data);
                                            echo "<input type='hidden' name='data[sys_info]' value='$hid_data' />\n";
                                        ?>
                                        <textarea class="widefat" id='orbisius_woocommerce_ext_quick_order_msg' name='data[msg]' required="required"></textarea>
                                        <br/>Your Email: <input type="text" class=""
                                               name='data[sender_email]' placeholder="Email" required="required"
                                               value="<?php echo esc_attr($email); ?>"
                                               />
                                        <br/><input type="submit" class="button-primary" value="<?php _e('Send Feedback') ?>"
                                                    onclick="try { if (jQuery('#orbisius_woocommerce_ext_quick_order_msg').val().trim() == '') { alert('Enter your message.'); jQuery('#orbisius_woocommerce_ext_quick_order_msg').focus(); return false; } } catch(e) {};" />
                                        <br/>
                                        What data will be sent
                                        <a href='javascript:void(0);'
                                            onclick='jQuery(".orbisius_woocommerce_ext_quick_order_data_to_be_sent").toggle();'>(show/hide)</a>
                                        <div class="hide-if-js hide orbisius_woocommerce_ext_quick_order_data_to_be_sent">
                                            <textarea class="widefat" rows="4" readonly="readonly" disabled="disabled"><?php
                                            foreach ($hidden_data as $key => $val) {
                                                if (is_array($val)) {
                                                    $val = var_export($val, 1);
                                                }

                                                echo "$key: $val\n";
                                            }
                                            ?></textarea>
                                        </div>
                                    </form>
                                </div>
                            </div> <!-- .inside -->

                        </div> <!-- .postbox --> <!-- /quick-contact -->
                    </div> <!-- .meta-box-sortables -->

                </div> <!-- #postbox-container-1 .postbox-container -->

            </div> <!-- #post-body .metabox-holder .columns-2 -->

            <br class="clear">
        </div> <!-- #poststuff -->
</div> <!-- /wrap -->
