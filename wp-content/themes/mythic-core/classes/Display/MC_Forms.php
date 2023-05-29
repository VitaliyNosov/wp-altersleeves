<?php

namespace Mythic_Core\Display;

use MC_Ajax_Functions;
use MC_Vars;

/**
 * Class MC_Forms
 *
 * @package Mythic_Core\Display
 */
class MC_Forms {
    
    /**
     * This function renders file input with custom button and label so we can style it and set existing value
     *
     * @param        $name
     * @param string $classes
     * @param string $value
     * @param string $formats
     * @param bool   $echo
     *
     * @return bool|void
     */
    public static function renderFileInput( $name, $classes = '', $value = '', $formats = '.png,.jpg,.jpeg', $echo = true, $default_view = true ) {
        ob_start(); ?>
        <div>
            <input type="file" class="<?php echo $classes ?> form-control <?php echo $default_view?'':'mc_file_input' ?>"
                   accept="<?php echo $formats ?>"
                   name="<?php echo $name ?>" id="mc_<?php echo $name ?>_input" />
            <?php if(!$default_view): ?>
                <button type="button" class="mc_file_input_button">Choose File</button>
                <?php $label_text = !empty( $value ) ? $value : 'No file chosen'; ?>
                <label for="mc_<?php echo $name ?>_input" class="mc_file_input_label"><?php echo $label_text ?></label>
            <?php endif ?>
        </div>
        <?php $output = ob_get_clean();
        
        if( $echo ) {
            echo $output;
            return;
        }
        
        return $output;
    }
    
    /**
     * @param string $id
     * @param array  $fields
     * @param bool   $required
     *
     * @return string
     */
    public static function buildRadioFields( $id = '', $fields = [], $required = true ) : string {
        if( empty( $id ) || empty( $fields ) ) return '';
        $output = '';
        foreach( $fields as $field ) {
            ob_start();
            $field['required'] = $required;
            self::buildRadioField( $id, $field );
            $output .= ob_get_clean();
        }
        echo '<div class="form-check">'.$output.'</div>';
        
        return '';
    }
    
    /**
     * @param string $id
     * @param array  $data
     * @param bool   $render
     *
     * @return string
     */
    public static function buildRadioField( $id = '', $data = [], $render = true ) : string {
        if( empty( $id ) || empty( $data ) ) return '';
        $args                = [];
        $args['radio_id']    = $radio_id = $id;
        $args['radio_value'] = $radio_value = $data['value'];
        if( empty( $radio_id ) || empty( $radio_value ) ) return '';
        $args['radio_checked']     = $data['selected'] ?? 0;
        $args['radio_field_class'] = $data['field_class'] ?? '';
        $args['radio_input_class'] = $data['input_class'] ?? '';
        $args['radio_label_class'] = $data['label_class'] ?? '';
        $args['radio_disabled']    = !empty( $data['disabled'] );
        $args['radio_label']       = $data['label'] ?? '';
        $args['radio_required']    = !empty( $data['required'] );
        
        $output = MC_Template_Parts::formField( 'radio', '', $args );
        if( $render ) echo $output;
        
        return $output;
    }
    
    /**
     * Renders form
     *
     * @param        $classes
     * @param        $fields
     * @param string $submit_text
     * @param array  $hidden_fields
     * @param array  $existing_data
     */
    public static function mcFormRender( $classes, $fields, $submit_text = 'Submit', $hidden_fields = [],
                                         $existing_data = [] ) {
        if( empty( $fields ) ) return;
        
        if( !empty( $classes ) && is_array( $classes ) ) {
            $classes = implode( ' ', $classes );
        } ?>
        <form action="" class="mc-styled-form <?php echo $classes ?>">
            <ul>
                <li class="mc-styled-form-notices"></li>
                <?php static::mcFormRenderFields( $fields, $existing_data ); ?>
                <li class="mc-styled-form-submit">
                    <button type="submit"><?php echo $submit_text ?></button>
                    <?php MC_Render::templatePart( 'loading', 'loader-animation' );
                    static::mcFormRenderHiddenFields( $hidden_fields ); ?>
                </li>
            </ul>
        </form>
    <?php }
    
    /**
     * Renders all fields for form
     *
     * @param       $fields
     * @param array $existing_data
     */
    public static function mcFormRenderFields( $fields, $existing_data = [] ) {
        foreach( $fields as $field ) {
            switch( $field['type'] ) {
                case 'text':
                case 'email':
                case 'number':
                case 'checkbox':
                case 'datepicker':
                    static::mcFormRenderMainField( $field, $existing_data );
                    break;
                case 'password':
                    static::mcFormRenderPasswordField( $field, $existing_data );
                    break;
                case 'select':
                    static::mcFormRenderSelectField( $field, $existing_data );
                    break;
                case 'textarea':
                    static::mcFormRenderTextareaField( $field, $existing_data );
                    break;
                case 'editor':
                    static::mcFormRenderEditorField( $field, $existing_data );
                    break;
                case 'affiliateAutocomplete':
                    static::mcFormRenderAffiliateAutocompleteField( $field );
                    break;
                case 'file':
                    static::mcFormRenderFileField( $field );
                    break;
            }
        }
    }
    
