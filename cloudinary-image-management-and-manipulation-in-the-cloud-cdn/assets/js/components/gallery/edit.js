/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { InspectorControls, MediaPlaceholder } from '@wordpress/block-editor';
import {
	Button,
	ButtonGroup,
	ColorPalette,
	PanelBody,
	RangeControl,
	SelectControl,
	ToggleControl,
} from '@wordpress/components';

/**
 * Internal dependencies
 */
import './gallery.scss';
import Radio from './radio';
import {
	ALLOWED_MEDIA_TYPES,
	ASPECT_RATIOS,
	CAROUSEL_LOCATION,
	CAROUSEL_STYLE,
	COLORS,
	FADE_TRANSITIONS,
	LAYOUT_OPTIONS,
	MEDIA_ICON_SHAPE,
	NAVIGATION,
	NAVIGATION_BUTTON_SHAPE,
	SELECTED_BORDER_POSITION,
	SELECTED_STYLE,
	ZOOM_TRIGGER,
	ZOOM_TYPE,
} from './options';
import { publicIdFromUrl } from './utils';

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
		carouselLocation,
		carouselOffset,
		carouselStyle,
		width,
		height,
		navigationButtonShape,
		selectedStyle,
		selectedBorderPosition,
		selectedBorderWidth,
		mediaIconShape,
	},
} ) => {
	const setAttr = ( attribute, value ) => setAttributes( { [ attribute ]: value } );

	return <div>
		<>
			<div id="cld-gallery"></div>
			<MediaPlaceholder
				labels={ { title: __( 'Cloudinary Gallery', 'cloudinary' ) } }
				icon="format-gallery"
				allowedTypes={ ALLOWED_MEDIA_TYPES }
				multiple
				onSelect={ ( selections ) => {
					const publicIds = selections.map( ( image ) => publicIdFromUrl( image.url ) );

					cloudinary.galleryWidget( {
						container: '#cld-gallery',
						cloudName: CLDN.mloptions.cloud_name,
						mediaAssets: publicIds,
						displayProps: {
							mode: 'classic',
							columns: 1,
							spacing: 15,
						},
						aspectRatio: '3:4',
						transformation: {
							crop: 'fill',
						},
						bgColor: 'transparent',
						carouselLocation: 'left',
						carouselOffset: 20,
						navigation: 'always',
						thumbnailProps: {
							mediaSymbolSize: 42,
							spacing: 20,
							width: 90,
							height: 90,
							navigationFloat: true,
							navigationShape: 'radius',
							navigationSize: 40,
							navigationColor: '#ffffff',
							selectedStyle: 'all',
							selectedBorderPosition: 'all',
							selectedBorderWidth: 4,
							mediaSymbolShape: 'none',
						},
						navigationButtonProps: {
							shape: 'rectangle',
							iconColor: '#ffffff',
							color: '#000',
							size: 52,
							navigationPosition: 'offset',
							navigationOffset: 12,
						},
						themeProps: {
							primary: '#000000',
							active: '#777777',
						},
					} ).render();
				} }
			/>
		</>
		<InspectorControls>
			<PanelBody title={ __( 'Layout', 'cloudinary' ) }>
				{ LAYOUT_OPTIONS.map( ( layoutStyle ) => (
					<Radio
						key={ layoutStyle.name + '-layout' }
						name={ layoutStyle.name }
						onChange={ ( value ) => setAttr( 'layout', value ) }
						icon={ layoutStyle.icon }
						current={ layout }
					>
						{ layoutStyle.label }
					</Radio>
				) ) }
			</PanelBody>
			<PanelBody title={ __( 'Color Palette', 'cloudinary' ) }>
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
			<PanelBody title={ __( 'Main Viewer Parameters', 'cloudinary' ) }>
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
								key={ navType.value + '-navigation' }
								isDefault
								isPrimary={ navType.value === navigation }
								onClick={ () => setAttr( 'navigation', navType.value ) }
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
								{ ZOOM_TYPE.map( ( item ) => (
									<Button
										key={ item.value + '-zoom-type' }
										isDefault
										isPrimary={ item.value === zoomType }
										onClick={ () => setAttr( 'zoomType', item.value ) }
									>
										{ item.label }
									</Button>
								) ) }
							</ButtonGroup>
						</p>
						<p>{ __( 'Zoom Trigger', 'cloudinary' ) }</p>
						<p>
							<ButtonGroup>
								{ ZOOM_TRIGGER.map( ( item ) => (
									<Button
										key={ item.value + '-zoom-trigger' }
										isDefault
										isPrimary={ item.value === zoomTrigger }
										onClick={ () => setAttr( 'zoomTrigger', item.value ) }
									>
										{ item.label }
									</Button>
								) ) }
							</ButtonGroup>
						</p>
					</> }
				</div>
			</PanelBody>
			<PanelBody title={ __( 'Carousel Parameters', 'cloudinary' ) }>
				<p>{ __( 'Carousel Location', 'cloudinary' ) }</p>
				<p>
					<ButtonGroup>
						{ CAROUSEL_LOCATION.map( ( item ) => (
							<Button
								key={ item.value + '-carousel-location' }
								isDefault
								isPrimary={ item.value === carouselLocation }
								onClick={ () => setAttr( 'carouselLocation', item.value ) }
							>
								{ item.label }
							</Button>
						) ) }
					</ButtonGroup>
				</p>
				<RangeControl
					label={ __( 'Carousel Offset', 'cloudinary' ) }
					value={ carouselOffset }
					onChange={ ( offset ) => setAttributes( { carouselOffset: offset } ) }
					min={ 0 }
					max={ 100 }
				/>
				<p>{ __( 'Carousel Style', 'cloudinary' ) }</p>
				<p>
					<ButtonGroup>
						{ CAROUSEL_STYLE.map( ( item ) => (
							<Button
								key={ item.value + '-carousel-style' }
								isDefault
								isPrimary={ item.value === carouselStyle }
								onClick={ () => setAttr( 'carouselStyle', item.value ) }
							>
								{ item.label }
							</Button>
						) ) }
					</ButtonGroup>
				</p>
				<RangeControl
					label={ __( 'Width', 'cloudinary' ) }
					value={ width }
					onChange={ ( newWidth ) => setAttributes( { width: newWidth } ) }
					min={ 0 }
					max={ 300 }
				/>
				<RangeControl
					label={ __( 'Height', 'cloudinary' ) }
					value={ height }
					onChange={ ( newHeight ) => setAttributes( { height: newHeight } ) }
					min={ 0 }
					max={ 300 }
				/>
				<p>{ __( 'Navigation Button Shape', 'cloudinary' ) }</p>
				{ NAVIGATION_BUTTON_SHAPE.map( ( item ) => (
					<Radio
						key={ item.name + '-navigation-button-shape' }
						name={ item.name }
						onChange={ ( value ) => setAttr( 'navigationButtonShape', value ) }
						icon={ item.icon }
						current={ navigationButtonShape }
					>
						{ item.label }
					</Radio>
				) ) }
				<p>{ __( 'Selected Style', 'cloudinary' ) }</p>
				<p>
					<ButtonGroup>
						{ SELECTED_STYLE.map( ( item ) => (
							<Button
								key={ item.value + '-selected-style' }
								isDefault
								isPrimary={ item.value === selectedStyle }
								onClick={ () => setAttr( 'selectedStyle', item.value ) }
							>
								{ item.label }
							</Button>
						) ) }
					</ButtonGroup>
				</p>
				<SelectControl
					label={ __( 'Selected Border Position', 'cloudinary' ) }
					value={ selectedBorderPosition }
					options={ SELECTED_BORDER_POSITION }
					onChange={ ( value ) => setAttributes( { selectedBorderPosition: value } ) }
				/>
				<RangeControl
					label={ __( 'Selected Border Width', 'cloudinary' ) }
					value={ selectedBorderWidth }
					onChange={ ( newBw ) => setAttributes( { selectedBorderWidth: newBw } ) }
					min={ 0 }
					max={ 10 }
				/>
				<p>{ __( 'Media Shape Icon', 'cloudinary' ) }</p>
				{ MEDIA_ICON_SHAPE.map( ( item ) => (
					<Radio
						key={ item.name + '-media' }
						name={ item.name }
						onChange={ ( value ) => setAttr( 'mediaIconShape', value ) }
						icon={ item.icon }
						current={ mediaIconShape }
					>
						{ item.label }
					</Radio>
				) ) }
			</PanelBody>
		</InspectorControls>
	</div>;
};

export default Edit;
