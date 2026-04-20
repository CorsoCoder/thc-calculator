( function( blocks, element, blockEditor, components, i18n, serverSideRender ) {
	const el = element.createElement;
	const InspectorControls = blockEditor.InspectorControls;
	const PanelBody = components.PanelBody;
	const TextControl = components.TextControl;
	const RangeControl = components.RangeControl;
	const ColorPicker = components.ColorPicker;
	const BaseControl = components.BaseControl;
	const __ = i18n.__;

	const colorControl = ( label, value, onChange ) =>
		el(
			BaseControl,
			{ label: label },
			el( ColorPicker, {
				color: value,
				onChangeComplete: function( color ) {
					onChange( color && color.hex ? color.hex : value );
				},
				disableAlpha: true,
			} )
		);

	blocks.registerBlockType( 'thc-detox/calculator', {
		edit: function( props ) {
			const attributes = props.attributes;
			const setAttributes = props.setAttributes;

			return el(
				element.Fragment,
				{},
				el(
					InspectorControls,
					{},
					el(
						PanelBody,
						{ title: __( 'Textos', 'thc-detox-calculator' ), initialOpen: true },
						el( TextControl, {
							label: __( 'Título', 'thc-detox-calculator' ),
							value: attributes.title,
							onChange: function( value ) {
								setAttributes( { title: value } );
							},
						} ),
						el( TextControl, {
							label: __( 'Descripción', 'thc-detox-calculator' ),
							value: attributes.description,
							onChange: function( value ) {
								setAttributes( { description: value } );
							},
						} ),
						el( TextControl, {
							label: __( 'Disclaimer', 'thc-detox-calculator' ),
							value: attributes.disclaimer,
							onChange: function( value ) {
								setAttributes( { disclaimer: value } );
							},
						} ),
						el( TextControl, {
							label: __( 'Texto botón Anterior', 'thc-detox-calculator' ),
							value: attributes.previousButtonLabel,
							onChange: function( value ) {
								setAttributes( { previousButtonLabel: value } );
							},
						} ),
						el( TextControl, {
							label: __( 'Texto botón Siguiente', 'thc-detox-calculator' ),
							value: attributes.nextButtonLabel,
							onChange: function( value ) {
								setAttributes( { nextButtonLabel: value } );
							},
						} ),
						el( TextControl, {
							label: __( 'Texto botón Calcular', 'thc-detox-calculator' ),
							value: attributes.submitButtonLabel,
							onChange: function( value ) {
								setAttributes( { submitButtonLabel: value } );
							},
						} ),
						el( TextControl, {
							label: __( 'Texto cargando', 'thc-detox-calculator' ),
							value: attributes.submitLoadingLabel,
							onChange: function( value ) {
								setAttributes( { submitLoadingLabel: value } );
							},
						} )
					),
					el(
						PanelBody,
						{ title: __( 'Estilos', 'thc-detox-calculator' ), initialOpen: false },
						el( RangeControl, {
							label: __( 'Ancho máximo (px)', 'thc-detox-calculator' ),
							value: attributes.maxWidth,
							onChange: function( value ) {
								setAttributes( { maxWidth: value } );
							},
							min: 480,
							max: 1400,
						} ),
						el( RangeControl, {
							label: __( 'Tamaño de fuente base (rem)', 'thc-detox-calculator' ),
							value: attributes.fontSize,
							onChange: function( value ) {
								setAttributes( { fontSize: value } );
							},
							min: 0.8,
							max: 1.4,
							step: 0.05,
						} ),
						el( RangeControl, {
							label: __( 'Radio de bordes (px)', 'thc-detox-calculator' ),
							value: attributes.borderRadius,
							onChange: function( value ) {
								setAttributes( { borderRadius: value } );
							},
							min: 0,
							max: 48,
						} ),
						el( RangeControl, {
							label: __( 'Grosor de borde (px)', 'thc-detox-calculator' ),
							value: attributes.borderWidth,
							onChange: function( value ) {
								setAttributes( { borderWidth: value } );
							},
							min: 0,
							max: 12,
						} ),
						colorControl( __( 'Color de fondo', 'thc-detox-calculator' ), attributes.backgroundColor, function( value ) {
							setAttributes( { backgroundColor: value } );
						} ),
						colorControl( __( 'Color de texto', 'thc-detox-calculator' ), attributes.textColor, function( value ) {
							setAttributes( { textColor: value } );
						} ),
						colorControl( __( 'Color principal', 'thc-detox-calculator' ), attributes.primaryColor, function( value ) {
							setAttributes( { primaryColor: value } );
						} ),
						colorControl( __( 'Color acento', 'thc-detox-calculator' ), attributes.accentColor, function( value ) {
							setAttributes( { accentColor: value } );
						} ),
						colorControl( __( 'Color de borde', 'thc-detox-calculator' ), attributes.borderColor, function( value ) {
							setAttributes( { borderColor: value } );
						} )
					)
				),
				el( 'div', { className: props.className }, el( serverSideRender, { block: 'thc-detox/calculator', attributes: attributes } ) )
			);
		},
		save: function() {
			return null;
		},
	} );
} )(
	window.wp.blocks,
	window.wp.element,
	window.wp.blockEditor,
	window.wp.components,
	window.wp.i18n,
	window.wp.serverSideRender
);
