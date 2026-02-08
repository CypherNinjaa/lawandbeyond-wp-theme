<?php
/**
 * Demo Content Importer for Law and Beyond.
 *
 * Adds a "Demo Content" admin page under Appearance with a one-click
 * button to generate sample posts across all categories so the admin
 * can see the theme in action immediately.
 *
 * @package LawAndBeyond
 */

/**
 * Register the admin menu page.
 */
function lawandbeyond_demo_content_menu() {
	add_theme_page(
		__( 'Import Demo Content', 'lawandbeyond' ),
		__( 'Demo Content', 'lawandbeyond' ),
		'manage_options',
		'lawandbeyond-demo-content',
		'lawandbeyond_demo_content_page'
	);
}
add_action( 'admin_menu', 'lawandbeyond_demo_content_menu' );

/**
 * Handle the AJAX / form submission for importing demo content.
 */
function lawandbeyond_handle_demo_import() {
	if ( ! isset( $_POST['lawandbeyond_import_demo_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( $_POST['lawandbeyond_import_demo_nonce'], 'lawandbeyond_import_demo' ) ) {
		wp_die( __( 'Security check failed.', 'lawandbeyond' ) );
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to do this.', 'lawandbeyond' ) );
	}

	$result = lawandbeyond_import_demo_posts();

	set_transient( 'lawandbeyond_demo_result', $result, 60 );
	wp_safe_redirect( admin_url( 'themes.php?page=lawandbeyond-demo-content&imported=1' ) );
	exit;
}
add_action( 'admin_init', 'lawandbeyond_handle_demo_import' );

/**
 * Render the Demo Content admin page.
 */
function lawandbeyond_demo_content_page() {
	$imported = isset( $_GET['imported'] ) && $_GET['imported'] == '1';
	$result   = get_transient( 'lawandbeyond_demo_result' );
	if ( $imported && $result ) {
		delete_transient( 'lawandbeyond_demo_result' );
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Law and Beyond — Demo Content', 'lawandbeyond' ); ?></h1>

		<?php if ( $imported && $result ) : ?>
			<div class="notice notice-success is-dismissible">
				<p>
					<strong><?php esc_html_e( 'Demo content imported successfully!', 'lawandbeyond' ); ?></strong><br>
					<?php printf(
						/* translators: 1: number of posts, 2: number of categories */
						esc_html__( 'Created %1$d demo posts across %2$d categories.', 'lawandbeyond' ),
						intval( $result['posts'] ),
						intval( $result['categories'] )
					); ?>
				</p>
			</div>
		<?php endif; ?>

		<div class="card" style="max-width:700px;padding:20px 25px;">
			<h2 style="margin-top:0;"><?php esc_html_e( 'Import Demo Posts', 'lawandbeyond' ); ?></h2>
			<p><?php esc_html_e( 'Click the button below to create sample posts in every category. This helps you see how the theme looks with real content. You can edit or delete these posts anytime.', 'lawandbeyond' ); ?></p>

			<table class="widefat" style="margin-bottom:20px;">
				<thead>
					<tr>
						<th><?php esc_html_e( 'What will be created', 'lawandbeyond' ); ?></th>
						<th><?php esc_html_e( 'Details', 'lawandbeyond' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><strong><?php esc_html_e( 'Categories', 'lawandbeyond' ); ?></strong></td>
						<td><?php esc_html_e( 'Latest News, High Court, Supreme Court, Legal Updates, Monthly Recap, Blog, Other Courts, Know Your Courts', 'lawandbeyond' ); ?></td>
					</tr>
					<tr>
						<td><strong><?php esc_html_e( 'Demo Posts', 'lawandbeyond' ); ?></strong></td>
						<td><?php esc_html_e( '4 posts per category (32 total) — realistic legal news headlines', 'lawandbeyond' ); ?></td>
					</tr>
					<tr>
						<td><strong><?php esc_html_e( 'Menus', 'lawandbeyond' ); ?></strong></td>
						<td><?php esc_html_e( 'Primary & Footer menus will be created if they don\'t exist', 'lawandbeyond' ); ?></td>
					</tr>
					<tr>
						<td><strong><?php esc_html_e( 'Pages', 'lawandbeyond' ); ?></strong></td>
						<td><?php esc_html_e( 'About Us, Contact Us, Privacy Policy, Terms & Conditions, Disclaimer, Refunds Policy', 'lawandbeyond' ); ?></td>
					</tr>
				</tbody>
			</table>

			<form method="post">
				<?php wp_nonce_field( 'lawandbeyond_import_demo', 'lawandbeyond_import_demo_nonce' ); ?>
				<p>
					<button type="submit" class="button button-primary button-hero" onclick="return confirm('<?php esc_attr_e( 'This will create demo posts, categories, menus and pages. Continue?', 'lawandbeyond' ); ?>');">
						<span class="dashicons dashicons-download" style="vertical-align:middle;margin-right:5px;"></span>
						<?php esc_html_e( 'Import Demo Content Now', 'lawandbeyond' ); ?>
					</button>
				</p>
			</form>

			<p class="description" style="margin-top:10px;">
				<?php esc_html_e( 'Tip: After importing, go to Appearance → Customize → Front Page Sections to configure which categories appear on the homepage.', 'lawandbeyond' ); ?>
			</p>
		</div>

		<div class="card" style="max-width:700px;padding:20px 25px;margin-top:20px;">
			<h2 style="margin-top:0;"><?php esc_html_e( 'Quick Setup Guide', 'lawandbeyond' ); ?></h2>
			<ol style="font-size:14px;line-height:1.8;">
				<li><?php esc_html_e( 'Click "Import Demo Content Now" above to populate the site.', 'lawandbeyond' ); ?></li>
				<li><?php esc_html_e( 'Go to Appearance → Menus to review/edit the Primary and Footer menus.', 'lawandbeyond' ); ?></li>
				<li><?php esc_html_e( 'Go to Appearance → Customize → Front Page Sections to choose categories for each homepage section.', 'lawandbeyond' ); ?></li>
				<li><?php esc_html_e( 'Set Social Links in Appearance → Customize → Social Links.', 'lawandbeyond' ); ?></li>
				<li><?php esc_html_e( 'Upload your logo in Appearance → Customize → Site Identity.', 'lawandbeyond' ); ?></li>
				<li><?php esc_html_e( 'Add widgets to the sidebar in Appearance → Widgets.', 'lawandbeyond' ); ?></li>
			</ol>
		</div>
	</div>
	<?php
}

/**
 * Generate demo posts for each category.
 *
 * @return array Counts of posts and categories created.
 */
function lawandbeyond_import_demo_posts() {
	// Make sure categories and menus exist first.
	if ( function_exists( 'lawandbeyond_create_default_categories' ) ) {
		lawandbeyond_create_default_categories();
	}
	if ( function_exists( 'lawandbeyond_create_default_pages' ) ) {
		lawandbeyond_create_default_pages();
	}
	if ( function_exists( 'lawandbeyond_create_default_menus' ) ) {
		lawandbeyond_create_default_menus();
	}
	if ( function_exists( 'lawandbeyond_set_default_customizer_options' ) ) {
		lawandbeyond_set_default_customizer_options();
	}

	$demo_data = array(
		'latest-news' => array(
			array(
				'title'   => 'Government Introduces New Data Privacy Bill in Parliament',
				'content' => 'The central government tabled a comprehensive data privacy bill aimed at regulating the collection, storage, and processing of citizens\' personal data. Legal experts have called it a landmark move towards digital rights protection in India.',
			),
			array(
				'title'   => 'Bar Council Announces Revised Rules for Advocate Registration',
				'content' => 'The Bar Council of India has issued revised guidelines for advocate enrollment, streamlining the verification process and introducing digital certificates for new practitioners across the country.',
			),
			array(
				'title'   => 'Law Commission Submits Report on Uniform Civil Code',
				'content' => 'The Law Commission of India has submitted its latest report examining the feasibility and constitutional implications of implementing a Uniform Civil Code across the nation.',
			),
			array(
				'title'   => 'NCLAT Upholds CCI Order Against Major Tech Company',
				'content' => 'The National Company Law Appellate Tribunal has upheld the Competition Commission of India\'s penalty order against a leading technology company for anti-competitive practices in the digital marketplace.',
			),
		),
		'supreme-court' => array(
			array(
				'title'   => 'Supreme Court Expands Right to Privacy in Landmark Ruling',
				'content' => 'In a unanimous decision, the Supreme Court has broadened the scope of the right to privacy, holding that digital surveillance by state agencies must conform to strict proportionality tests established in earlier constitutional rulings.',
			),
			array(
				'title'   => 'SC Issues Guidelines on Bail for Undertrials in Minor Offences',
				'content' => 'The Supreme Court has directed all trial courts to follow updated guidelines for granting bail to undertrial prisoners charged with offences carrying a maximum sentence of less than seven years.',
			),
			array(
				'title'   => 'Constitutional Bench Hears Arguments on Electoral Bond Transparency',
				'content' => 'A five-judge constitutional bench of the Supreme Court has commenced hearing arguments on the validity of electoral bonds and their impact on democratic transparency and accountability.',
			),
			array(
				'title'   => 'Supreme Court Orders Environmental Impact Review for Industrial Zones',
				'content' => 'The apex court has ordered a comprehensive environmental review of industrial zones near ecologically sensitive areas, directing state governments to submit compliance reports within 90 days.',
			),
		),
		'high-court' => array(
			array(
				'title'   => 'Delhi HC Strengthens Protection Against Cyberbullying in Schools',
				'content' => 'The Delhi High Court has issued sweeping directions to educational institutions to implement anti-cyberbullying policies, ruling that schools have a duty of care extending to students\' online interactions.',
			),
			array(
				'title'   => 'Bombay High Court Upholds Media\'s Right to Report on Court Proceedings',
				'content' => 'Bombay High Court has upheld the right of media organizations to report on ongoing court proceedings, striking down restrictions imposed by a lower court as unconstitutional censorship.',
			),
			array(
				'title'   => 'Allahabad HC Sets Guidelines for Fast-Track Court Timelines',
				'content' => 'The Allahabad High Court has issued comprehensive guidelines mandating time-bound disposal of cases in designated fast-track courts, aiming to reduce pendency across Uttar Pradesh.',
			),
			array(
				'title'   => 'Madras HC Rules on Right to Internet Access as Fundamental Right',
				'content' => 'In a progressive ruling, the Madras High Court has recognized internet access as an essential component of the right to education and freedom of expression in the digital age.',
			),
		),
		'legal-updates' => array(
			array(
				'title'   => 'New Arbitration Amendment Act Comes into Effect from April',
				'content' => 'The Arbitration and Conciliation (Amendment) Act introducing significant reforms to institutional arbitration processes will come into effect from April 1st, with new provisions for expedited resolution.',
			),
			array(
				'title'   => 'SEBI Issues Updated Framework for Corporate Governance Standards',
				'content' => 'The Securities and Exchange Board of India has released an updated regulatory framework enhancing corporate governance standards for listed companies with new disclosure requirements.',
			),
			array(
				'title'   => 'Parliament Passes Amendment to Consumer Protection Act',
				'content' => 'Parliament has passed a crucial amendment to the Consumer Protection Act, strengthening e-commerce consumer rights and establishing a dedicated digital complaints redressal mechanism.',
			),
			array(
				'title'   => 'RBI Announces New Digital Lending Guidelines for NBFCs',
				'content' => 'The Reserve Bank of India has issued comprehensive guidelines for digital lending by Non-Banking Financial Companies, mandating transparent disclosure of loan terms and interest calculations.',
			),
		),
		'monthly-recap' => array(
			array(
				'title'   => 'Supreme Court Monthly Recap: Key Judgments This Month',
				'content' => 'This month, the Supreme Court delivered several notable judgments covering fundamental rights, environmental law, and criminal justice reform. Here is a comprehensive round-up of the most significant decisions.',
			),
			array(
				'title'   => 'High Courts Monthly Wrap: Top Rulings Across States',
				'content' => 'A curated summary of the most impactful High Court rulings from across India this month, covering constitutional law, civil rights, and administrative decisions.',
			),
			array(
				'title'   => 'Legislative Recap: Bills Passed and Notifications Issued',
				'content' => 'A complete recap of legislative activity this month, including bills passed by Parliament, significant ordinances, and regulatory notifications by key government bodies.',
			),
			array(
				'title'   => 'Legal Appointments and Transfers: Monthly Update',
				'content' => 'This month saw several significant appointments and transfers in the judiciary. Here is a comprehensive update on changes across the Supreme Court and various High Courts.',
			),
		),
		'blog' => array(
			array(
				'title'   => 'Understanding the Basics of PIL: A Beginner\'s Guide',
				'content' => 'Public Interest Litigation (PIL) has been a powerful tool for social change in India. This guide explains what PIL is, who can file it, and how it has shaped Indian jurisprudence over the decades.',
			),
			array(
				'title'   => 'How AI Is Transforming Legal Research in India',
				'content' => 'Artificial intelligence is revolutionizing how lawyers and judges approach legal research. From predictive analytics to automated document review, here is how technology is reshaping the legal landscape.',
			),
			array(
				'title'   => '5 Constitutional Amendments Every Law Student Should Know',
				'content' => 'From the 42nd Amendment to the most recent ones, certain constitutional amendments have fundamentally shaped India\'s legal and political framework. Here are five that every aspiring lawyer should study.',
			),
			array(
				'title'   => 'Career Paths After Law School: Beyond Traditional Practice',
				'content' => 'Law graduates today have more career options than ever before. From legal tech startups to international arbitration, explore the diverse career paths available beyond traditional courtroom practice.',
			),
		),
		'other-courts' => array(
			array(
				'title'   => 'Family Court Rules on Digital Assets in Divorce Proceedings',
				'content' => 'A family court in Delhi has issued a precedent-setting order on the division of digital assets, including cryptocurrency holdings, in divorce proceedings, calling it a new frontier in matrimonial law.',
			),
			array(
				'title'   => 'NCLT Approves Major Insolvency Resolution Plan for Steel Company',
				'content' => 'The National Company Law Tribunal has approved a resolution plan for a mid-sized steel company, setting important precedents for operational creditor rights under the Insolvency and Bankruptcy Code.',
			),
			array(
				'title'   => 'Consumer Forum Orders E-Commerce Giant to Pay Compensation',
				'content' => 'A state consumer disputes redressal forum has ordered a major e-commerce platform to pay substantial compensation to a consumer for delivering counterfeit products and misleading advertising.',
			),
			array(
				'title'   => 'Tribunal Sets New Standards for Environmental Clearance Compliance',
				'content' => 'The National Green Tribunal has set stringent new standards for environmental clearance compliance, requiring real-time monitoring data submission from industries in sensitive zones.',
			),
		),
		'know-your-courts' => array(
			array(
				'title'   => 'How the Supreme Court of India Works: Structure and Jurisdiction',
				'content' => 'An in-depth look at the Supreme Court of India — its composition, jurisdiction, the process of filing cases, and how the Chief Justice allocates benches for hearing different matters.',
			),
			array(
				'title'   => 'District Courts Explained: The Backbone of Indian Judiciary',
				'content' => 'District courts handle the majority of cases in India. Understand their structure, types of courts at the district level, and how civil and criminal matters are adjudicated.',
			),
			array(
				'title'   => 'Tribunals in India: What They Are and How They Function',
				'content' => 'From the Armed Forces Tribunal to the National Green Tribunal, India has numerous specialized tribunals. Learn about their purpose, powers, and the types of cases they handle.',
			),
			array(
				'title'   => 'Understanding Lok Adalats: Alternative Dispute Resolution in India',
				'content' => 'Lok Adalats offer a fast and free alternative to traditional litigation. Discover how these people\'s courts work, what cases they handle, and why they are crucial for access to justice.',
			),
		),
	);

	$posts_created = 0;
	$cats_used     = 0;
	$offset_days   = 0;

	foreach ( $demo_data as $cat_slug => $posts ) {
		$cat = get_category_by_slug( $cat_slug );
		if ( ! $cat ) {
			continue;
		}
		$cats_used++;

		foreach ( $posts as $post ) {
			// Check if a post with this title already exists.
			$existing = get_page_by_title( $post['title'], OBJECT, 'post' );
			if ( $existing ) {
				continue;
			}

			$content_blocks = '<!-- wp:paragraph --><p>' . esc_html( $post['content'] ) . '</p><!-- /wp:paragraph -->';
			$content_blocks .= "\n\n" . '<!-- wp:paragraph --><p>This is demo content generated by the Law and Beyond theme. Feel free to edit or replace it with your own articles.</p><!-- /wp:paragraph -->';

			$post_date = date( 'Y-m-d H:i:s', strtotime( "-{$offset_days} days" ) );

			$post_id = wp_insert_post( array(
				'post_title'   => $post['title'],
				'post_content' => $content_blocks,
				'post_status'  => 'publish',
				'post_type'    => 'post',
				'post_date'    => $post_date,
				'post_category' => array( $cat->term_id ),
			) );

			if ( ! is_wp_error( $post_id ) ) {
				$posts_created++;
			}

			$offset_days++;
		}
	}

	// -------------------------------------------------------------------
	// Create one long, detailed demo post that exercises every content style.
	// -------------------------------------------------------------------
	$long_title = 'The Complete Guide to Fundamental Rights Under the Indian Constitution: A Detailed Analysis';
	$existing_long = get_page_by_title( $long_title, OBJECT, 'post' );

	if ( ! $existing_long ) {
		$long_content = <<<'LONGPOST'
<!-- wp:paragraph {"className":"lead-paragraph"} -->
<p class="lead-paragraph"><strong>The Fundamental Rights enshrined in Part III of the Indian Constitution (Articles 12–35) form the bedrock of individual liberty and democratic governance in India.</strong> This comprehensive guide examines each fundamental right, landmark Supreme Court judgments, recent amendments, and their practical implications for citizens, lawyers, and law students.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>1. Introduction: Why Fundamental Rights Matter</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Fundamental Rights are the cornerstone of the Indian Constitution. Borrowed from the Bill of Rights in the United States Constitution, these rights guarantee civil liberties to all citizens — and in some cases, non-citizens — ensuring that the state does not encroach upon individual freedoms. Unlike ordinary legal rights, Fundamental Rights are enforceable by the Supreme Court and High Courts under Articles 32 and 226, respectively.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Dr. B.R. Ambedkar described Article 32 as "the very soul of the Constitution and the very heart of it." Without these rights, the promises of justice, liberty, equality, and fraternity in the Preamble would remain hollow words. The Constituent Assembly debated these provisions extensively between 1946 and 1949, drawing from multiple constitutional traditions worldwide.</p>
<!-- /wp:paragraph -->

<!-- wp:quote -->
<blockquote class="wp-block-quote"><p>"The Fundamental Rights are not gifts of the State to its citizens. They are inherent rights that every person is born with, and the Constitution merely recognizes and protects them."</p><cite>– Justice H.R. Khanna, ADM Jabalpur v. Shivkant Shukla (1976)</cite></blockquote>
<!-- /wp:quote -->

<!-- wp:heading -->
<h2>2. Right to Equality (Articles 14–18)</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Article 14 guarantees equality before law and equal protection of laws to every person in India. The Supreme Court has interpreted this not as absolute equality but as "reasonable classification" — the state may treat differently situated persons differently, provided the classification has a rational nexus with the object sought to be achieved.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3>Key provisions under Right to Equality:</h3>
<!-- /wp:heading -->

<!-- wp:list -->
<ul><li><strong>Article 14:</strong> Equality before law — No person shall be denied equality before law or equal protection of laws within the territory of India.</li><li><strong>Article 15:</strong> Prohibition of discrimination on grounds of religion, race, caste, sex, or place of birth. However, the state may make special provisions for women, children, and socially/educationally backward classes.</li><li><strong>Article 16:</strong> Equality of opportunity in matters of public employment. Reservations for backward classes are permissible under Articles 16(4) and 16(4A).</li><li><strong>Article 17:</strong> Abolition of Untouchability — The practice is abolished and its enforcement in any form is a punishable offence under the Protection of Civil Rights Act, 1955.</li><li><strong>Article 18:</strong> Abolition of titles — No citizen shall accept any title from a foreign state. Military and academic distinctions are exempted.</li></ul>
<!-- /wp:list -->

<!-- wp:heading {"level":3} -->
<h3>Landmark Judgments on Right to Equality</h3>
<!-- /wp:heading -->

<!-- wp:table -->
<figure class="wp-block-table"><table><thead><tr><th>Case Name</th><th>Year</th><th>Key Ruling</th></tr></thead><tbody><tr><td>Indra Sawhney v. Union of India</td><td>1992</td><td>Mandal Commission case — upheld 27% OBC reservation; capped total reservation at 50%; introduced "creamy layer" concept.</td></tr><tr><td>Navtej Singh Johar v. Union of India</td><td>2018</td><td>Decriminalized consensual homosexual acts; struck down Section 377 IPC insofar as it criminalized same-sex relations between consenting adults.</td></tr><tr><td>Indian Young Lawyers Association v. State of Kerala</td><td>2018</td><td>Sabarimala case — women of all ages have the right to enter and worship at the temple; later referred to a larger bench.</td></tr><tr><td>Air India v. Nergesh Meerza</td><td>1981</td><td>Struck down discriminatory service conditions for air hostesses regarding pregnancy and retirement age.</td></tr></tbody></table></figure>
<!-- /wp:table -->

<!-- wp:heading -->
<h2>3. Right to Freedom (Articles 19–22)</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Article 19 protects six freedoms that are available only to citizens of India. These freedoms are not absolute — the state can impose "reasonable restrictions" in the interest of sovereignty, integrity, public order, decency, morality, and other specified grounds.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3>The Six Freedoms under Article 19(1):</h3>
<!-- /wp:heading -->

<!-- wp:list {"ordered":true} -->
<ol><li><strong>Freedom of speech and expression</strong> — Includes freedom of the press (Romesh Thappar v. State of Madras, 1950). The Supreme Court in Shreya Singhal v. Union of India (2015) struck down Section 66A of the IT Act as violating this freedom.</li><li><strong>Freedom to assemble peacefully</strong> — Without arms. Reasonable restrictions can be imposed in the interest of public order and sovereignty.</li><li><strong>Freedom to form associations or unions</strong> — Includes the right to form political parties, trade unions, and cooperative societies.</li><li><strong>Freedom to move freely throughout India</strong> — Can be restricted on grounds of public interest and protection of scheduled tribes.</li><li><strong>Freedom to reside and settle in any part of India</strong> — Subject to reasonable restrictions.</li><li><strong>Freedom to practice any profession, trade, or business</strong> — The state may prescribe professional or technical qualifications and can create monopolies.</li></ol>
<!-- /wp:list -->

<!-- wp:paragraph -->
<p>Article 20 provides protection in respect of conviction for offences — no ex-post-facto criminal laws, no double jeopardy, and no self-incrimination. Article 21, perhaps the most expansive fundamental right, guarantees that "no person shall be deprived of his life or personal liberty except according to procedure established by law."</p>
<!-- /wp:paragraph -->

<!-- wp:quote -->
<blockquote class="wp-block-quote"><p>"The right to life under Article 21 does not mean merely animal existence. It includes the right to live with human dignity, the right to livelihood, the right to health, the right to clean environment, the right to education, and the right to shelter."</p><cite>– Justice P.N. Bhagwati, Francis Coralie Mullin v. Administrator, Union Territory of Delhi (1981)</cite></blockquote>
<!-- /wp:quote -->

<!-- wp:heading -->
<h2>4. Right Against Exploitation (Articles 23–24)</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>These articles address social justice concerns that were particularly relevant at the time of Independence and remain tragically relevant today:</p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul><li><strong>Article 23:</strong> Prohibits traffic in human beings, begar (forced labor), and other similar forms of forced labor. Violations are punishable under the Bonded Labour System (Abolition) Act, 1976. The Supreme Court in People's Union for Democratic Rights v. Union of India (1982) — the Asiad Workers case — held that paying wages below minimum wage amounts to forced labor under Article 23.</li><li><strong>Article 24:</strong> Prohibits employment of children below 14 years in factories, mines, and other hazardous employment. The Child Labour (Prohibition and Regulation) Amendment Act, 2016, extended this protection further.</li></ul>
<!-- /wp:list -->

<!-- wp:heading -->
<h2>5. Right to Freedom of Religion (Articles 25–28)</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>India's commitment to secularism is reflected in these articles, which protect religious freedom while allowing the state to regulate secular aspects of religious practice:</p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul><li><strong>Article 25:</strong> Freedom of conscience — all persons are equally entitled to freedom of conscience and the right to freely profess, practice, and propagate religion, subject to public order, morality, and health.</li><li><strong>Article 26:</strong> Freedom to manage religious affairs — every religious denomination has the right to establish institutions, manage its own affairs in matters of religion, and own and administer property.</li><li><strong>Article 27:</strong> No person shall be compelled to pay taxes for the promotion of any particular religion.</li><li><strong>Article 28:</strong> No religious instruction shall be provided in educational institutions wholly maintained by state funds.</li></ul>
<!-- /wp:list -->

<!-- wp:heading -->
<h2>6. Cultural and Educational Rights (Articles 29–30)</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>These articles protect the interests of minorities — both linguistic and religious:</p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul><li><strong>Article 29:</strong> Any section of citizens having a distinct language, script, or culture has the right to conserve the same. No citizen can be denied admission to any educational institution on grounds of religion, race, caste, or language.</li><li><strong>Article 30:</strong> All minorities, whether based on religion or language, have the right to establish and administer educational institutions of their choice. The state shall not discriminate against such institutions in granting aid. The Supreme Court in <em>TMA Pai Foundation v. State of Karnataka (2002)</em> clarified the scope of minority educational institutions' rights.</li></ul>
<!-- /wp:list -->

<!-- wp:heading -->
<h2>7. Right to Constitutional Remedies (Article 32)</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Dr. Ambedkar called Article 32 "the most important article — the very soul of the Constitution." This article empowers citizens to move the Supreme Court directly for enforcement of Fundamental Rights through five types of writs:</p>
<!-- /wp:paragraph -->

<!-- wp:list {"ordered":true} -->
<ol><li><strong>Habeas Corpus</strong> — "To have the body." Issued to produce a detained person before the court to examine the legality of detention.</li><li><strong>Mandamus</strong> — "We command." Directs a public authority to perform its duty that it has failed or refused to perform.</li><li><strong>Prohibition</strong> — Issued by a higher court to a lower court to prevent it from exceeding its jurisdiction.</li><li><strong>Certiorari</strong> — Issued to quash an order already passed by a lower court or tribunal that has acted beyond jurisdiction.</li><li><strong>Quo Warranto</strong> — "By what authority." Challenges the legal authority of a person holding a public office.</li></ol>
<!-- /wp:list -->

<!-- wp:heading -->
<h2>8. Recent Developments and Emerging Issues</h2>
<!-- /wp:heading -->

<!-- wp:heading {"level":3} -->
<h3>8.1 Right to Privacy as a Fundamental Right</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>In <strong>Justice K.S. Puttaswamy v. Union of India (2017)</strong>, a nine-judge bench of the Supreme Court unanimously held that the right to privacy is a fundamental right protected under Article 21. This landmark judgment overruled the earlier decisions in M.P. Sharma (1954) and Kharak Singh (1962) that had held privacy was not a fundamental right. The Puttaswamy decision has had far-reaching implications for data protection, surveillance, Aadhaar, and digital rights in India.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3>8.2 CAA and Challenges to Article 14</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>The Citizenship Amendment Act, 2019 (CAA) has been challenged before the Supreme Court as violating Articles 14 and 15 by granting citizenship based on religion. The matter is pending before a Constitution Bench and represents one of the most significant contemporary challenges to the equality provisions of the Constitution.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3>8.3 Digital Rights and Free Speech</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>With the proliferation of social media, courts are increasingly called upon to balance free speech under Article 19(1)(a) with the government's power to impose reasonable restrictions. The IT Rules, 2021, have been challenged in multiple High Courts for imposing content takedown and compliance obligations on social media intermediaries.</p>
<!-- /wp:paragraph -->

<!-- wp:separator -->
<hr class="wp-block-separator"/>
<!-- /wp:separator -->

<!-- wp:heading -->
<h2>9. Summary: Fundamental Rights at a Glance</h2>
<!-- /wp:heading -->

<!-- wp:table -->
<figure class="wp-block-table"><table><thead><tr><th>Right</th><th>Articles</th><th>Key Protection</th></tr></thead><tbody><tr><td>Right to Equality</td><td>14–18</td><td>Equality before law, prohibition of discrimination, abolition of untouchability and titles</td></tr><tr><td>Right to Freedom</td><td>19–22</td><td>Six freedoms, protection from arbitrary arrest, right to life and personal liberty</td></tr><tr><td>Right Against Exploitation</td><td>23–24</td><td>Prohibition of human trafficking, forced labor, and child labor</td></tr><tr><td>Right to Freedom of Religion</td><td>25–28</td><td>Freedom of conscience, manage religious affairs, no compulsory religious tax</td></tr><tr><td>Cultural and Educational Rights</td><td>29–30</td><td>Protection of minorities' language, script, culture; right to establish educational institutions</td></tr><tr><td>Right to Constitutional Remedies</td><td>32</td><td>Right to approach Supreme Court for enforcement via writs</td></tr></tbody></table></figure>
<!-- /wp:table -->

<!-- wp:heading -->
<h2>10. Conclusion</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Fundamental Rights are not static — they evolve through judicial interpretation, constitutional amendments, and changing social values. From the basic structure doctrine established in <em>Kesavananda Bharati v. State of Kerala (1973)</em> to the recognition of privacy as a fundamental right in Puttaswamy (2017), the Indian judiciary has continuously expanded the ambit of these rights to meet contemporary challenges.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Understanding Fundamental Rights is essential not only for law students and practitioners but for every citizen who wishes to hold the state accountable and protect individual liberties. As India navigates the complexities of digital governance, environmental challenges, and social justice movements, these rights will continue to serve as the ultimate check on state power and the ultimate guarantee of individual dignity.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><em>This is a demo article generated by the Law and Beyond theme to showcase how long-form legal content appears on the site. Feel free to edit or replace it with your own articles.</em></p>
<!-- /wp:paragraph -->
LONGPOST;

		$know_cat = get_category_by_slug( 'know-your-courts' );
		$sc_cat   = get_category_by_slug( 'supreme-court' );
		$cat_ids  = array();
		if ( $know_cat ) $cat_ids[] = $know_cat->term_id;
		if ( $sc_cat )   $cat_ids[] = $sc_cat->term_id;
		if ( empty( $cat_ids ) ) {
			$cat_ids[] = 1; // fallback to "Uncategorized"
		}

		$long_post_id = wp_insert_post( array(
			'post_title'    => $long_title,
			'post_content'  => $long_content,
			'post_status'   => 'publish',
			'post_type'     => 'post',
			'post_date'     => date( 'Y-m-d H:i:s' ),
			'post_category' => $cat_ids,
			'tags_input'    => array( 'Fundamental Rights', 'Constitution', 'Article 21', 'Supreme Court', 'Legal Guide', 'Right to Equality', 'Right to Privacy' ),
		) );

		if ( ! is_wp_error( $long_post_id ) ) {
			$posts_created++;
			// Mark it as sticky so it appears in Top Stories
			stick_post( $long_post_id );
		}
	}

	return array(
		'posts'      => $posts_created,
		'categories' => $cats_used,
	);
}
