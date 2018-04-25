<?php $settings = get_option( 'wll_settings' ); ?>

<?php if( isset( $settings['record_ip_address'] ) && $settings['record_ip_address'] == 1 ){ $checked = 1; } else { $checked = 0; } ?>
<tr>
	<th><?php _e('Record user\'s IP address when logging in', 'when-last-login'); ?></th>
	<td><input type='checkbox' value='1' name='wll_record_user_ip_address' <?php checked( 1, $checked ); ?>/></td>
</tr>


<?php if( isset( $settings['show_all_login_records'] ) && $settings['show_all_login_records'] == 1 ){ $checked = 1; } else { $checked = 0; } ?>
<tr>
	<th><?php _e('Enable "All Login Records" log', 'when-last-login'); ?></th>
	<td><input type='checkbox' value='1' name='wll_all_login_records' <?php checked( 1, $checked ); ?>/></td>
</tr>

<?php if( isset( $settings['gdpr_consent'] ) && $settings['gdpr_consent'] == 1 ){ $checked = 1; } else { $checked = 0; } ?>
<tr>
	<th><?php _e('Enable consent for login tracking', 'when-last-login'); ?></th>
	<td><input type='checkbox' value='1' name='wll_gdpr_consent' <?php checked( 1, $checked ); ?>/></td>
</tr>

<tr>
	<th><?php _e( 'Registration consent text.', 'when-last-login'); ?></th>
	<td><textarea name='wll_registration_consent_text' rows="4" cols="50"><?php echo $settings['registration_consent_text']; ?></textarea></td>
</tr>

<tr>
    <th><input type="submit" name="wll_save_settings"  class="button-primary" value="<?php _e('Save Settings', 'when-last-login'); ?>" /></th>
    <td></td>
</tr>