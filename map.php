<?php
namespace nanaobiriyeboahcompanion\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class nanaobiriyeboah_map extends Widget_Base {

    public function get_name() {
        return 'nanaobiriyeboah-map';
    }

    public function get_title() {
        return __( 'Map', 'nanaobiriyeboah-companion' );
    }

    public function get_icon() {
        return 'eicon-google-maps';
    }

    public function get_categories() {
        return [ 'nanaobiriyeboah' ];
    }

    protected function _register_controls() {
        $this->start_controls_section(
            'section_content',
            [
                'label' => __( 'Map Settings', 'nanaobiriyeboah-companion' ),
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'location_name',
            [
                'label' => __( 'Location Name', 'nanaobiriyeboah-companion' ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'New Location', 'nanaobiriyeboah-companion' ),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'latitude',
            [
                'label' => __( 'Latitude', 'nanaobiriyeboah-companion' ),
                'type' => Controls_Manager::NUMBER,
                'default' => 51.503454,
            ]
        );

        $repeater->add_control(
            'longitude',
            [
                'label' => __( 'Longitude', 'nanaobiriyeboah-companion' ),
                'type' => Controls_Manager::NUMBER,
                'default' => -0.119562,
            ]
        );

        $repeater->add_control(
            'info_content',
            [
                'label' => __( 'Info Window Content', 'nanaobiriyeboah-companion' ),
                'type' => Controls_Manager::TEXTAREA,
                'default' => __( 'Enter your info content here', 'nanaobiriyeboah-companion' ),
                'rows' => 5,
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'marker_icon',
            [
                'label' => __( 'Marker Icon', 'nanaobiriyeboah-companion' ),
                'type' => Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-map-marker',
                    'library' => 'solid',
                ],
            ]
        );

        $this->add_control(
            'markers',
            [
                'label' => __( 'Markers', 'nanaobiriyeboah-companion' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'location_name' => __( 'London Eye', 'nanaobiriyeboah-companion' ),
                        'latitude' => 51.503454,
                        'longitude' => -0.119562,
                        'info_content' => '<p>The London Eye is a giant Ferris wheel...</p>',
                        'marker_icon' => [ 'value' => 'fas fa-map-marker', 'library' => 'solid' ],
                    ],
                    [
                        'location_name' => __( 'Palace of Westminster', 'nanaobiriyeboah-companion' ),
                        'latitude' => 51.499633,
                        'longitude' => -0.124755,
                        'info_content' => '<p>The Palace of Westminster is the meeting place...</p>',
                        'marker_icon' => [ 'value' => 'fas fa-map-marker', 'library' => 'solid' ],
                    ],
                ],
                'title_field' => '{{{ location_name }}}',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings();

        ?>
        <div id="map_canvas" style="height: 850px;"></div>

        <script>
            function initializeMap() {
                var mapOptions = {
                    center: new google.maps.LatLng(0, 0), // Default center
                    zoom: 2, // Default zoom level
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };

                var map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
                var bounds = new google.maps.LatLngBounds();

                var markers = <?php echo wp_json_encode($settings['markers']); ?>;

                markers.forEach(function(markerInfo) {
                    var position = new google.maps.LatLng(markerInfo.latitude, markerInfo.longitude);

                    var marker = new google.maps.Marker({
                        position: position,
                        map: map,
                        title: markerInfo.location_name,
                        icon: {
                            url: markerInfo.marker_icon.value,
                            scaledSize: new google.maps.Size(40, 40) // Adjust icon size as needed
                        }
                    });

                    var infoWindow = new google.maps.InfoWindow({
                        content: markerInfo.info_content
                    });

                    marker.addListener('click', function() {
                        infoWindow.open(map, marker);
                    });

                    bounds.extend(position);
                });

                map.fitBounds(bounds);
            }

            function loadGoogleMaps() {
                var script = document.createElement('script');
                script.src = "https://maps.googleapis.com/maps/api/js?key=AIzaSyBEmGuIceexKma4Q6yyRD8M-KS5vbPtUVM&callback=initializeMap";
                document.body.appendChild(script);
            }

            document.addEventListener('DOMContentLoaded', loadGoogleMaps);
        </script>
        <?php
    }

    protected function _content_template() {
    }
}

\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new nanaobiriyeboah_map() );
