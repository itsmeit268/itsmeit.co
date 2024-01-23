/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { external as linkIcon } from '@wordpress/icons';
import { useSelect } from '@wordpress/data';

/**
 * SolidWP dependencies
 */
import { Button, Notice, Text } from '@ithemes/ui';

/**
 * Internal dependencies
 */
import { BeforeHeaderFill } from '@ithemes/security.pages.vulnerabilities';
import { coreStore } from '@ithemes/security.packages.data';

export default function App() {
	const { installType } = useSelect(
		( select ) => ( {
			installType: select( coreStore ).getInstallType(),
		} ),
		[]
	);

	if ( installType !== 'free' ) {
		return null;
	}
	return (
		<BeforeHeaderFill>
			<Notice
				text={
					<Text
						text={ __( 'Pro users receive early protection and alerts for vulnerabilities.', 'it-l10n-ithemes-security-pro' ) }
					>
						<Button
							icon={ linkIcon }
							iconPosition="right"
							text={ __( 'Get early protection', 'it-l10n-ithemes-security-pro' ) }
							variant="link"
							target="_blank"
							href="https://go.solidwp.com/basic-to-pro"
						/>
					</Text>
				}
				badge={ __( 'Why go Pro?', 'it-l10n-ithemes-security-pro' ) }
			/>
		</BeforeHeaderFill>
	);
}
