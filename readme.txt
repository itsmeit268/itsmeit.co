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


find /var/www/vhosts/itsmeit.co/httpdocs/wp-content/uploads/pmpro-register-helper/* -type f   \( -regex '.*-[0-9]+x[0-9]+\.jpeg' -o -regex '.*-[0-9]+x[0-9]+\.jpeg.webp'   -o -regex '.*-[0-9]+x[0-9]+\.jpg' -o -regex '.*-[0-9]+x[0-9]+\.jpg.webp'   -o -regex '.*-[0-9]+x[0-9]+\.png' -o -regex '.*-[0-9]+x[0-9]+\.png.webp'   -o -regex '.*-[0-9]+x[0-9]+\.webp' -o -regex '.*-[0-9]+x[0-9]+\.avif'   -o -regex '.*-[0-9]+x[0-9]+\.avif.webp' \)   ! -name "*-150x150.jpeg" ! -name "*-150x150.jpeg.webp"   ! -name "*-150x150.jpg" ! -name "*-150x150.jpg.webp"   ! -name "*-150x150.png" ! -name "*-150x150.png.webp" -exec rm {} +
