/**
 * External dependencies
 */
import styled from '@emotion/styled';
import { useTheme } from '@emotion/react';
import {
	AreaChart,
	Area,
	ResponsiveContainer,
	XAxis,
	YAxis,
	Tooltip,
} from 'recharts';
import { isEmpty } from 'lodash';

/**
 * WordPress dependencies
 */
import { dateI18n } from '@wordpress/date';
import { _n, sprintf } from '@wordpress/i18n';
import { useDispatch, useSelect } from '@wordpress/data';
import { useResizeObserver } from '@wordpress/compose';

/**
 * SolidWP dependencies
 */
import { Surface, SurfaceVariant, Text } from '@ithemes/ui';
import { DateRangeControl } from '@ithemes/security-ui';

/**
 * Internal dependencies
 */
import Header, { Title } from '../../../components/card/header';
import './style.scss';

const StyledTooltip = styled( Surface )`
	display: flex;
	flex-direction: column;
	justify-content: center;
	align-items: center;
	padding: 4px 8px;
	border-radius: 2px;
`;

function TooltipContent( { active, payload } ) {
	if ( ! active || ! payload?.length ) {
		return null;
	}

	const label = sprintf(
		/* translators: 1. Number of attempts. */
		_n( '%d attempt', '%d attempts', payload[ 0 ].value, 'it-l10n-ithemes-security-pro' ),
		payload[ 0 ].value
	);

	return (
		<StyledTooltip variant={ SurfaceVariant.DARK }>
			<Text as="p" text={ label } />
			<Text as="p" text={ payload[ 0 ].payload.name } />
		</StyledTooltip>
	);
}

export default function LineGraph( { card, config } ) {
	const [ resizeListener, size ] = useResizeObserver();
	const { period } = useSelect( ( select ) => ( {
		period: select( 'ithemes-security/dashboard' ).getDashboardCardQueryArgs( card.id )?.period ??
			config.query_args.period?.default,
	} ), [ card.id, config ] );

	const { queryDashboardCard } = useDispatch( 'ithemes-security/dashboard' );

	const theme = useTheme();

	const data = [],
		lines = [];

	const onPeriodChange = ( newPeriod ) => {
		return queryDashboardCard( card.id, { period: newPeriod } );
	};

	if ( ! isEmpty( card.data ) ) {
		for ( const key in card.data ) {
			if ( ! card.data.hasOwnProperty( key ) ) {
				continue;
			}

			for ( let i = 0; i < card.data[ key ].data.length; i++ ) {
				const datum = card.data[ key ].data[ i ];

				if ( data[ i ] ) {
					data[ i ][ key ] = datum.y;
				} else {
					const format = period === '24-hours' ? 'g A' : 'M j';

					data.push( {
						name: datum.t ? dateI18n( format, datum.t ) : datum.x,
						[ key ]: datum.y,
					} );
				}
			}
			lines.push( {
				name: card.data[ key ].label,
				dataKey: key,
			} );
		}
	}

	return (
		<div className="itsec-card--type-line-graph">
			{ resizeListener }
			<Header>
				<Title card={ card } config={ config } />
				<DateRangeControl value={ period } onChange={ onPeriodChange } />
			</Header>
			<ResponsiveContainer width="100%" height="100%">
				<AreaChart
					data={ data }
					margin={
						{ top: 40, left: -15, right: 50, bottom: 10 }
					}
				>
					{ size.width >= 800 ? (
						<XAxis
							interval={ 1 }
							dataKey="name"
							tickLine={ false }
							stroke={ theme.colors.text.muted }
						/>
					) : (
						<XAxis
							ticks={ [
								data[ 0 ]?.name,
								data[ data.length / 2 ]?.name,
								data[ data.length - 1 ].name,
							] }
							dataKey="name"
							tickLine={ false }
							stroke={ theme.colors.text.muted }
						/>
					) }

					<YAxis allowDecimals={ false } tickLine={ false } stroke={ theme.colors.text.muted } />
					<Tooltip content={ <TooltipContent /> } />
					{ lines.map( ( line ) => (
						<Area
							type="linear"
							key={ line.dataKey }
							dataKey={ line.dataKey }
							stroke={ theme.colors.primary.darker20 }
							fill={ theme.colors.tertiary.base }
							isAnimationActive={ false }
							dot={ true }
						/>
					) ) }
				</AreaChart>
			</ResponsiveContainer>
		</div>
	);
}
