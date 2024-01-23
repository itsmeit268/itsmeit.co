/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * SolidWP dependencies
 */
import { FiltersGroupDropdown } from '@ithemes/ui';

/**
 * Internal dependencies
 */
import {
	EditingModalActionFill,
	EditingModalActionButton,
	UserSecurityFilterFill,
} from '@ithemes/security.pages.user-security';
import './style.scss';

export default function App() {
	return (
		<>
			<EditingModalActionFill>
				<EditingModalActionButton
					title={ __( 'Remind Users to Set Up Two-Factor Authentication', 'it-l10n-ithemes-security-pro' ) }
					description={ __( 'Send a reminder by email to prompt users to set up Two-Factor Authentication for increased login security.', 'it-l10n-ithemes-security-pro' ) }
					buttonText={ __( 'Send a Two-Factor Reminder Email', 'it-l10n-ithemes-security-pro' ) }
					slug="send-2fa-reminder"
					confirmationText={ __( 'Sending Two-Factor Reminder', 'it-l10n-ithemes-security-pro' ) }
				/>
			</EditingModalActionFill>
			<UserSecurityFilterFill>
				<FiltersGroupDropdown
					slug="two_factor"
					title={ __( 'Two Factor Authentication', 'it-l10n-ithemes-security-pro' ) }
					options={ [
						{ value: 'enabled', label: __( 'Has Enabled', 'it-l10n-ithemes-security-pro' ) },
						{ value: 'disabled', label: __( 'Does Not Have Enabled', 'it-l10n-ithemes-security-pro' ) },
					] }
				/>
			</UserSecurityFilterFill>
		</>
	);
}