    /**
     * @param       $config
     * @param       $field
     * @param array $existing_data
     *
     * @return array|null
     */
    public static function mcFormAddConfigurationsToField( $config, $field, $existing_data = [] ) {
        extract( $field );
        // Check user permissions
        if(
                empty($type) ||
                !empty($field['permissions']) && !current_user_can($field['permissions']) ||
                $type == 'select' && empty($options)
        ) return null;
    
        if( $type == 'password' ) {
            $value = !empty( $existing_data[ $field['name'] ] ) ? $existing_data[ $field['name'] ] : MC_Vars::generate();
        } else if( $type == 'select' || $type == 'textarea' || $type == 'editor' ) {
            $value = !empty( $existing_data[ $field['name'] ] ) ? $existing_data[ $field['name'] ] : '';
        } else {
            $value = !empty( $existing_data[ $name ] ) ? $existing_data[ $name ] : '';
        }
        
        if( empty( $value ) && !empty( $field['default'] ) ) {
            $value = $field['default'];
        }
        $classes       = '';
        $autocomplete  = '';
        $checked       = '';
        $disabled_attr = '';
        $required_data = '';
        if( !empty( $required ) ) {
            $classes       = 'mc-required';
            $required_data = '<span class="mc-required-field-span">*</span>';
        }
        if( !empty( $disabled ) && ( $field['name'] != 'freeProductsQuantity' || empty( $value ) ) || !empty( $permissions ) && !current_user_can( $permissions ) ) {
            $disabled_attr = 'disabled';
        }
        if( $type == 'datepicker' ) {
            $type         = 'text';
            $classes      .= ' mc-datepicker';
            $autocomplete = 'autocomplete="off"';
            if( !empty( $value ) && is_a( $value, 'DateTime' ) ) {
                $value = $value->format( 'Y-n-j' );
            }
        } else if( $type == 'checkbox' && !empty( $value ) ) {
            $checked = 'checked';
        } else if( $type == 'number' && empty( $value ) && !empty( $required_data ) ) {
            $value = 0;
        }
        $conditional = static::mcRenderConditionalVal( $field );
    
        $id_part = !empty($id_part) ? $id_part : '';
        $label = !empty($label) ? $label : '';
        $value = !empty($value) ? $value : '';
        $options = !empty($options) ? $options : [];
        $showEmpty = $field['empty'] ?? false;
        
        return compact(['conditional', 'id_part', 'label', 'required_data', 'type', 'classes', 'value', 'autocomplete',
                        'checked', 'disabled_attr', 'options', 'showEmpty']);
    }
    
    /**
     * Renders all standard fields
     *
     * @param $field
     * params list:
     * $type
     * $id_part
     * $label
     * $required
     */
    public static function mcFormRenderMainField( $field, $existing_data = [] ) {
        // We will need add a list of options for use it for all input types
        $config = [];
        $field_config = static::mcFormAddConfigurationsToField($config, $field, $existing_data);
        if(empty($field_config)) return;
        
        extract( $field_config ); ?>
        <li <?php echo $conditional ?> class="<?php echo !empty( $conditional ) ? 'mc-conditional-field' : '' ?>">
            <label for="mc-<?php echo $id_part ?>"><?php echo $label.$required_data ?></label>
            <input type="<?php echo $type ?>" name="mc-<?php echo $id_part ?>" id="mc-<?php echo $id_part ?>"
                   class="<?php echo $classes ?>"
                   value="<?php echo $value ?>" <?php echo $autocomplete.' '.$checked.' '.$disabled_attr ?>>
        </li>
    <?php }
    
    /**
     * Renders passwords field
     *
     * @param $field
     * params list:
     * $type
     * $id_part
     * $label
     * $required
     */
    public static function mcFormRenderPasswordField( $field, $existing_data = [] ) {
        // We will need add a list of options for use it for all input types
        $config = [];
        $field_config = static::mcFormAddConfigurationsToField($config, $field, $existing_data);
        if(empty($field_config)) return;
    
        extract( $field_config ); ?>
        <li <?php echo $conditional ?> class="<?php echo !empty( $conditional ) ? 'mc-conditional-field' : '' ?>">
            <label for="mc-<?php echo $id_part ?>"><?php echo $label.$required_data ?></label>
            <input type="text" name="mc-<?php echo $id_part ?>" id="mc-<?php echo $id_part ?>"
                   class="<?php echo $classes ?>" value="<?php echo $value ?>" disabled>
        </li>
    <?php }
    
    /**
     * Renders textarea field
     *
     * @param $field
     * params list:
     * $type
     * $id_part
     * $label
     * $required
     */
    public static function mcFormRenderTextareaField( $field, $existing_data = [] ) {
        // We will need add a list of options for use it for all input types
        $config = [];
        $field_config = static::mcFormAddConfigurationsToField($config, $field, $existing_data);
        if(empty($field_config)) return;
    
        extract( $field_config ); ?>
        <li <?php echo $conditional ?> class="<?php echo !empty( $conditional ) ? 'mc-conditional-field' : '' ?>">
            <label for="mc-<?php echo $id_part ?>"><?php echo $label.$required_data ?></label>
            <textarea name="mc-<?php echo $id_part ?>" id="mc-<?php echo $id_part ?>" rows="3"
                      class="<?php echo $classes ?>"><?php echo $value ?></textarea>
        </li>
    <?php }

