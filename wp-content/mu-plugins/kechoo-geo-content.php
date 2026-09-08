<?php
/**
 * Plugin Name: KECHOO GEO Content
 * Description: Adds concise, machine-readable buying guidance and matching structured data to key KECHOO pages.
 * Version: 1.2.0
 * Requires PHP: 8.1
 *
 * @package Kechoo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Kechoo_GEO_Content {
	const VERSION = '1.2.0';

	public static function init() {
		add_action( 'init', array( __CLASS__, 'maybe_create_guide_pages' ), 30 );
		add_filter( 'the_content', array( __CLASS__, 'enrich_page_content' ), 30 );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_calculator_assets' ) );
		add_action( 'wp_head', array( __CLASS__, 'print_faq_schema' ), 40 );
		add_action( 'wp_head', array( __CLASS__, 'print_guide_schema' ), 41 );
	}

	private static function guide_pages() {
		return array(
			'bandsaw-blade-selection' => array(
				'title'       => 'How to Choose a Bandsaw Blade',
				'description' => 'A practical step-by-step guide to choosing bandsaw blade dimensions, construction, tooth pitch, and application fit.',
			),
			'bandsaw-blade-use' => array(
				'title'       => 'How to Use a Bandsaw Blade Correctly',
				'description' => 'Installation, break-in, operating checks, and safe-use guidance for more consistent bandsaw blade performance.',
			),
			'bandsaw-blade-maintenance' => array(
				'title'       => 'Bandsaw Blade Maintenance Guide',
				'description' => 'A preventive maintenance routine for the blade, guides, wheels, coolant, chip brush, tension, and storage.',
			),
			'bandsaw-blade-troubleshooting' => array(
				'title'       => 'Bandsaw Blade Troubleshooting Guide',
				'description' => 'Diagnose tooth stripping, crooked cuts, premature wear, band breakage, vibration, and chip loading.',
			),
			'bandsaw-blade-size' => array(
				'title'       => 'Find Your Bandsaw Blade Size',
				'description' => 'Identify a replacement bandsaw blade from the machine nameplate, manual, old blade, or a two-wheel blade-length estimate.',
			),
		);
	}

	public static function maybe_create_guide_pages() {
		if ( self::VERSION === get_option( 'kechoo_geo_content_version' ) ) {
			return;
		}

		$resources = get_page_by_path( 'resources', OBJECT, 'page' );
		$parent_id = $resources instanceof WP_Post ? (int) $resources->ID : 0;
		$complete  = true;

		foreach ( self::guide_pages() as $slug => $guide ) {
			$path = $parent_id ? 'resources/' . $slug : $slug;
			if ( get_page_by_path( $path, OBJECT, 'page' ) ) {
				continue;
			}

			$result = wp_insert_post(
				array(
					'post_type'      => 'page',
					'post_status'    => 'publish',
					'post_title'     => $guide['title'],
					'post_name'      => $slug,
					'post_excerpt'   => $guide['description'],
					'post_content'   => '',
					'post_parent'    => $parent_id,
					'comment_status' => 'closed',
				),
				true
			);

			if ( is_wp_error( $result ) ) {
				$complete = false;
			}
		}

		if ( $complete ) {
			update_option( 'kechoo_geo_content_version', self::VERSION, false );
		}
	}

	public static function enrich_page_content( $content ) {
		if ( is_admin() || ! is_singular( 'page' ) || ! in_the_loop() || ! is_main_query() ) {
			return $content;
		}

		if ( is_page( 'resources' ) && false === strpos( $content, 'data-kechoo-geo="resources-guides"' ) ) {
			return $content . self::guide_navigation_html() . self::resources_faq_html();
		}

		if ( is_page( 'about' ) && false === strpos( $content, 'data-kechoo-geo="about-facts"' ) ) {
			return $content . self::about_facts_html();
		}

		if ( is_page( 'find-your-blade' ) && false === strpos( $content, 'data-kechoo-geo="machine-fit-entry"' ) ) {
			return $content . self::machine_fit_entry_html();
		}

		foreach ( self::guide_pages() as $slug => $guide ) {
			if ( is_page( $slug ) && false === strpos( $content, 'data-kechoo-guide="' . $slug . '"' ) ) {
				return $content . self::guide_content( $slug );
			}
		}

		return $content;
	}

	private static function guide_url( $slug ) {
		return home_url( '/resources/' . $slug . '/' );
	}

	private static function guide_navigation_html() {
		$html  = '<section class="kechoo-resource-guides" data-kechoo-geo="resources-guides" aria-labelledby="kechoo-resource-guides-title">';
		$html .= '<h2 id="kechoo-resource-guides-title">Practical bandsaw blade guides</h2>';
		$html .= '<p>Follow the guide that matches your current task, from choosing a blade through diagnosing a cutting problem.</p>';
		$html .= '<ul>';
		foreach ( self::guide_pages() as $slug => $guide ) {
			$html .= '<li><a href="' . esc_url( self::guide_url( $slug ) ) . '"><strong>' . esc_html( $guide['title'] ) . '</strong></a><br>' . esc_html( $guide['description'] ) . '</li>';
		}
		$html .= '</ul></section>';
		return $html;
	}

	private static function guide_content( $slug ) {
		switch ( $slug ) {
			case 'bandsaw-blade-selection':
				return self::selection_guide_html();
			case 'bandsaw-blade-use':
				return self::use_guide_html();
			case 'bandsaw-blade-maintenance':
				return self::maintenance_guide_html();
			case 'bandsaw-blade-troubleshooting':
				return self::troubleshooting_guide_html();
			case 'bandsaw-blade-size':
				return self::blade_size_guide_html();
			default:
				return '';
		}
	}

	private static function machine_fit_entry_html() {
		return '<section class="kechoo-machine-fit-entry" data-kechoo-geo="machine-fit-entry" aria-labelledby="kechoo-machine-fit-entry-title">'
			. '<div><h2 id="kechoo-machine-fit-entry-title">Do not know your machine’s blade size?</h2>'
			. '<p>Identify the specification from the nameplate or manual, measure a known fitting blade, or estimate the loop length of a conventional two-wheel saw.</p></div>'
			. '<p><a class="kechoo-button" href="' . esc_url( self::guide_url( 'bandsaw-blade-size' ) ) . '">Find your blade size</a></p>'
			. '</section>';
	}

	private static function blade_size_guide_html() {
		return <<<'HTML'
<article class="kechoo-practical-guide kechoo-size-guide" data-kechoo-guide="bandsaw-blade-size">
<p class="kechoo-guide-summary"><strong>Use the most reliable source available:</strong> check the machine manual or nameplate first, then a correctly fitted old blade. Use wheel measurements only to estimate the length of a conventional two-wheel saw. Always verify length, width, thickness, and permitted tension range before ordering.</p>

<nav class="kechoo-fit-route" aria-label="Blade size identification methods">
<a href="#read-machine-nameplate"><span>1</span><strong>Read the machine</strong><small>Best source</small></a>
<a href="#measure-old-blade"><span>2</span><strong>Measure the old blade</strong><small>Physical check</small></a>
<a href="#blade-length-calculator"><span>3</span><strong>Estimate from wheels</strong><small>Two-wheel saws only</small></a>
</nav>

<h2 id="read-machine-nameplate">1. Read the machine nameplate and manual</h2>
<p>Look for a plate near the motor, electrical enclosure, upper wheel housing, rear cover, or base. The owner’s manual or parts list may contain the blade specification even when the plate does not.</p>

<div class="kechoo-reading-grid">
<section>
<h3>Record these identity fields</h3>
<dl>
<dt>Manufacturer</dt><dd>The company that made the saw</dd>
<dt>Model</dt><dd>The exact model, including letters, numbers, and type or series</dd>
<dt>Serial or type number</dt><dd>Useful when one model changed specification between production runs</dd>
<dt>Machine category</dt><dd>Portable, woodworking, butcher, vertical metal, or horizontal production saw</dd>
</dl>
</section>
<section>
<h3>Record these blade fields</h3>
<dl>
<dt>Blade length</dt><dd>The closed-loop length, often shown in millimetres or inches</dd>
<dt>Blade width</dt><dd>From the tooth tip to the back edge</dd>
<dt>Blade thickness</dt><dd>The gauge of the blade body</dd>
<dt>Permitted width range</dt><dd>Common on woodworking saws that accept narrow and wide blades</dd>
<dt>Speed range</dt><dd>Helps confirm application and blade construction after size is known</dd>
</dl>
</section>
</div>

<aside class="kechoo-fit-note"><strong>Photograph the complete plate.</strong> Keep the model suffix, voltage label, serial/type field, and measurement units visible. Do not crop the photo to one number that may be the motor, electrical, or wheel specification rather than the blade length.</aside>

<h2>Portable bandsaws need an exact model match</h2>
<p>Compact, sub-compact, deep-cut, and cordless portable bandsaws can look similar while using different loop lengths. Use the complete model and type number to check the manufacturer’s manual or approved accessory list. Do not infer blade length from cutting capacity or measure the outside of the housing. Portable saws may use closely spaced nominal sizes that are not interchangeable.</p>

<h2>Woodworking bandsaws need size and width range</h2>
<p>For a woodworking bandsaw, confirm the loop length and the minimum and maximum blade width allowed by the manufacturer. Width is then chosen for the job: narrower blades can follow tighter curves, while wider blades generally provide more support for straight cuts and resawing when the machine permits them. Thickness must remain suitable for the wheels and guides.</p>

<h2 id="measure-old-blade">2. Measure a correctly fitted old blade</h2>
<p>Only use an old blade as the reference when you know it fitted and tensioned correctly. Isolate the machine and follow its blade-removal instructions. Wear suitable hand and eye protection; large loops can spring open and should be handled by trained personnel.</p>

<h3>Measure the loop length</h3>
<ol>
<li>Place the uncoiled loop on a clear floor or long workbench without twisting it.</li>
<li>Mark one reference point on the blade back and the matching start point on the surface.</li>
<li>Roll the loop carefully along its back edge for one complete revolution until the blade mark returns to the surface.</li>
<li>Measure the distance between the start and finish points. Repeat the measurement to check consistency.</li>
</ol>
<p>Do not measure around a folded coil and do not assume the old blade is correct if the machine could not tension or track it normally.</p>

<h3>Measure the rest of the specification</h3>
<table>
<thead><tr><th>Field</th><th>How to measure or identify it</th><th>Common mistake</th></tr></thead>
<tbody>
<tr><td><strong>Width</strong></td><td>Measure from the tooth tip to the back edge</td><td>Measuring only the blade body below the teeth</td></tr>
<tr><td><strong>Thickness</strong></td><td>Use a micrometer on clean blade body away from the weld and tooth set</td><td>Measuring across a set tooth or burr</td></tr>
<tr><td><strong>TPI</strong></td><td>Count tooth spacing over one inch, or record both values of a variable pitch</td><td>Assuming TPI from length or machine model</td></tr>
<tr><td><strong>Tooth direction</strong></td><td>Photograph the blade as installed before removal</td><td>Installing the replacement with teeth facing away from the cutting direction</td></tr>
</tbody>
</table>

<section class="kechoo-calculator" id="blade-length-calculator" aria-labelledby="blade-calculator-title">
<div class="kechoo-calculator__intro">
<h2 id="blade-calculator-title">3. Two-wheel bandsaw blade length estimator</h2>
<p>Use this only when the manual, nameplate, and correct old blade are unavailable. It estimates the open-loop path around two wheels. Place the adjustable wheel near the middle of its tension travel before measuring center distance.</p>
</div>

<form class="kechoo-calculator__form" data-kechoo-blade-calculator novalidate>
<fieldset class="kechoo-unit-choice">
<legend>Measurement unit</legend>
<label><input type="radio" name="kechoo_calc_unit" value="mm" checked> Millimetres</label>
<label><input type="radio" name="kechoo_calc_unit" value="in"> Inches</label>
</fieldset>

<div class="kechoo-calculator__fields">
<label for="kechoo-wheel-one"><span>Wheel 1 diameter</span><input id="kechoo-wheel-one" name="wheel_one" type="number" min="0.01" step="any" inputmode="decimal" required aria-describedby="kechoo-wheel-help"><small>Use the effective diameter near the blade path.</small></label>
<label for="kechoo-wheel-two"><span>Wheel 2 diameter</span><input id="kechoo-wheel-two" name="wheel_two" type="number" min="0.01" step="any" inputmode="decimal" required aria-describedby="kechoo-wheel-help"><small>For equal wheels, enter the same diameter.</small></label>
<label for="kechoo-center-distance"><span>Center-to-center distance</span><input id="kechoo-center-distance" name="center_distance" type="number" min="0.01" step="any" inputmode="decimal" required aria-describedby="kechoo-center-help"><small>Measure between the two wheel axle centers.</small></label>
</div>

<p id="kechoo-wheel-help" class="kechoo-calculator__help">Measure the wheel diameter where the blade runs, not the outside of a guard or housing.</p>
<p id="kechoo-center-help" class="kechoo-calculator__help">Move the adjustable wheel to approximately the middle of its usable tension range before measuring.</p>

<div class="kechoo-calculator__actions">
<button class="kechoo-button" type="submit">Estimate blade length</button>
<button class="kechoo-button kechoo-button--outline" type="reset">Clear measurements</button>
</div>
<p class="kechoo-calculator__error" data-kechoo-calculator-error role="alert" hidden></p>
</form>

<section class="kechoo-calculator__result" data-kechoo-calculator-result aria-live="polite" hidden>
<p class="kechoo-calculator__label">Estimated reference length</p>
<p class="kechoo-calculator__value"><strong data-kechoo-result-primary></strong> <span data-kechoo-result-secondary></span></p>
<p data-kechoo-result-formula></p>
<p>This is an estimate, not an ordering confirmation. Tyre thickness, blade tracking position, wheel geometry, measuring error, and tension travel can change the required production length.</p>
<div class="kechoo-calculator__actions">
<button class="kechoo-button kechoo-button--outline-light" type="button" data-kechoo-copy-result>Copy result</button>
<a class="kechoo-button" href="/request-a-quote/">Verify with KECHOO</a>
</div>
</section>

<details class="kechoo-calculator__formula">
<summary>Formula and assumptions</summary>
<p>For wheel diameters <var>D1</var> and <var>D2</var>, with center distance <var>C</var>, the estimator uses:</p>
<p><code>L ≈ 2C + π(D1 + D2) ÷ 2 + (D1 − D2)² ÷ 4C</code></p>
<p>For two equal wheels of diameter <var>D</var>, this simplifies to <code>L ≈ 2C + πD</code>. The formula approximates an open belt path and assumes two circular wheels in the same plane. It is not suitable for three-wheel saws, twisted blade paths, portable saw internals, or machines with additional routing pulleys.</p>
</details>
</section>

<h2>Before ordering the replacement</h2>
<ul class="kechoo-confirm-list">
<li>Confirm the manufacturer, full model, and type or serial range.</li>
<li>Confirm length, width, and thickness as separate values.</li>
<li>Check that the estimated length sits within the machine’s tension adjustment range.</li>
<li>Choose tooth pitch only after identifying the material and workpiece section.</li>
<li>For portable and three-wheel saws, use the manufacturer’s specified blade size.</li>
<li>Send photos and measurements when any value remains uncertain.</li>
</ul>

<aside><h2>Send us enough information to verify the fit</h2><p>Include the machine plate, full machine view, upper and lower wheel area, old blade label, measured loop length, width, thickness, and the material you plan to cut. <a href="/request-a-quote/">Request a specification check</a> before ordering a custom loop.</p></aside>
</article>
HTML;
	}

	private static function selection_guide_html() {
		return <<<'HTML'
<article class="kechoo-practical-guide" data-kechoo-guide="bandsaw-blade-selection">
<p class="kechoo-guide-summary"><strong>Short answer:</strong> choose the blade dimensions from the saw first, then match the blade construction, tooth pitch, and tooth form to the material and workpiece section. A blade that fits the wheels can still be wrong for the cutting job.</p>

<nav aria-label="On this page"><strong>Selection steps:</strong> <a href="#confirm-blade-size">blade size</a> · <a href="#identify-cutting-job">application</a> · <a href="#choose-construction">construction</a> · <a href="#choose-tooth-pitch">tooth pitch</a> · <a href="#selection-checklist">final check</a></nav>

<h2 id="confirm-blade-size">1. Confirm the blade size your saw accepts</h2>
<p>Start with the machine plate, manual, or the complete marking on a correctly fitted old blade. Record all three dimensions: blade length, width, and thickness. Do not identify compatibility from the saw type or blade length alone.</p>
<ul>
<li><strong>Length</strong> must match the machine's adjustment range.</li>
<li><strong>Width</strong> must suit the wheels, guides, and the required straight or contour cut.</li>
<li><strong>Thickness</strong> must suit the wheel diameter and guide system.</li>
</ul>
<p>If the label is missing, provide the machine make, model, plate photo, and old-blade measurements through <a href="/request-a-quote/">technical review</a>. Follow the machine manufacturer's procedure whenever measuring or removing a blade.</p>

<h2 id="identify-cutting-job">2. Describe what the blade must cut</h2>
<p>The same machine may need different blades for different work. Record the material, cross-section, condition, and production goal before selecting a product.</p>
<table>
<thead><tr><th>Question</th><th>Examples</th><th>Why it matters</th></tr></thead>
<tbody>
<tr><td>What material is being cut?</td><td>Fresh or frozen meat and bone, softwood, hardwood, carbon steel, stainless steel, alloy steel, aluminium</td><td>Determines the suitable cutting-edge material and tooth geometry</td></tr>
<tr><td>What is its shape?</td><td>Solid, tube, profile, bundle, plate, carcass, log</td><td>Changes tooth engagement and chip space requirements</td></tr>
<tr><td>What is its size?</td><td>Diameter, wall thickness, bundle width, cutting height</td><td>Guides the tooth-pitch range</td></tr>
<tr><td>What is the priority?</td><td>Blade life, cutting speed, surface quality, accuracy, cost per cut</td><td>Helps resolve trade-offs instead of selecting by price alone</td></tr>
</tbody>
</table>

<h2 id="choose-construction">3. Choose the blade construction</h2>
<table>
<thead><tr><th>Construction</th><th>Typical fit</th><th>Check before choosing</th></tr></thead>
<tbody>
<tr><td><strong>Hardened high-carbon steel</strong></td><td>Food and bone, woodworking, and general-purpose cutting</td><td>Required dimensions, tooth form, pitch, and application hygiene</td></tr>
<tr><td><strong>M42 bi-metal</strong></td><td>Versatile cutting of carbon steel, stainless steel, tube, profiles, bundles, and solid sections</td><td>Material grade, section, speed, feed, coolant, and break-in</td></tr>
<tr><td><strong>Carbide-tipped</strong></td><td>Abrasive materials, high alloys, difficult-to-machine stock, and large production sections</td><td>Machine rigidity, guides, tension system, coolant delivery, and operator control</td></tr>
</tbody>
</table>
<p>See the <a href="/technology/">blade technology comparison</a> or browse by <a href="/applications/">cutting application</a>.</p>

<h2 id="choose-tooth-pitch">4. Choose tooth pitch from the engaged section</h2>
<p>Tooth pitch is not selected from material name alone. Consider how much of the blade is in contact with the workpiece during the cut. Thin walls and small sections normally require a finer pitch; large solid sections need more gullet capacity and therefore a coarser or suitable variable pitch.</p>
<ul>
<li>Too fine a pitch can pack chips into the gullets and contribute to stripped teeth.</li>
<li>Too coarse a pitch can create unstable tooth engagement, impact, vibration, or tooth damage.</li>
<li>Bundles and structural profiles must be evaluated at the maximum and minimum engaged sections.</li>
<li>Food and bone applications must account for product size, fresh or frozen condition, and bone content.</li>
</ul>
<p>Use the pitch chart supplied for the selected product, or send the workpiece dimensions to KECHOO instead of guessing.</p>

<h2>5. Check tooth form and production conditions</h2>
<p>Regular, hook, variable, set, and carbide tooth geometries manage cutting forces and chip formation differently. Also consider whether the saw is manual, semi-automatic, or production automatic; whether coolant is available; and whether the workpiece can be clamped securely.</p>

<h2 id="selection-checklist">Final selection checklist</h2>
<ul>
<li>Machine make and model confirmed</li>
<li>Length × width × thickness confirmed</li>
<li>Material grade or product identified</li>
<li>Workpiece shape and full section range recorded</li>
<li>Blade construction selected for the application</li>
<li>Tooth pitch selected from tooth engagement, not guesswork</li>
<li>Machine condition, guides, tension, coolant, and chip brush checked</li>
<li>Performance priority defined</li>
</ul>

<aside><h2>Need a verified recommendation?</h2><p>Use <a href="/find-your-blade/">Find Your Blade</a> for catalog matches. For an unknown machine, custom length, difficult material, or repeat blade failure, <a href="/request-a-quote/">send the complete cutting details for technical review</a>.</p></aside>
</article>
HTML;
	}

	private static function use_guide_html() {
		return <<<'HTML'
<article class="kechoo-practical-guide" data-kechoo-guide="bandsaw-blade-use">
<p class="kechoo-guide-summary"><strong>Short answer:</strong> correct use begins before the first cut. Verify the blade and machine, install the teeth in the cutting direction, set tension and guides to the machine specification, break in applicable blades, and watch the cut, chips, sound, and coolant.</p>

<h2>Before installing the blade</h2>
<ol>
<li>Lock out the machine and follow its manual and site safety procedure.</li>
<li>Confirm blade length, width, thickness, tooth pitch, and application.</li>
<li>Inspect the wheels, flanges, guides, bearings, guards, chip brush, vise, and coolant system.</li>
<li>Clean chips and contamination from the blade path.</li>
<li>Inspect the new blade for shipping damage before opening and handling it with suitable protection.</li>
</ol>

<h2>Install it in the correct direction</h2>
<p>The tooth points must face the cutting direction at the work zone. Make sure the blade back and sides sit correctly in the guides and on the wheels. Refit guards before operation. If the blade will not track normally, stop and correct the machine setup rather than forcing it into position.</p>

<h2>Set tension, guides, clamping, and coolant</h2>
<ul>
<li><strong>Tension:</strong> use the value and method specified by the saw manufacturer for the installed blade size.</li>
<li><strong>Guide spacing:</strong> position adjustable guide arms as close to the work as the machine safely permits.</li>
<li><strong>Clamping:</strong> secure single pieces, tubes, profiles, and bundles so they cannot rotate or move.</li>
<li><strong>Coolant:</strong> use the recommended fluid, concentration, flow, and nozzle position for the material and blade.</li>
<li><strong>Chip brush:</strong> adjust it to clear the gullets without damaging the blade.</li>
</ul>

<h2>Break in a new bi-metal or carbide blade</h2>
<p>A new tooth edge is sharp and vulnerable to microscopic chipping. Use the blade supplier's break-in guidance for the specific product and material. Begin with a controlled cutting load, maintain the appropriate band speed and coolant, and increase feed gradually while monitoring the cut. Do not apply full production feed immediately unless the selected blade is explicitly supplied as ready to use without break-in.</p>

<h2>Monitor the first cuts</h2>
<p>Do not judge the process by cutting time alone. Watch for stable tracking, consistent sound, effective chip evacuation, coolant reaching the cut, secure clamping, and an even cut surface. Stop and inspect when you see sudden vibration, sparks outside the expected process, smoke, a changing cut line, unusual noise, missing teeth, a crack, or workpiece movement.</p>

<h2>During production</h2>
<ul>
<li>Keep workpiece dimensions and material within the range used to choose the tooth pitch.</li>
<li>Do not change speed and feed together when diagnosing a problem; make controlled changes and record them.</li>
<li>Remove offcuts and chips using the approved method, never by hand near a moving blade.</li>
<li>Record cuts or operating time so blade life can be compared under similar conditions.</li>
<li>When changing material or section, confirm that the existing blade and settings are still appropriate.</li>
</ul>

<aside><h2>Use the machine manual as the authority</h2><p>This guide does not replace the saw manufacturer's safety, tension, speed, feed, guarding, or maintenance instructions. If performance changes unexpectedly, use the <a href="/resources/bandsaw-blade-troubleshooting/">troubleshooting guide</a> or <a href="/contact/">contact technical support</a>.</p></aside>
</article>
HTML;
	}

	private static function maintenance_guide_html() {
		return <<<'HTML'
<article class="kechoo-practical-guide" data-kechoo-guide="bandsaw-blade-maintenance">
<p class="kechoo-guide-summary"><strong>Short answer:</strong> blade life depends on the complete sawing system. A clean blade path, sound guides and wheels, stable tension, effective chip removal, correct coolant, secure clamping, and consistent operating records prevent many failures blamed on the blade.</p>

<h2>Before each shift or production run</h2>
<ul>
<li>Inspect the blade for cracks, damaged or missing teeth, unusual wear, and weld-zone damage.</li>
<li>Check guides, guide bearings or blocks, wheel condition, tracking, and guards.</li>
<li>Confirm the chip brush is present, correctly positioned, and not worn out.</li>
<li>Check vise and bundle clamping for movement.</li>
<li>Verify coolant level, concentration, condition, flow, and nozzle direction where coolant is used.</li>
<li>Confirm tension using the machine manufacturer's approved method.</li>
</ul>

<h2>During operation</h2>
<p>Monitor sound, vibration, chip formation, cut direction, cutting time, coolant delivery, and the finished surface. A gradual change is useful maintenance evidence. Record it before adjusting multiple settings.</p>

<h2>After the run</h2>
<ul>
<li>Stop, isolate, and clean the saw using the approved procedure.</li>
<li>Remove accumulated chips from the brush, guides, wheels, vise, and coolant return.</li>
<li>Inspect the blade and record abnormal wear or damage.</li>
<li>Label a removed reusable blade with its specification, material, approximate use, and reason for removal.</li>
<li>Keep carbon-steel blades dry and protect cutting edges from impact and corrosion.</li>
</ul>

<h2>Periodic machine checks</h2>
<table>
<thead><tr><th>Component</th><th>What to inspect</th><th>Why it affects blade life</th></tr></thead>
<tbody>
<tr><td>Blade guides</td><td>Wear, alignment, clearance, contamination</td><td>Can cause crooked cuts, side wear, and fatigue</td></tr>
<tr><td>Wheels and flanges</td><td>Wear, damage, tracking, chip build-up</td><td>Can twist the band or damage the back edge</td></tr>
<tr><td>Chip brush</td><td>Contact, wear, rotation, missing bristles</td><td>Packed chips can damage teeth and the cut</td></tr>
<tr><td>Coolant system</td><td>Concentration, contamination, flow, nozzle position</td><td>Affects heat, lubrication, and chip removal</td></tr>
<tr><td>Tension system</td><td>Reading accuracy and stability</td><td>Low or unstable tension can cause deviation and fatigue</td></tr>
<tr><td>Vise and supports</td><td>Clamping, alignment, bundle security</td><td>Movement can strip teeth or damage the blade</td></tr>
</tbody>
</table>

<h2>Store blades without damaging them</h2>
<p>Keep blades identified, dry, protected from corrosion, and away from impacts to the teeth and weld. Use trained personnel and the correct protective equipment to coil or uncoil a blade. Do not hang an unidentified used blade where its history and condition can no longer be verified.</p>

<h2>Keep a simple blade-life record</h2>
<p>For each blade, record the SKU or specification, machine, material, section, installation date, operator, speed and feed reference, coolant check, cuts or operating time, removal date, and failure or removal reason. Compare blades only when the operating conditions are reasonably similar.</p>

<h2>When to replace or stop using a blade</h2>
<p>Remove the blade when it is cracked, cannot maintain an acceptable cut, has extensive tooth loss, shows unsafe weld or back-edge damage, or reaches the site's defined wear limit. Do not continue running a damaged blade to obtain one more cut.</p>

<aside><h2>Recurring failure is a system problem until proven otherwise</h2><p>If several blades fail in the same pattern, preserve a failed section and photograph the teeth, blade back, weld, machine plate, workpiece, guides, and chips. Then follow the <a href="/resources/bandsaw-blade-troubleshooting/">diagnostic workflow</a> or <a href="/request-a-quote/">request a technical review</a>.</p></aside>
</article>
HTML;
	}

	private static function troubleshooting_guide_html() {
		return <<<'HTML'
<article class="kechoo-practical-guide" data-kechoo-guide="bandsaw-blade-troubleshooting">
<p class="kechoo-guide-summary"><strong>Short answer:</strong> diagnose the visible failure before changing the blade specification. Record what changed, inspect the machine and workpiece, correct one likely cause at a time, and verify the result under controlled conditions.</p>

<h2>Start with the failure pattern</h2>
<table>
<thead><tr><th>Symptom</th><th>Likely areas to inspect</th><th>First corrective checks</th></tr></thead>
<tbody>
<tr><td><strong>Teeth strip or chip</strong></td><td>Pitch, feed, break-in, clamping, hard spots, chip packing</td><td>Secure the work, confirm pitch, inspect brush and coolant, review initial feed</td></tr>
<tr><td><strong>Cut runs crooked</strong></td><td>Worn blade, guides, tension, feed, tooth damage, work support</td><td>Inspect guide alignment and spacing, tension method, blade wear, and feed</td></tr>
<tr><td><strong>Blade breaks</strong></td><td>Fatigue, excess tension or feed, wheel alignment, guides, blade rubbing, weld area</td><td>Identify the break location and shape; inspect the full blade path before replacing it</td></tr>
<tr><td><strong>Teeth wear too quickly</strong></td><td>Blade direction, break-in, speed, feed, material hardness, coolant</td><td>Confirm installation direction and actual material; review settings and coolant condition</td></tr>
<tr><td><strong>Chips pack in the teeth</strong></td><td>Pitch, brush, coolant, speed/feed balance</td><td>Inspect gullet capacity, brush contact, coolant delivery, and chip evacuation</td></tr>
<tr><td><strong>Vibration or unusual noise</strong></td><td>Pitch, speed, clamping, guide spacing, worn components</td><td>Stop and secure the work; inspect engagement, guides, bearings, and tracking</td></tr>
<tr><td><strong>Rough cut surface</strong></td><td>Worn teeth, pitch, vibration, speed/feed, coolant, work movement</td><td>Check blade condition and clamping before changing parameters</td></tr>
<tr><td><strong>Wear on one side</strong></td><td>Guides, wheels, tooth-set damage, material inclusions</td><td>Inspect alignment, rubbing, wheel flange contact, and the workpiece</td></tr>
</tbody>
</table>

<h2>A repeatable diagnostic workflow</h2>
<ol>
<li><strong>Stop safely.</strong> Isolate the machine when the blade, workpiece, or guarding must be inspected.</li>
<li><strong>Preserve evidence.</strong> Do not discard the failed blade or clean away every chip before taking photos.</li>
<li><strong>Record the last good condition.</strong> Note material, section, blade, machine, approximate use, settings, coolant, and what changed.</li>
<li><strong>Classify the symptom.</strong> Photograph the teeth, both blade sides, back edge, break or weld, cut surface, and chips.</li>
<li><strong>Inspect the sawing system.</strong> Check clamping, guides, wheels, tension, brush, coolant, and blade compatibility.</li>
<li><strong>Change one factor at a time.</strong> Record the correction and compare the next controlled cut.</li>
</ol>

<h2>Was the blade wrong, or did the process change?</h2>
<p>A blade-related problem often follows a change in material grade, hardness, section, bundle arrangement, blade pitch, or construction. A process-related problem often follows changes in guides, tension, wheel condition, clamping, coolant, chip brush, speed, feed, or break-in. Both can occur together, so a new blade alone is not a complete diagnosis.</p>

<h2>What to send KECHOO for technical diagnosis</h2>
<ul>
<li>Machine make, model, and plate photo</li>
<li>Blade label and complete dimensions</li>
<li>Blade construction, tooth form, and TPI</li>
<li>Material grade, hardness if known, shape, and section size</li>
<li>Band speed, feed reference, and break-in method</li>
<li>Coolant type, concentration check, flow, and nozzle position</li>
<li>Approximate cuts or operating time before failure</li>
<li>Clear photos of the teeth, break, weld, blade back, chips, cut surface, guides, and workpiece</li>
</ul>

<h2>When not to continue testing</h2>
<p>Stop when the blade is cracked, the work cannot be clamped, guards are missing, guides or wheels are damaged, the blade repeatedly contacts a flange, or the machine behaves unpredictably. Repair and verify the saw before fitting another blade.</p>

<aside><h2>Get a diagnosis, not a guess</h2><p><a href="/contact/">Contact KECHOO technical support</a> for an existing order, or <a href="/request-a-quote/">submit a cutting application</a> when you need a different blade specification.</p></aside>
</article>
HTML;
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

	public static function enqueue_calculator_assets() {
		if ( ! is_page( array( 'bandsaw-blade-size', 'find-your-blade' ) ) ) {
			return;
		}

		wp_register_style( 'kechoo-machine-fit', false, array(), self::VERSION );
		wp_enqueue_style( 'kechoo-machine-fit' );
		wp_add_inline_style( 'kechoo-machine-fit', self::calculator_css() );

		if ( ! is_page( 'bandsaw-blade-size' ) ) {
			return;
		}

		wp_register_script( 'kechoo-blade-calculator', false, array(), self::VERSION, true );
		wp_enqueue_script( 'kechoo-blade-calculator' );
		wp_add_inline_script( 'kechoo-blade-calculator', self::calculator_javascript() );
	}

	private static function calculator_css() {
		return <<<'CSS'
.kechoo-machine-fit-entry {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 2rem;
  margin-block: 3rem;
  padding: clamp(1.5rem, 4vw, 3rem);
  background: #1b2226;
  color: #fff;
}

.kechoo-machine-fit-entry > div {
  max-width: 60ch;
}

.kechoo-machine-fit-entry h2 {
  margin-block-end: .75rem;
}

.kechoo-machine-fit-entry p:last-child {
  flex: 0 0 auto;
  margin: 0;
}

.kechoo-fit-route {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  margin-block: clamp(2rem, 5vw, 4rem);
  border-block: 1px solid #d8ddde;
}

.kechoo-fit-route a {
  display: grid;
  grid-template-columns: 2rem 1fr;
  gap: .15rem .75rem;
  align-items: center;
  min-height: 6.5rem;
  padding: 1rem;
  text-decoration: none;
}

.kechoo-fit-route a + a {
  border-inline-start: 1px solid #d8ddde;
}

.kechoo-fit-route a:hover strong,
.kechoo-fit-route a:focus-visible strong {
  color: #b51d24;
}

.kechoo-fit-route span {
  grid-row: 1 / span 2;
  color: #b51d24;
  font-size: 1.65rem;
  font-weight: 800;
}

.kechoo-fit-route strong,
.kechoo-fit-route small {
  display: block;
}

.kechoo-fit-route small {
  color: #5a6469;
}

.kechoo-reading-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: clamp(1.5rem, 4vw, 3rem);
  margin-block: 1.5rem 2.5rem;
}

.kechoo-reading-grid section {
  padding-block-start: 1.25rem;
  border-block-start: 3px solid #1b2226;
}

.kechoo-reading-grid dl {
  display: grid;
  grid-template-columns: minmax(8rem, .65fr) 1.35fr;
  gap: .75rem 1rem;
  margin: 0;
}

.kechoo-reading-grid dt {
  font-weight: 700;
}

.kechoo-reading-grid dd {
  margin: 0;
  color: #5a6469;
}

.kechoo-fit-note {
  margin-block: 2rem 3rem;
  padding: 1.25rem 1.5rem;
  border: 1px solid #b9c0c2;
  background: #f1f3f3;
}

.kechoo-calculator {
  width: min(100%, 70rem);
  margin-block: clamp(3rem, 7vw, 6rem);
  background: #f1f3f3;
}

.kechoo-calculator__intro {
  padding: clamp(1.5rem, 4vw, 3rem);
  background: #8e151b;
  color: #fff;
}

.kechoo-calculator__intro h2 {
  max-width: 20ch;
  margin-block-end: 1rem;
  font-size: clamp(1.85rem, 4vw, 3.25rem);
}

.kechoo-calculator__intro p {
  max-width: 65ch;
  margin: 0;
}

.kechoo-calculator__form {
  padding: clamp(1.5rem, 4vw, 3rem);
}

.kechoo-unit-choice {
  display: flex;
  flex-wrap: wrap;
  gap: .75rem 1.5rem;
  margin: 0 0 1.5rem;
  padding: 0;
  border: 0;
}

.kechoo-unit-choice legend {
  width: 100%;
  margin-block-end: .5rem;
  font-weight: 800;
}

.kechoo-unit-choice label {
  display: inline-flex;
  align-items: center;
  gap: .5rem;
  min-height: 2.75rem;
  cursor: pointer;
}

.kechoo-unit-choice input {
  width: 1.25rem;
  height: 1.25rem;
  accent-color: #b51d24;
}

.kechoo-calculator__fields {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 1rem;
}

.kechoo-calculator__fields label {
  display: flex;
  flex-direction: column;
  gap: .45rem;
  font-weight: 700;
}

.kechoo-calculator__fields input {
  width: 100%;
  min-height: 3.25rem;
  padding: .7rem .8rem;
  border: 1px solid #768187;
  border-radius: 0;
  background: #fff;
  color: #1b2226;
}

.kechoo-calculator__fields input[aria-invalid="true"] {
  border-color: #9f151c;
  outline: 2px solid #9f151c;
  outline-offset: 1px;
}

.kechoo-calculator__fields small,
.kechoo-calculator__help {
  color: #465157;
  font-weight: 400;
}

.kechoo-calculator__help {
  margin-block: .8rem 0;
  font-size: .93rem;
}

.kechoo-calculator__actions {
  display: flex;
  flex-wrap: wrap;
  gap: .75rem;
  margin-block-start: 1.5rem;
}

.kechoo-calculator__error {
  margin-block: 1rem 0;
  padding: 1rem;
  border: 1px solid #9f151c;
  background: #fff;
  color: #7b1016;
  font-weight: 700;
}

.kechoo-calculator__result {
  padding: clamp(1.5rem, 4vw, 3rem);
  background: #1b2226;
  color: #fff;
}

.kechoo-calculator__label {
  margin-block-end: .3rem;
  color: #d8ddde;
  font-weight: 700;
}

.kechoo-calculator__value {
  display: flex;
  flex-wrap: wrap;
  align-items: baseline;
  gap: .4rem 1rem;
  margin-block-end: 1.25rem;
}

.kechoo-calculator__value strong {
  color: #fff;
  font-size: clamp(2rem, 7vw, 4.75rem);
  line-height: 1;
  letter-spacing: -.035em;
}

.kechoo-calculator__value span {
  color: #d8ddde;
  font-size: 1.15rem;
}

.kechoo-calculator__result > p:not(.kechoo-calculator__value, .kechoo-calculator__label) {
  max-width: 70ch;
}

.kechoo-calculator__formula {
  padding: 1.25rem clamp(1.5rem, 4vw, 3rem);
  border-block-start: 1px solid #b9c0c2;
}

.kechoo-calculator__formula summary {
  min-height: 2.75rem;
  font-weight: 800;
  cursor: pointer;
}

.kechoo-calculator__formula code {
  overflow-wrap: anywhere;
  font-family: ui-monospace, Consolas, monospace;
}

.kechoo-confirm-list {
  columns: 2;
  column-gap: 3rem;
}

.kechoo-confirm-list li {
  break-inside: avoid;
  margin-block-end: .75rem;
}

@media (max-width: 48rem) {
  .kechoo-machine-fit-entry {
    align-items: flex-start;
    flex-direction: column;
  }

  .kechoo-fit-route,
  .kechoo-reading-grid,
  .kechoo-calculator__fields {
    grid-template-columns: 1fr;
  }

  .kechoo-fit-route a + a {
    border-inline-start: 0;
    border-block-start: 1px solid #d8ddde;
  }

  .kechoo-reading-grid dl {
    grid-template-columns: 1fr;
    gap: .15rem;
  }

  .kechoo-reading-grid dd + dt {
    margin-block-start: .65rem;
  }

  .kechoo-confirm-list {
    columns: 1;
  }
}
CSS;
	}

	private static function calculator_javascript() {
		return <<<'JS'
(() => {
  const format = (value, digits) => new Intl.NumberFormat(undefined, {
    maximumFractionDigits: digits,
    minimumFractionDigits: digits,
  }).format(value);

  document.querySelectorAll('[data-kechoo-blade-calculator]').forEach((form) => {
    const result = form.parentElement.querySelector('[data-kechoo-calculator-result]');
    const error = form.querySelector('[data-kechoo-calculator-error]');
    const primary = result.querySelector('[data-kechoo-result-primary]');
    const secondary = result.querySelector('[data-kechoo-result-secondary]');
    const formula = result.querySelector('[data-kechoo-result-formula]');
    const copyButton = result.querySelector('[data-kechoo-copy-result]');

    const fields = {
      wheelOne: form.elements.wheel_one,
      wheelTwo: form.elements.wheel_two,
      center: form.elements.center_distance,
    };

    const showError = (message, invalidFields = []) => {
      error.textContent = message;
      error.hidden = false;
      result.hidden = true;
      Object.values(fields).forEach((field) => field.removeAttribute('aria-invalid'));
      invalidFields.forEach((field) => field.setAttribute('aria-invalid', 'true'));
      if (invalidFields[0]) invalidFields[0].focus();
    };

    form.addEventListener('input', () => {
      error.hidden = true;
      Object.values(fields).forEach((field) => field.removeAttribute('aria-invalid'));
    });

    form.addEventListener('submit', (event) => {
      event.preventDefault();
      const d1 = Number.parseFloat(fields.wheelOne.value);
      const d2 = Number.parseFloat(fields.wheelTwo.value);
      const center = Number.parseFloat(fields.center.value);
      const invalidFields = Object.values(fields).filter((field) => {
        const value = Number.parseFloat(field.value);
        return !Number.isFinite(value) || value <= 0;
      });

      if (invalidFields.length) {
        showError('Enter a positive number for both wheel diameters and the center-to-center distance.', invalidFields);
        return;
      }

      if (center <= (d1 + d2) / 2) {
        showError('The wheel centers must be farther apart than the combined wheel radii. Check that you measured axle center to axle center.', [fields.center]);
        return;
      }

      const estimated = (2 * center) + (Math.PI * (d1 + d2) / 2) + (((d1 - d2) ** 2) / (4 * center));
      const unit = form.querySelector('input[name="kechoo_calc_unit"]:checked').value;
      const lengthMm = unit === 'mm' ? estimated : estimated * 25.4;
      const lengthIn = lengthMm / 25.4;
      const isEqual = Math.abs(d1 - d2) < 0.000001;

      primary.textContent = unit === 'mm' ? `${format(lengthMm, 1)} mm` : `${format(lengthIn, 2)} in`;
      secondary.textContent = unit === 'mm' ? `(${format(lengthIn, 2)} in)` : `(${format(lengthMm, 1)} mm)`;
      formula.textContent = isEqual
        ? `Equal-wheel estimate: 2 × ${format(center, 2)} + π × ${format(d1, 2)} ${unit}.`
        : `Unequal-wheel estimate using D1 ${format(d1, 2)}, D2 ${format(d2, 2)}, and center distance ${format(center, 2)} ${unit}.`;
      result.dataset.copyText = `Estimated bandsaw blade length: ${format(lengthMm, 1)} mm (${format(lengthIn, 2)} in). Wheel 1: ${format(d1, 2)} ${unit}; Wheel 2: ${format(d2, 2)} ${unit}; Center distance: ${format(center, 2)} ${unit}. Verify against the machine manual and tension range before ordering.`;
      error.hidden = true;
      result.hidden = false;
      result.scrollIntoView({ behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth', block: 'nearest' });
    });

    form.addEventListener('reset', () => {
      error.hidden = true;
      result.hidden = true;
      Object.values(fields).forEach((field) => field.removeAttribute('aria-invalid'));
    });

    copyButton.addEventListener('click', async () => {
      const originalText = copyButton.textContent;
      try {
        await navigator.clipboard.writeText(result.dataset.copyText);
        copyButton.textContent = 'Result copied';
      } catch (copyError) {
        copyButton.textContent = 'Copy unavailable';
      }
      window.setTimeout(() => {
        copyButton.textContent = originalText;
      }, 1800);
    });
  });
})();
JS;
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

	public static function print_guide_schema() {
		if ( is_admin() || ! is_singular( 'page' ) ) {
			return;
		}

		$current_slug = get_post_field( 'post_name', get_queried_object_id() );
		$guides       = self::guide_pages();
		if ( ! isset( $guides[ $current_slug ] ) ) {
			return;
		}

		$url    = self::guide_url( $current_slug );
		$schema = array(
			'@context'         => 'https://schema.org',
			'@type'            => 'Article',
			'@id'              => $url . '#article',
			'mainEntityOfPage' => $url,
			'headline'         => $guides[ $current_slug ]['title'],
			'description'      => $guides[ $current_slug ]['description'],
			'datePublished'    => get_the_date( DATE_W3C, get_queried_object_id() ),
			'dateModified'     => get_the_modified_date( DATE_W3C, get_queried_object_id() ),
			'author'           => array(
				'@type' => 'Organization',
				'name'  => 'KECHOO Technical Team',
				'url'   => home_url( '/about/' ),
			),
			'publisher'        => array(
				'@type' => 'Organization',
				'@id'   => home_url( '/#organization' ),
				'name'  => 'KECHOO',
				'url'   => home_url( '/' ),
			),
		);

		echo "\n<script type=\"application/ld+json\" data-kechoo-geo=\"practical-guide\">";
		echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
		echo "</script>\n";
	}
}

Kechoo_GEO_Content::init();
