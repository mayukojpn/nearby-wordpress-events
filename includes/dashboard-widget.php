<?php

defined( 'WPINC' ) or die();

/**
 * Render callback for the Dashboard widget
 */
function nearbywp_render_dashboard_events() {
	?>
    <div class="hide-if-js">
        <?php esc_html_e( 'This widget requires JavaScript.' ); ?>
    </div>
	<div id="nearbywp" class="hide-if-no-js nearbywp">
		<span class="spinner is-active"></span>
		<?php esc_html_e( 'Loading&hellip;' ); ?>
	</div>

	<script id="tmpl-nearbywp" type="text/template">
		<div class="activity-block">
			<h2>
				<?php printf( __( 'Attend an upcoming event near %s' ), '<strong>{{{ data.location.description }}}</strong>' ); ?>
			</h2>
			<button id="nearbywp-toggle" class="button-link nearbywp-toggle">
				<?php esc_html_e( 'Change location?' ); ?>
			</button>
            <form id="nearbywp-form" class="nearbywp-form" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" method="post">
                <input id="nearbywp-location" class="regular-text" type="text" name="nearbywp-location" />
				<?php submit_button( __( 'Submit' ), 'primary', 'nearbywp-submit', false ); ?>
                <button id="nearbywp-cancel" class="button button-secondary" type="button"><?php esc_html_e( 'Cancel' ); ?></button>
                <span class="spinner"></span>
            </form>
		</div>
		<ul class="activity-block">
            <# if ( data.events.length ) { #>
                <# _.each( data.events, function( event ) { #>
                    <li class="event-{{ event.type }}">
                        <div class="dashicons event-icon"></div>
                        <div class="event-date">{{ event.date }}</div>
                        <div class="event-info">
                            <a class="event-title" href="{{ event.url }}">{{ event.title }}</a>
                            <span class="event-city">{{ event.location.location }}</span>
                        </div>
                    </li>
                <# } ) #>
            <# } else { #>
                <li class="event-none">
                    <?php esc_html_e( 'No events found.' ); ?>
                </li>
            <# } #>
		</ul>
		<p class="nearbywp-footer"><?php esc_html_e( 'Looking for something closer? Search for more ' ); ?><a href="<?php esc_html_e( 'https://www.meetup.com/pro/wordpress/' ); ?>"><?php esc_html_e( 'Meetups' ); ?></a> <?php esc_html_e('or'); ?> <a href="<?php esc_html_e( 'https://central.wordcamp.org/schedule/' ); ?>"><?php esc_html_e( 'WordCamps' ); ?></a>.</p>
	</script>
	<?php
}