	/**
	 * @param $field
	 * @param array $existing_data
	 */
	  public static function mcFormRenderFileField( $field, $existing_data = [] ) {
	      extract( $field );
	      $value         = !empty( $existing_data[ $field['name'] ] ) ? $existing_data[ $field['name'] ] : '';
	      $conditional = static::mcRenderConditionalVal( $field );

	      $required_data = '';
        if( !empty( $required ) ) {
          $classes       = 'mc-required';
          $required_data = '<span class="mc-required-field-span">*</span>';
        }
      ?>
      <li <?php echo $conditional ?> class="<?php echo !empty( $conditional ) ? 'mc-conditional-field' : '' ?>">
        <label for="mc-<?php echo $id_part ?>"><?php echo $label.$required_data ?></label>
          <style>.mc_file_input{display: none}</style>
        <div style="width: 70%;"><?php self::renderFileInput( $field['name'], $classes, $value, $formats ?? null, $echo = true ); ?></div>
      </li>
      <?php
    }
    
    /**
     * Renders editor field
     *
     * @param $field
     * params list:
     * $type
     * $id_part
     * $label
     */
    public static function mcFormRenderEditorField( $field, $existing_data = [] ) {
        // We will need add a list of options for use it for all input types
        $config = [];
        $field_config = static::mcFormAddConfigurationsToField($config, $field, $existing_data);
        if(empty($field_config)) return;
    
        extract( $field_config ); ?>
        <li <?php echo $conditional ?> class="<?php echo !empty( $conditional ) ? 'mc-conditional-field' : '' ?>">
            <label for="mc-<?php echo $id_part ?>"><?php echo $label.$required_data ?></label>
            <?php wp_editor( $value, 'mc-'.$id_part ) ?>
        </li>
    <?php }
    
    /**
     * Renders select field
     *
     * @param $field
     * params list:
     * $type
     * $id_part
     * $label
     * $required
     * $options
     */
    public static function mcFormRenderSelectField( $field, $existing_data = [] ) {
        // We will need add a list of options for use it for all input types
        $config = [];
        $field_config = static::mcFormAddConfigurationsToField($config, $field, $existing_data);
        if(empty($field_config)) return;
    
        extract( $field_config ); ?>
        <li <?php echo $conditional ?> class="<?php echo !empty( $conditional ) ? 'mc-conditional-field' : '' ?>">
            <label for="mc-<?php echo $id_part ?>"><?php echo $label.$required_data ?></label>
            <select name="mc-<?php echo $id_part ?>" id="mc-<?php echo $id_part ?>" class="<?php echo $classes ?>">
                <?php foreach( $options as $option_key => $option ) {
                    $checked = $option_key == $value ? 'selected' : ''; ?>
                    <option <?php echo $checked ?>
                            value="<?php echo $option_key ?>"><?php echo $option ?></option>
                <?php } ?>
            </select>
        </li>
    <?php }
    
    /**
     * Renders affiliate autocomplete field
     *
     * @param $field
     * params list:
     * $label
     * $required
     */
    public static function mcFormRenderAffiliateAutocompleteField( $field ) {
        extract( $field );
        $classes = !empty( $name ) && $name == 'publisher' ? 'mc-affiliates-search ' : '';
        if( !empty( $required ) ) {
            $classes .= 'mc-required';
            $label   .= '<span class="mc-required-field-span">*</span>';
        }
        $args = [
            'classes' => $classes,
            'label'   => $label,
        ];
        MC_Render::templatePart( 'search', 'mc-search-autocomplete-affiliates', $args );
    }
    
    /**
     * Renders conditional data for field
     *
     * @param $field
     *
     * @return string
     */
    public static function mcRenderConditionalVal( $field ) {
        return !empty( $field['conditional'] ) ? 'data-mc-conditional-field="mc-'.$field['conditional'].'"' : '';
    }
    
    /**
     * Renders hidden fields
     *
     * @param $hidden_fields
     */
    public static function mcFormRenderHiddenFields( $hidden_fields ) {
        if( empty( $hidden_fields ) ) return;
        
        foreach( $hidden_fields as $hidden_field ) {
            if( !empty( $hidden_field['mc_nonce'] ) ) {
                MC_Ajax_Functions::render_nonce( $hidden_field['mc_nonce'] );
            } else {
                static::mcFormRenderHiddenField( $hidden_field );
            }
        }
    }
    
    /**
     * @param $hidden_field
     */
    public static function mcFormRenderHiddenField( $hidden_field ) {
        $value = !empty( $hidden_field['val'] ) ? $hidden_field['val'] : ''; ?>
        <input type="hidden" id="mc-<?php echo $hidden_field['id_part'] ?>" value="<?php echo $value ?>">
    <?php }
    
}
