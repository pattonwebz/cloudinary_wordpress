/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { InspectorControls, MediaPlaceholder } from '@wordpress/block-editor';
import { Button, ButtonGroup, ColorPalette, PanelBody, SelectControl, ToggleControl } from '@wordpress/components';

/**
 * Internal dependencies
 */
import './gallery.scss';
import Radio from './radio';
import {
	ALLOWED_MEDIA_TYPES,
	ASPECT_RATIOS,
	COLORS,
	FADE_TRANSITIONS,
	LAYOUT_OPTIONS,
	NAVIGATION,
	ZOOM_TRIGGER,
	ZOOM_TYPE,
} from './options';

const Edit = ( {
	setAttributes,
	attributes: {
		primaryColor,
		onPrimaryColor,
		activeColor,
		layout,
		fadeTransition,
		aspectRatio,
		navigation,
		showZoom,
		zoomType,
		zoomTrigger,
	},
} ) => {
	const onLayoutChange = ( selectedLayout ) => setAttributes( { layout: selectedLayout } );
	const onNavigationClick = ( selectedNavigation ) => setAttributes( { navigation: selectedNavigation } );
	const onZoomTypeClick = ( selectedType ) => setAttributes( { zoomType: selectedType } );
	const onZoomTriggerClick = ( selectedTriggeer ) => setAttributes( { zoomTrigger: selectedTriggeer } );

	return <div>
		<MediaPlaceholder
			labels={ { title: __( 'Cloudinary Gallery' ) } }
			icon="format-gallery"
			allowedTypes={ ALLOWED_MEDIA_TYPES }
			multiple
			onSelect={ ( selections ) => {
				console.log( selections );
			} }
		/>
		<InspectorControls>
			<PanelBody title={ __( 'Layout' ) }>
				{ LAYOUT_OPTIONS.map( ( layoutStyle ) => (
					<Radio
						key={ layoutStyle.name }
						name={ layoutStyle.name }
						onChange={ onLayoutChange }
						icon={ layoutStyle.icon }
						current={ layout }
					>
						{ layoutStyle.label }
					</Radio>
				) ) }
			</PanelBody>
			<PanelBody title={ __( 'Color Palette' ) }>
				<p>{ __( 'Primary', 'cloudinary' ) }</p>
				<ColorPalette
					colors={ COLORS }
					value={ primaryColor }
					onChange={ ( value ) => setAttributes( { primaryColor: value } ) }
				/>
				<p>{ __( 'On Primary', 'cloudinary' ) }</p>
				<ColorPalette
					colors={ COLORS }
					value={ onPrimaryColor }
					onChange={ ( value ) => setAttributes( { onPrimaryColor: value } ) }
				/>
				<p>{ __( 'Active', 'cloudinary' ) }</p>
				<ColorPalette
					colors={ COLORS }
					value={ activeColor }
					onChange={ ( value ) => setAttributes( { activeColor: value } ) }
				/>
			</PanelBody>
			{ layout === 'classic' && <PanelBody title={ __( 'Fade Transition' ) }>
				<SelectControl
					value={ fadeTransition }
					options={ FADE_TRANSITIONS }
					onChange={ ( value ) => setAttributes( { activeColor: value } ) }
				/>
			</PanelBody> }
			<PanelBody title={ __( 'Main Viewer Parameters' ) }>
				<SelectControl
					label={ __( 'Aspect Ratio', 'cloudinary' ) }
					value={ aspectRatio }
					options={ ASPECT_RATIOS }
					onChange={ ( value ) => setAttributes( { aspectRatio: value } ) }
				/>
				<p>{ __( 'Navigation', 'cloudinary' ) }</p>
				<p>
					<ButtonGroup>
						{ NAVIGATION.map( ( navType ) => (
							<Button
								key={ navType.value }
								isDefault
								isPrimary={ navType.value === navigation }
								onClick={ () => onNavigationClick( navType.value ) }
							>
								{ navType.label }
							</Button>
						) ) }
					</ButtonGroup>
				</p>
				<div style={ { marginTop: '30px' } }>
					<ToggleControl
						label={ __( 'Show Zoom', 'cloudinary' ) }
						checked={ showZoom }
						onChange={ () => setAttributes( { showZoom: ! showZoom } ) }
					/>
					{ showZoom && <>
						<p>{ __( 'Zoom Type', 'cloudinary' ) }</p>
						<p>
							<ButtonGroup>
								{ ZOOM_TYPE.map( ( type ) => (
									<Button
										key={ type.value }
										isDefault
										isPrimary={ type.value === zoomType }
										onClick={ () => onZoomTypeClick( type.value ) }
									>
										{ type.label }
									</Button>
								) ) }
							</ButtonGroup>
						</p>
						<p>{ __( 'Zoom Trigger', 'cloudinary' ) }</p>
						<p>
							<ButtonGroup>
								{ ZOOM_TRIGGER.map( ( trigger ) => (
									<Button
										key={ trigger.value }
										isDefault
										isPrimary={ trigger.value === zoomTrigger }
										onClick={ () => onZoomTriggerClick( trigger.value ) }
									>
										{ trigger.label }
									</Button>
								) ) }
							</ButtonGroup>
						</p>
					</> }
				</div>
			</PanelBody>
		</InspectorControls>
	</div>;
};

export default Edit;
