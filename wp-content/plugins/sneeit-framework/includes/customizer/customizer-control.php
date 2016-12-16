<?php

class WP_Customize_Sneeit_Control extends WP_Customize_Control {
	var $declarations;
	var $setting_id;
	
	public function render_content() {		
		include_once( sneeit_framework_plugin_path('/includes/controls/controls.php') );
		new Sneeit_Controls($this->setting_id, $this->declarations, $this->value());
	}
}


class WP_Customize_Sneeit_FontFamily_Control extends WP_Customize_Control {
	public function render_font_list($default) {
		global $Sneeit_Safe_Fonts;
		global $Sneeit_Google_Fonts;
		global $Sneeit_Upload_Fonts;

		?>
	<div class="noselect sneeit-font-list-holder <?php echo $this->type; ?>" data-input="<?php echo $this->id; ?>">
		<div class="sneeit-font-list-value">
			<span class="value"><?php echo $default; ?></span>
			<a href="javascript:void(0)" class="drop"><i class="fa fa-chevron-down icon-down icon"></i><i class="fa fa-chevron-up icon-up icon"></i></a>
		</div>
		<div class="sneeit-font-list scrollbar noselect">
			<?php foreach ($Sneeit_Safe_Fonts as $font_name => $font_property) : ?>
				<a class="safe-font sneeit-font-list-item <?php if ($font_name == $default) echo 'active'; ?>"><?php echo $font_name; ?></a>
			<?php endforeach; ?>
				<span class="spliter"></span>
			<?php foreach ($Sneeit_Google_Fonts as $font_name => $font_property) : ?>
				<a class="google-font sneeit-font-list-item <?php if ($font_name == $default) echo 'active'; ?>"><?php echo $font_name; ?></a>
			<?php endforeach; ?>

			<?php if (is_array($Sneeit_Upload_Fonts) && count($Sneeit_Upload_Fonts)): ?> 
				<span class="spliter"></span>
				<?php foreach ($Sneeit_Upload_Fonts as $font_name => $font_property) : ?>
					<a class="upload-font sneeit-font-list-item <?php if ($font_name == $default) echo 'active'; ?>"><?php echo $font_name; ?></a>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>
		<?php
	}

	public function render_content() {					
		?>
			<label>

				<?php if ( ! empty( $this->label ) ) : ?>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php endif;
				if ( ! empty( $this->description ) ) : ?>
					<span class="description customize-control-description"><?php echo $this->description; ?></span>
				<?php endif; ?>

				<?php $this->render_font_list($this->value()); ?>
				<input type="hidden" <?php $this->input_attrs(); ?> id="<?php echo $this->id; ?>" data-type="sneeit-<?php echo $this->type; ?>" class="sneeit-<?php echo $this->type; ?>" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?> />
			</label>
		<?php
	}
}

class WP_Customize_Sneeit_Font_Control extends WP_Customize_Sneeit_FontFamily_Control { 
	public function render_content() {
		?>
			<label>
				<?php if ( ! empty( $this->label ) ) : ?>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php endif;
				if ( ! empty( $this->description ) ) : ?>
					<span class="description customize-control-description"><?php echo $this->description; ?></span>
				<?php endif; ?>
					
					<?php 
					$the_value = $this->value();
					$the_value = explode(' ', $the_value);
					$font_style = '';
					$font_weight = '';
					$font_size = '';
					$font_name = '';
					foreach ($the_value as $key => $value) {
						switch ($key) {
							case 0:
								$font_style = $value;
								break;
							
							case 1:
								$font_weight = $value;
								break;
							
							case 2:
								$font_size = $value;
								break;
							
							default:
								if ($font_name) {
									$font_name .= ' ';
								}
								$font_name .= $value;
								$font_name = str_replace('\'', '', $font_name);
								$font_name = str_replace('"', '', $font_name);
								break;
						}						
					}
					$this->render_font_list($font_name);
					$font_size = (int) str_replace('px', '', $font_size);
					global $Sneeit_Font_Sizes;
					if (!in_array($font_size, $Sneeit_Font_Sizes)) {
						foreach ($Sneeit_Font_Sizes as $index => $size) :
							if ($font_size < $size) {
								array_splice($Sneeit_Font_Sizes, $index, 0, $font_size);
								break;
							}
						endforeach;
					}
					?>
					<div class="sneeit-font-design" data-input="<?php echo $this->id; ?>">												
						<a class="noselect font-style<?php 
						if ($font_style == 'italic') {
							echo ' active';
						}
						?>" data-input="<?php echo $this->id; ?>" style="font-style: <?php echo $font_style; ?>" >I</a>
						<a class="noselect font-weight<?php 
						if ($font_weight == 'bold') {
							echo ' active';
						}
						?>" data-input="<?php echo $this->id; ?>" style="font-weight: <?php echo $font_weight; ?>">B</a>
						<select class="font-size">
							<?php
							foreach ($Sneeit_Font_Sizes as $size) :?>
								<option value="<?php echo $size; ?>px"<?php selected($font_size, $size); ?>><?php echo $size; ?>px</option>
							<?php
							endforeach;
							?>							
						</select>
					</div>

					<input type="hidden" <?php $this->input_attrs(); ?> id="<?php echo $this->id; ?>" class="sneeit-<?php echo $this->type; ?>" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?> />
			</label>
		<?php
	}
}

class WP_Customize_Sneeit_Visual_Control extends WP_Customize_Control {	
	public function render_content() {
		if ( empty( $this->choices ) ) {
			return;
		}
		?>
		<label>
			
			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif;
			if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo $this->description; ?></span>
			<?php endif; ?>
				
			<input type="hidden" <?php $this->input_attrs(); ?> id="<?php echo $this->id; ?>" class="sneeit-<?php echo $this->type; ?>" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?> />

			<div class="sneeit-visual-picker" data-input="<?php echo $this->id; ?>">
				<?php
				foreach ( $this->choices as $value => $label ):
					echo '<a href="javascript:void(0)" class="noselect sneeit-visual-picker-item ' . ( $this->value() == $value? 'active' : '') . '" data-value="' . esc_attr( $value ) . '">' . $label . '</a>';
				endforeach;
				?>
			</div>
			
		</label>
		<?php
	}
}

class WP_Customize_Sneeit_Sidebar_Control extends WP_Customize_Control {
	var $choices = array();
	var $prefix = '';
	
	public function render_content() {
		global $wp_registered_sidebars;		
		?>
		<label>
			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif;
			if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo $this->description; ?></span>
			<?php endif; ?>

			<select <?php $this->link(); ?>>
				<?php if ($this->choices): ?>
					<?php foreach ($this->choices as $value => $label) : ?>
						<option value="<?php echo $value; ?>" <?php selected( $this->value(), $value, true ); ?>><?php echo $label; ?></option>
					<?php endforeach; ?>					
				<?php endif; ?>
					
				<?php				
				foreach ( $wp_registered_sidebars as $sidebar ) :
					if ($this->prefix && (!(strpos($sidebar['id'], $this->prefix) === 0))) :
						continue;
					endif;
					
					echo '<option value="' . esc_attr( $sidebar['id'] ) . '"' . selected( $this->value(), $sidebar['id'], false ) . '>' . $sidebar['name'] . '</option>';
				endforeach;
				?>
			</select>
		</label>
		<?php
	}
}