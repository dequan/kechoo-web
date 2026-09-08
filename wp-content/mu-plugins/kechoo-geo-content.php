<?php
/**
 * Plugin Name: KECHOO GEO Content
 * Description: Adds concise, machine-readable buying guidance and matching structured data to key KECHOO pages.
 * Version: 1.0.0
 * Requires PHP: 8.1
 *
 * @package Kechoo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Kechoo_GEO_Content {
	const VERSION = '1.0.0';

	public static function init() {
		add_filter( 'the_content', array( __CLASS__, 'enrich_page_content' ), 30 );
		add_action( 'wp_head', array( __CLASS__, 'print_faq_schema' ), 40 );
	}

	public static function enrich_page_content( $content ) {
		if ( is_admin() || ! is_singular( 'page' ) || ! in_the_loop() || ! is_main_query() ) {
			return $content;
		}

		if ( is_page( 'resources' ) && false === strpos( $content, 'data-kechoo-geo="resources-faq"' ) ) {
			return $content . self::resources_faq_html();
		}

		if ( is_page( 'about' ) && false === strpos( $content, 'data-kechoo-geo="about-facts"' ) ) {
			return $content . self::about_facts_html();
		}

		return $content;
	}

	private static function faq_items() {
		return array(
			array(
				'question' => 'What size bandsaw blade do I need?',
				'answer'   => 'Use the exact blade length, width, and thickness specified on the machine plate or in the saw manufacturer’s manual. The saw type alone is not enough to identify a compatible blade. If the specification is missing, send KECHOO the machine model, an old blade label, or measured blade dimensions before ordering.',
				'link'     => home_url( '/find-your-blade/' ),
				'link_text' => 'Use the blade selector',
			),
			array(
				'question' => 'How do I choose bandsaw blade TPI?',
				'answer'   => 'Choose tooth pitch from the material, its cross-section, and whether the workpiece is solid, tubular, bundled, fresh, frozen, or contains bone. The goal is stable tooth engagement without overloading the gullets. Provide the material and section size when you need a specific recommendation.',
				'link'     => home_url( '/request-a-quote/' ),
				'link_text' => 'Ask for a TPI recommendation',
			),
			array(
				'question' => 'When should I choose a hardened, bi-metal, or carbide blade?',
				'answer'   => 'Hardened high-carbon steel blades are commonly used for food and bone, woodworking, and general-purpose cutting. M42 bi-metal blades are a versatile choice for fabrication and metal cutting. Carbide-tipped blades suit demanding alloys, abrasive materials, and large sections when the saw and setup are sufficiently rigid.',
				'link'     => home_url( '/technology/' ),
				'link_text' => 'Compare blade technologies',
			),
			array(
				'question' => 'Can one bandsaw blade be used for meat, wood, and metal?',
				'answer'   => 'Do not select a blade by length alone. Meat and bone, wood, and metal applications need different tooth geometry, pitch, blade construction, machine settings, and hygiene or chip-control practices. Match the blade to both the saw and the material being cut.',
				'link'     => home_url( '/applications/' ),
				'link_text' => 'Choose by application',
			),
			array(
				'question' => 'Why do bandsaw blade teeth strip or wear too quickly?',
				'answer'   => 'Common causes include an unsuitable tooth pitch, excessive feed, incorrect speed, poor break-in, low or unstable tension, worn guides, ineffective coolant, a damaged chip brush, or movement in the workpiece. Inspect the failed blade and the complete cutting setup instead of changing only one parameter.',
				'link'     => home_url( '/contact/' ),
				'link_text' => 'Contact technical support',
			),
			array(
				'question' => 'How should I break in a new bi-metal or carbide bandsaw blade?',
				'answer'   => 'Start with the saw in good condition and use the blade supplier’s speed, feed, tension, and coolant guidance for the material. Apply a controlled initial cutting load and increase it gradually while checking chip formation and cut stability. A break-in procedure should be matched to the blade, machine, and workpiece rather than treated as one universal setting.',
				'link'     => home_url( '/resources/' ),
				'link_text' => 'Read selection and operating guidance',
			),
			array(
				'question' => 'When is a carbide-tipped bandsaw blade appropriate?',
				'answer'   => 'Consider carbide for high-alloy steels, abrasive materials, difficult-to-machine stock, or large production sections. The machine should have adequate rigidity, accurate guides, stable tension, effective coolant, and controlled cutting parameters. For an uncertain setup, confirm machine capability before ordering.',
				'link'     => home_url( '/blade-technology/carbide/' ),
				'link_text' => 'Explore carbide blade guidance',
			),
			array(
				'question' => 'What information does KECHOO need to recommend or quote a blade?',
				'answer'   => 'Send the application, material grade or product being cut, workpiece size and shape, machine make and model, blade length, width, thickness, tooth pitch if known, estimated quantity, destination country, and the cutting problem or production target. Photos of the machine plate, old blade label, cut, or failed blade can reduce ambiguity.',
				'link'     => home_url( '/request-a-quote/' ),
				'link_text' => 'Request a blade quotation',
			),
		);
	}

	private static function resources_faq_html() {
		$html  = '<section class="kechoo-geo-faq" data-kechoo-geo="resources-faq" aria-labelledby="kechoo-blade-faq-title">';
		$html .= '<h2 id="kechoo-blade-faq-title">Bandsaw blade questions, answered</h2>';
		$html .= '<p>Direct answers to the specification and operating questions buyers ask most often.</p>';

		foreach ( self::faq_items() as $item ) {
			$html .= '<article class="kechoo-geo-faq__item">';
			$html .= '<h3>' . esc_html( $item['question'] ) . '</h3>';
			$html .= '<p>' . esc_html( $item['answer'] ) . '</p>';
			$html .= '<p><a href="' . esc_url( $item['link'] ) . '">' . esc_html( $item['link_text'] ) . ' <span aria-hidden="true">→</span></a></p>';
			$html .= '</article>';
		}

		$html .= '</section>';
		return $html;
	}

	private static function about_facts_html() {
		return '<section class="kechoo-geo-facts" data-kechoo-geo="about-facts" aria-labelledby="kechoo-facts-title">'
			. '<h2 id="kechoo-facts-title">KECHOO at a glance</h2>'
			. '<dl>'
			. '<dt>What KECHOO supplies</dt><dd>Industrial bandsaw blades for food and bone processing, woodworking, fabrication, and metal cutting.</dd>'
			. '<dt>Blade technologies</dt><dd>Hardened high-carbon steel, M42 bi-metal, and carbide-tipped blade constructions.</dd>'
			. '<dt>Who KECHOO serves</dt><dd>Factories, distributors, equipment partners, and OEM buyers that need standard or application-specific blade supply.</dd>'
			. '<dt>How buyers select a blade</dt><dd>Start with the cutting application, then confirm the machine and the blade length, width, thickness, tooth pitch, and material requirements.</dd>'
			. '<dt>How to buy</dt><dd>Browse available specifications or request a quotation for custom lengths, technical selection, volume supply, and OEM requirements.</dd>'
			. '</dl>'
			. '<p><a href="' . esc_url( home_url( '/find-your-blade/' ) ) . '">Find your blade</a> · <a href="' . esc_url( home_url( '/request-a-quote/' ) ) . '">Request a quote</a> · <a href="' . esc_url( home_url( '/contact/' ) ) . '">Contact KECHOO</a></p>'
			. '</section>';
	}

	public static function print_faq_schema() {
		if ( is_admin() || ! is_page( 'resources' ) ) {
			return;
		}

		$entities = array();
		foreach ( self::faq_items() as $item ) {
			$entities[] = array(
				'@type'          => 'Question',
				'name'           => $item['question'],
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => $item['answer'],
				),
			);
		}

		$schema = array(
			'@context'   => 'https://schema.org',
			'@type'      => 'FAQPage',
			'@id'        => trailingslashit( home_url( '/resources/' ) ) . '#bandsaw-blade-faq',
			'url'        => trailingslashit( home_url( '/resources/' ) ),
			'name'       => 'Bandsaw blade questions, answered',
			'mainEntity' => $entities,
		);

		echo "\n<script type=\"application/ld+json\" data-kechoo-geo=\"resources-faq\">";
		echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
		echo "</script>\n";
	}
}

Kechoo_GEO_Content::init();
