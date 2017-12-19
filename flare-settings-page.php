<div class="wrap">
    <?php settings_fields( 'flare-options-settings' ); ?>
    <?php do_settings_sections( 'flare-options-settings' ); ?>
    <div class="metabox-holder">
        <div class="postbox">
            <form method="post" action="options.php">
                <table class="form-table">
                <tr>
                    <th scope="row">Flare URL</th>
                    <td><input type="text" name="flate_setting" value="<?php echo get_option('flare_url');?>" style="width:350px;" /></td>
                </tr>
                <tr>
                    <th scope="row">&nbsp;</th>
                    <td style="padding-top:10px;  padding-bottom:10px;">
                        <input type="submit" name="fs_submit" value="Save changes" class="button-primary" />
                    </td>
                </tr>
                </table>
            </form>
        </div>
    </div>
</div>
<?php
    function your_function() {
        echo "<div style='color: red; font-size: 30px; margin: 20px;'>" . get_option('flare_setting') . "</div>";
    }
    add_action( 'wp_footer', 'your_function', 1 );
?>