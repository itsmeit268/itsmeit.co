## Fix update
1 - wp-login.php 1103 - .'?itsec-hb-token=admin_ma2405'
2 - Hide Backend - config nginx line 79-81
3 - Fix woocommerce API: verify_payment_methods_dependencies(){}
4 - adjust_canonical_redirect(), new_uri_redirect_and_404();
5 - footerContents() ##comment $this->addContentModal();
6 - --g-color: #0b6cea; line 60 --g-color-90: #0b6ceae6; line 78 foxiz/assets/css/main.css
7 - class-wc-form-handler.php: comment 'account_first_name'   => __( 'First name', 'woocommerce' ), line 286
8 - wp-content/themes/foxiz/templates/ajax.php line 239
9 - Fix header.php vi_VN
10- Fix code wp-content/plugins/paid-memberships-pro/pages/invoice.php
11- Fix code pmpro_account.php
12- Fix $this->file['fullurl'] wp-content/plugins/paid-memberships-pro/classes/class-pmpro-field.php line 896