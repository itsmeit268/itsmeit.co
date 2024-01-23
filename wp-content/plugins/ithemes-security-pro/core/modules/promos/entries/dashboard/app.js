/**
 * External dependencies
 */
import { ThemeProvider, useTheme } from '@emotion/react';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useMemo } from '@wordpress/element';
import { closeSmall as dismissIcon } from '@wordpress/icons';

/**
 * SolidWP dependencies
 */
import { Text } from '@ithemes/ui';

/**
 * Internal dependencies
 */
import {
	BelowToolbarFill,
	EditCardsFill,
} from '@ithemes/security.dashboard.api';
import {
	useConfigContext,
	PromoCard,
} from '@ithemes/security.dashboard.dashboard';
import { RebrandingLogos } from '@ithemes/security-style-guide';
import { FlexSpacer } from '@ithemes/security-components';
import { useLocalStorage } from '@ithemes/security-hocs';
import {
	StyledBanner,
	StyledBannerButton,
	StyledBannerHeading,
	StyledTextContainer,
	StyledStellarSaleDismiss,
} from './styles';

export default function App() {
	const { installType } = useConfigContext();

	return (
		<>
			<BelowToolbarFill>
				{ ( { page, dashboardId } ) =>
					dashboardId > 0 && page === 'view-dashboard' && (
						<SolidSecurityDashboardBanner installType={ installType } />
					)
				}
			</BelowToolbarFill>
			{ installType === 'free' && (
				<EditCardsFill>
					<PromoCard title={ __( 'Trusted Devices', 'it-l10n-ithemes-security-pro' ) } />
					<PromoCard title={ __( 'Updates Summary', 'it-l10n-ithemes-security-pro' ) } />
					<PromoCard title={ __( 'User Security Profiles', 'it-l10n-ithemes-security-pro' ) } />
				</EditCardsFill>
			) }
		</>
	);
}

const start = Date.UTC( 2023, 6, 24, 8, 0, 0 );
const end = Date.UTC( 2024, 1, 1, 8, 0, 0 );
const now = Date.now();

function SolidSecurityDashboardBanner( { installType } ) {
	const [ isDismissed, setIsDismissed ] = useLocalStorage( 'itsecIsSolid' );
	const baseTheme = useTheme();
	const theme = useMemo( () => ( {
		...baseTheme,
		colors: {
			...baseTheme.colors,
			text: {
				...baseTheme.colors.text,
				white: '#F9FAF9',
			},
		},
	} ), [ baseTheme ] );

	if ( start > now || end < now ) {
		return null;
	}

	if ( isDismissed ) {
		return null;
	}

	return (
		<ThemeProvider theme={ theme }>
			<StyledBanner>
				<RebrandingLogos />
				<StyledTextContainer>
					<StyledBannerHeading
						level={ 2 }
						weight={ 700 }
						variant="dark"
						size="extraLarge"
						text={ __( 'iThemes is now SolidWP', 'it-l10n-ithemes-security-pro' ) }
					/>
					<Text
						size="subtitleSmall"
						variant="dark"
						text={ __( 'We have been working hard for almost a year to bring you incredible new features in the form of our new and improved brand: SolidWP. Discover what’s new!', 'it-l10n-ithemes-security-pro' ) }
					/>
				</StyledTextContainer>
				<FlexSpacer />
				<StyledStellarSaleDismiss
					label={ __( 'Dismiss', 'it-l10n-ithemes-security-pro' ) }
					icon={ dismissIcon }
					onClick={ () => setIsDismissed( true ) }
				/>
				<StyledBannerButton
					href={ installType === 'free'
						? 'https://go.solidwp.com/dashboard-free-ithemes-is-now-solidwp'
						: 'https://go.solidwp.com/dashboard-ithemes-is-now-solidwp'
					}
					weight={ 600 }
				>
					{ __( 'Learn more', 'it-l10n-ithemes-security-pro' ) }
				</StyledBannerButton>

			</StyledBanner>
		</ThemeProvider>
	);
}
