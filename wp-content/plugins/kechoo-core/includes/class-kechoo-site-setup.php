<?php
/**
 * Site content migrations and compact product administration.
 *
 * @package KechooCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Kechoo_Site_Setup {
	const CONTENT_VERSION = '1.4.0';

	public static function init() {
		add_action( 'init', array( __CLASS__, 'maybe_ensure_pages' ), 30 );
		add_action( 'admin_menu', array( __CLASS__, 'add_admin_guide_page' ) );
		add_action( 'template_redirect', array( __CLASS__, 'redirect_legacy_products_path' ) );
		add_filter( 'manage_edit-product_columns', array( __CLASS__, 'product_columns' ), 40 );
		add_action( 'manage_product_posts_custom_column', array( __CLASS__, 'product_column_content' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_product_styles' ) );
		add_action( 'admin_notices', array( __CLASS__, 'chinese_language_notice' ) );
		add_filter( 'gettext_kechoo-core', array( __CLASS__, 'translate_admin_string' ), 10, 3 );
		add_filter( 'gettext', array( __CLASS__, 'translate_common_admin_string' ), 20, 3 );
	}

	public static function maybe_ensure_pages() {
		if ( self::CONTENT_VERSION === get_option( 'kechoo_site_content_version' ) ) {
			return;
		}

		self::ensure_pages();
		update_option( 'kechoo_site_content_version', self::CONTENT_VERSION, false );
		flush_rewrite_rules( false );
	}

	public static function ensure_pages() {
		$pages = array(
			'find-your-blade' => array(
				'title'   => 'Find Your Blade',
				'content' => '[kechoo_blade_selector]',
			),
			'request-a-quote' => array(
				'title'   => 'Request a Quote',
				'content' => '[kechoo_rfq_form]',
			),
			'distributors'    => array(
				'title'   => 'Distributors',
				'content' => '<p>Discuss bulk supply, OEM packaging, regional demand, and long-term cooperation with KECHOO.</p>[kechoo_rfq_form buyer_type="distributor"]',
			),
			'applications' => array(
				'title'   => 'Applications',
				'content' => '<h2>Choose by what you cut</h2><p>Start with the workpiece, then narrow the blade technology and specification.</p><h3>Food &amp; Bone</h3><p>Hardened blades for butcher shops, meat processing, poultry, fish, and frozen-food production.</p><p><a href="/shop/?kechoo_application=food-bone">View Food &amp; Bone blades</a></p><h3>Wood</h3><p>Hardened blades for general woodworking, furniture production, resawing, and sawmill applications.</p><p><a href="/shop/?kechoo_application=wood">View Wood blades</a></p><h3>Metal</h3><p>Bi-metal and carbide blades for fabrication, steel service centers, and demanding production cutting.</p><p><a href="/shop/?kechoo_application=metal">View Metal blades</a></p>',
			),
			'technology' => array(
				'title'   => 'Blade Technology',
				'content' => '<h2>Match the blade construction to the work</h2><h3>Hardened</h3><p>High-carbon steel blades with hardened teeth for food, bone, wood, and general-purpose cutting.</p><h3>Bi-Metal</h3><p>Flexible alloy backing joined to an M42 high-speed steel tooth edge for dependable metal cutting.</p><h3>Carbide</h3><p>Carbide-tipped blades for rigid machines, abrasive materials, high alloys, and large sections.</p><p><a href="/find-your-blade/">Find the right blade</a></p>',
			),
			'resources' => array(
				'title'   => 'Technical Resources',
				'content' => '<h2>Selection and operating guidance</h2><p>Use the blade selector for verified catalog matches. For an unknown machine, custom length, or difficult material, send the blade label, dimensions, and workpiece details to the KECHOO technical team.</p><ul><li>Confirm blade length, width, and thickness before ordering.</li><li>Select tooth pitch from the workpiece size and section type.</li><li>Use the correct break-in procedure for bi-metal and carbide blades.</li><li>Check guides, tension, coolant, and chip brush condition when troubleshooting blade life.</li></ul><p><a href="/request-a-quote/">Request technical help</a></p>',
			),
			'about' => array(
				'title'   => 'About KECHOO',
				'content' => '<h2>Choose Better Cutting</h2><p>KECHOO focuses on industrial bandsaw blades for food processing, woodworking, and metal cutting. We combine controlled manufacturing processes with practical selection support for factories and distributors worldwide.</p><p>Precision-made blades, reliable cutting performance.</p><p><a href="/contact/">Contact KECHOO</a></p>',
			),
			'shipping' => array(
				'title'   => 'Shipping from China',
				'content' => '<h2>International shipping</h2><p>KECHOO stock products dispatch from China. Available services, delivery estimates, and charges depend on blade dimensions, order weight, destination, and the configured shipping zone.</p><p>Enter the destination at checkout to view available online rates. For destinations without an online method, or for volume orders, request a freight quotation.</p><p>Import duties, taxes, customs brokerage charges, and destination fees are normally the buyer’s responsibility unless the quotation states otherwise.</p><p><a href="/customs-duties/">Read customs and duties information</a></p>',
			),
			'contact' => array(
				'title'   => 'Contact KECHOO',
				'content' => '<h2>Tell us what you need to cut</h2><p>Include the application, workpiece material, machine model, blade dimensions, and expected quantity. A blade label or photo can help us confirm the specification.</p>[kechoo_rfq_form]',
			),
			'terms' => array(
				'title'   => 'Terms and Conditions',
				'content' => '<h2>Terms and Conditions</h2><p><strong>Draft notice:</strong> These terms provide a practical starting point for the KECHOO test site and should be reviewed before production launch.</p><h3>Orders</h3><p>Online orders are accepted for listed in-stock specifications. Custom sizes, distributor orders, OEM packaging, and unlisted applications should be confirmed by written quotation before production or dispatch.</p><h3>Prices and payment</h3><p>Prices are shown in USD unless stated otherwise. Payment methods, payment timing, and any bank or platform charges are confirmed at checkout or in the written quotation.</p><h3>Product information</h3><p>KECHOO aims to keep product dimensions, compatibility, stock, lead time, and application information accurate. Buyers remain responsible for confirming blade length, width, thickness, tooth pitch, machine compatibility, and cutting conditions before use.</p><h3>Shipping and risk</h3><p>Shipping options and estimated delivery times depend on destination, order size, customs processing, and carrier availability. Import duties, taxes, customs clearance, and destination charges are handled under the customs and duties policy unless a quotation states otherwise.</p><h3>Returns and claims</h3><p>Returns, shortages, visible damage, wrong specifications, and quality claims should be reported promptly with order details, photos, labels, and application information. See the returns and refunds policy for the working process.</p><h3>Limitation</h3><p>Before launch, replace this draft with the approved commercial terms used by KECHOO for international sales.</p>',
			),
			'privacy-policy' => array(
				'title'   => 'Privacy Policy',
				'content' => '<h2>Privacy Policy</h2><p><strong>Draft notice:</strong> This policy is a practical starting point for an international B2B WordPress and WooCommerce site. Review it before production launch.</p><h3>Information we collect</h3><p>We collect information submitted through quote forms, checkout, account registration, email, and customer support. This may include name, company, country, email address, shipping details, order details, product requirements, uploaded drawings or photos, and technical application information.</p><h3>How we use information</h3><p>We use this information to answer inquiries, prepare quotations, process orders, arrange shipping, provide technical support, improve the website, prevent spam and abuse, and maintain business records.</p><h3>Payments and shipping</h3><p>Payment providers, logistics providers, customs brokers, hosting providers, analytics tools, and email providers may process information when needed to operate the site and fulfill orders.</p><h3>Analytics and cookies</h3><p>The production site may use analytics and essential cookies for site operation, performance measurement, fraud prevention, and checkout. Analytics should be configured before launch and disclosed here.</p><h3>Retention</h3><p>We keep business records for as long as needed for order support, legal, accounting, warranty, and commercial purposes.</p><h3>Contact</h3><p>For privacy questions, contact KECHOO through the contact page or the official business email address configured for kechoo.com.</p>',
			),
			'returns-refunds' => array(
				'title'   => 'Returns and Refunds',
				'content' => '<h2>Returns and Refunds</h2><p><strong>Draft notice:</strong> This policy should be checked against KECHOO’s final sales process before production launch.</p><h3>Before ordering</h3><p>Bandsaw blades are specification-sensitive products. Confirm length, width, thickness, tooth pitch, blade technology, machine compatibility, and cutting material before ordering.</p><h3>Stock products</h3><p>For standard in-stock items, contact KECHOO promptly if the product is damaged in transit, incorrectly shipped, or materially different from the confirmed order. Provide order number, photos, packaging, labels, and a description of the issue.</p><h3>Custom products</h3><p>Custom-length, custom-tooth, OEM, and special-order blades are normally not returnable unless KECHOO supplied the wrong specification or confirms a quality issue.</p><h3>Quality claims</h3><p>Quality reviews may require blade photos, label photos, machine model, material being cut, cutting parameters, coolant condition, break-in process, and blade failure details. Do not discard the blade before the claim is reviewed.</p><h3>Refund method</h3><p>Approved refunds, replacements, or credits are handled according to the original payment method or written quotation.</p>',
			),
			'customs-duties' => array(
				'title'   => 'Customs and Duties',
				'content' => '<h2>Customs and Duties</h2><p>KECHOO ships stock products from China unless otherwise stated. International shipments may be subject to import duties, VAT/GST, customs brokerage charges, destination handling fees, inspection fees, or other local charges.</p><p>Unless a quotation specifically states otherwise, these destination charges are the buyer’s responsibility and are not included in the product price or online shipping fee.</p><p>For distributor, bulk, OEM, or project orders, request a written quotation so shipping terms, documentation, packaging, and Incoterms can be confirmed before payment.</p>',
			),
		);

		foreach ( $pages as $slug => $page ) {
			$existing_page = get_page_by_path( $slug, OBJECT, 'page' );
			if ( $existing_page ) {
				if ( get_post_meta( $existing_page->ID, '_kechoo_generated_page', true ) || 'privacy-policy' === $slug ) {
					wp_update_post(
						array(
							'ID'           => $existing_page->ID,
							'post_title'   => $page['title'],
							'post_content' => $page['content'],
							'post_status'  => 'publish',
						)
					);
					update_post_meta( $existing_page->ID, '_kechoo_generated_page', self::CONTENT_VERSION );
				}
				continue;
			}

			$page_id = wp_insert_post(
				array(
					'post_title'   => $page['title'],
					'post_name'    => $slug,
					'post_content' => $page['content'],
					'post_status'  => 'publish',
					'post_type'    => 'page',
				),
				true
			);

			if ( ! is_wp_error( $page_id ) ) {
				update_post_meta( $page_id, '_kechoo_generated_page', self::CONTENT_VERSION );
			}
		}

		$privacy_page = get_page_by_path( 'privacy-policy', OBJECT, 'page' );
		if ( $privacy_page ) {
			update_option( 'wp_page_for_privacy_policy', $privacy_page->ID );
		}
	}

	public static function redirect_legacy_products_path() {
		$request_path = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH ) : '';
		$request_path = trim( (string) $request_path, '/' );

		if ( 'products' !== $request_path ) {
			return;
		}

		$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
		wp_safe_redirect( $shop_url, 301 );
		exit;
	}

	public static function add_admin_guide_page() {
		add_submenu_page(
			'edit.php?post_type=product',
			'KECHOO 上品指南',
			'KECHOO 上品指南',
			'edit_products',
			'kechoo-product-guide',
			array( __CLASS__, 'render_admin_guide_page' )
		);
	}

	public static function render_admin_guide_page() {
		if ( ! current_user_can( 'edit_products' ) ) {
			wp_die( esc_html__( 'Sorry, you are not allowed to access this page.' ) );
		}

		$links = array(
			'add_product' => admin_url( 'post-new.php?post_type=product' ),
			'products'    => admin_url( 'edit.php?post_type=product' ),
			'rfqs'        => admin_url( 'edit.php?post_type=kechoo_rfq' ),
			'shop'        => home_url( '/shop/' ),
		);
		?>
		<div class="wrap kechoo-admin-guide">
			<h1>KECHOO 上品指南</h1>
			<p class="description">给中文运营同事使用：轻量公开版先保证产品能被看懂、能筛选、能询盘。价格、库存、在线支付和精确运费可后续补齐。</p>

			<div class="kechoo-admin-guide__actions">
				<a class="button button-primary" href="<?php echo esc_url( $links['add_product'] ); ?>">添加产品</a>
				<a class="button" href="<?php echo esc_url( $links['products'] ); ?>">查看全部产品</a>
				<a class="button" href="<?php echo esc_url( $links['rfqs'] ); ?>">查看询盘</a>
				<a class="button" href="<?php echo esc_url( $links['shop'] ); ?>" target="_blank" rel="noopener">预览商店</a>
			</div>

			<div class="kechoo-admin-guide__grid">
				<section>
					<h2>上架热卖规格的顺序</h2>
					<ol>
						<li><strong>标题：</strong>建议使用 “Blade type + length × width × thickness — TPI”，例如 M42 Bi-Metal Blade 4115 × 34 × 1.10 mm — 3/4 TPI。</li>
						<li><strong>SKU：</strong>保持唯一，方便报价、库存和后续导入。</li>
						<li><strong>价格与库存：</strong>轻量公开版可以先留空或只内部维护；前台默认显示 Quote on request，不直接成交。</li>
						<li><strong>分类：</strong>至少选择应用场景、锯条工艺、切割材料。机器适配不确定时可以先留空。</li>
						<li><strong>KECHOO 技术参数：</strong>填写长度、宽度、厚度、TPI、齿形、材料、发货时间、是否支持定制。</li>
						<li><strong>图片：</strong>先用统一风格主图；后续再补真实包装、齿形、切割场景图。</li>
					</ol>
				</section>

				<section>
					<h2>分类怎么选</h2>
					<ul>
						<li><strong>肉骨切割：</strong>应用选 Food &amp; Bone，工艺通常选 Hardened。</li>
						<li><strong>木材切割：</strong>应用选 Wood，工艺通常选 Hardened。</li>
						<li><strong>金属切割：</strong>应用选 Metal，常规金属选 Bi-Metal，高合金、大截面或高要求场景选 Carbide。</li>
						<li><strong>不确定：</strong>先不要硬归类，把参数发给技术同事确认后再发布。</li>
					</ul>
				</section>

				<section>
					<h2>发布前检查</h2>
					<ul>
						<li>产品页能看到发货说明、技术参数和 Request price and availability。</li>
						<li>商店列表能看到应用、工艺、尺寸和 TPI，不需要点开才知道规格。</li>
						<li>前台不要出现测试价格、Add to cart 或让客户误以为可以直接付款。</li>
						<li>定制、批量、经销商和不确定规格全部优先走询盘。</li>
					</ul>
				</section>

				<section>
					<h2>后台语言建议</h2>
					<p>正式上线时建议给中文同事创建单独账号，角色使用 Shop Manager 或自定义运营角色，并把该用户的语言设置为简体中文。这样前台仍是英文，后台按人显示中文。</p>
				</section>
			</div>
		</div>
		<?php
	}

	public static function product_columns( $columns ) {
		unset( $columns['product_tag'], $columns['product_brand'] );

		$compact = array();
		foreach ( $columns as $key => $label ) {
			$compact[ $key ] = $label;
			if ( 'sku' === $key ) {
				$compact['kechoo_profile'] = self::is_chinese_admin() ? '锯条分类' : __( 'Blade profile', 'kechoo-core' );
			}
		}

		return $compact;
	}

	public static function product_column_content( $column, $post_id ) {
		if ( 'kechoo_profile' !== $column ) {
			return;
		}

		$application = self::first_term_name( $post_id, 'kechoo_application' );
		$technology  = self::first_term_name( $post_id, 'kechoo_blade_technology' );
		$material    = self::first_term_name( $post_id, 'kechoo_cut_material' );

		echo '<div class="kechoo-admin-profile">';
		if ( $application || $technology ) {
			echo '<strong>' . esc_html( implode( ' · ', array_filter( array( $application, $technology ) ) ) ) . '</strong>';
		}
		if ( $material ) {
			echo '<span>' . esc_html( $material ) . '</span>';
		}
		echo '</div>';
	}

	private static function first_term_name( $post_id, $taxonomy ) {
		$terms = get_the_terms( $post_id, $taxonomy );
		return $terms && ! is_wp_error( $terms ) ? $terms[0]->name : '';
	}

	public static function admin_product_styles( $hook_suffix ) {
		$screen = get_current_screen();
		$is_product_list = 'edit.php' === $hook_suffix && $screen && 'product' === $screen->post_type;
		$is_guide_page   = $screen && 'product_page_kechoo-product-guide' === $screen->id;

		if ( ! $is_product_list && ! $is_guide_page ) {
			return;
		}

		wp_register_style( 'kechoo-admin-products', false, array(), KECHOO_CORE_VERSION );
		wp_enqueue_style( 'kechoo-admin-products' );
		wp_add_inline_style(
			'kechoo-admin-products',
			'.post-type-product .wp-list-table .column-name{width:30%}.post-type-product .wp-list-table .column-sku{width:13%}.post-type-product .wp-list-table .column-is_in_stock{width:9%}.post-type-product .wp-list-table .column-price{width:8%}.post-type-product .wp-list-table .column-kechoo_profile{width:18%}.kechoo-admin-profile{display:flex;flex-direction:column;gap:3px;line-height:1.35}.kechoo-admin-profile strong{font-weight:600}.kechoo-admin-profile span{color:#50575e}.kechoo-admin-guide{max-width:1120px}.kechoo-admin-guide>.description{max-width:720px;font-size:14px}.kechoo-admin-guide__actions{display:flex;flex-wrap:wrap;gap:8px;margin:18px 0 22px}.kechoo-admin-guide__grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:16px}.kechoo-admin-guide section{padding:18px 20px;border:1px solid #dcdcde;background:#fff}.kechoo-admin-guide h2{margin:0 0 12px;font-size:16px}.kechoo-admin-guide ol,.kechoo-admin-guide ul{margin-left:1.2em}.kechoo-admin-guide li+li{margin-top:8px}.kechoo-admin-guide strong{color:#1d2327}'
		);
	}

	public static function translate_admin_string( $translation, $text, $domain ) {
		if ( ! self::is_chinese_admin() ) {
			return $translation;
		}

		$translations = array(
			'Application'                    => '应用场景',
			'Applications'                   => '应用场景',
			'Blade technology'               => '锯条工艺',
			'Blade technologies'             => '锯条工艺',
			'Cut material'                   => '切割材料',
			'Cut materials'                  => '切割材料',
			'Machine compatibility'          => '适配机器',
			'RFQ requests'                   => '询价请求',
			'RFQ request'                    => '询价请求',
			'KECHOO RFQs'                    => 'KECHOO 询价',
			'Request'                        => '询价单',
			'Reference'                      => '编号',
			'Company'                        => '公司',
			'Email'                          => '邮箱',
			'Buyer type'                     => '买家类型',
			'KECHOO technical specifications'=> 'KECHOO 技术参数',
		);

		return isset( $translations[ $text ] ) ? $translations[ $text ] : $translation;
	}

	public static function translate_common_admin_string( $translation, $text, $domain ) {
		if ( ! self::is_chinese_admin() ) {
			return $translation;
		}

		$translations = array(
			'Dashboard'             => '仪表盘',
			'Posts'                 => '文章',
			'Media'                 => '媒体库',
			'Pages'                 => '页面',
			'Comments'              => '评论',
			'Products'              => '产品',
			'All Products'          => '全部产品',
			'Add new product'       => '添加产品',
			'Categories'            => '分类',
			'Attributes'            => '属性',
			'Reviews'               => '评价',
			'WooCommerce'           => 'WooCommerce 商店',
			'Orders'                => '订单',
			'Customers'             => '客户',
			'Coupons'               => '优惠券',
			'Reports'               => '报表',
			'Settings'              => '设置',
			'Status'                => '状态',
			'Payments'              => '支付',
			'Analytics'             => '数据分析',
			'Marketing'             => '营销',
			'Appearance'            => '外观',
			'Plugins'               => '插件',
			'Users'                 => '用户',
			'Tools'                 => '工具',
			'Name'                  => '名称',
			'Stock'                 => '库存',
			'Price'                 => '价格',
			'Date'                  => '日期',
			'Tags'                  => '标签',
			'Published'             => '已发布',
			'Bulk actions'          => '批量操作',
			'Apply'                 => '应用',
			'Filter'                => '筛选',
			'Search products'       => '搜索产品',
			'Select a category'     => '选择分类',
			'Filter by product type'=> '按产品类型筛选',
			'Filter by stock status'=> '按库存状态筛选',
			'Edit'                  => '编辑',
			'Trash'                 => '回收站',
			'View'                  => '查看',
		);

		return isset( $translations[ $text ] ) ? $translations[ $text ] : $translation;
	}

	public static function chinese_language_notice() {
		if ( ! self::is_chinese_admin() || in_array( 'zh_CN', get_available_languages(), true ) ) {
			return;
		}

		echo '<div class="notice notice-info is-dismissible"><p><strong>KECHOO 后台中文：</strong>常用产品与商店菜单已经提供中文。完整 WordPress 中文包需要服务器联网安装，可由管理员在“设置 / Settings → 常规 / General → 站点语言 / Site Language”中选择“简体中文”。英文前台不会因此改变。</p></div>';
	}

	private static function is_chinese_admin() {
		return is_admin() && 0 === strpos( get_user_locale(), 'zh_' );
	}
}
