<?php
/**
 * Landing page.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$lead_flag = isset( $_GET['lead'] ) ? sanitize_key( wp_unslash( $_GET['lead'] ) ) : '';
?>

<section class="hero" id="top">
	<div class="wrap hero-grid">
		<div class="hero-copy">
			<p class="eyebrow"><?php echo esc_html( kov( 'hero_eyebrow' ) ); ?></p>
			<h1><?php echo esc_html( kov( 'hero_title' ) ); ?></h1>
			<p class="lede"><?php echo kov_html( 'hero_lede' ); ?></p>
			<div class="hero-actions">
				<a class="btn btn-accent" href="<?php echo esc_url( kov_tel_href() ); ?>">Позвонить</a>
				<a class="btn btn-ghost" href="#request">Вызвать эвакуатор</a>
			</div>
			<a class="hero-phone" href="<?php echo esc_url( kov_tel_href() ); ?>"><?php echo esc_html( kov( 'phone_display' ) ); ?></a>
			<ul class="hero-facts">
				<?php for ( $fact = 1; $fact <= 3; $fact++ ) : ?>
					<?php if ( kov( 'fact_' . $fact . '_value' ) === '' ) { continue; } ?>
					<li>
						<strong><?php echo esc_html( kov( 'fact_' . $fact . '_value' ) ); ?></strong>
						<span><?php echo esc_html( kov( 'fact_' . $fact . '_label' ) ); ?></span>
					</li>
				<?php endfor; ?>
			</ul>
		</div>
		<figure class="hero-media">
			<?php kov_hero_media(); ?>
			<?php if ( kov( 'hero_caption' ) !== '' ) : ?>
				<figcaption class="hero-caption"><?php echo esc_html( kov( 'hero_caption' ) ); ?></figcaption>
			<?php endif; ?>
		</figure>
	</div>
</section>

<section class="section" id="services">
	<div class="wrap">
		<?php kov_section_head( 'services_eyebrow', 'services_title', 'services_text' ); ?>
		<div class="services">
			<?php foreach ( kov_items( 'kov_service' ) as $index => $service ) : ?>
				<article class="service reveal" style="--d: <?php echo (int) $index * 70; ?>ms">
					<?php echo kov_icon( (string) get_post_meta( $service->ID, '_kov_icon', true ) ); ?>
					<h3><?php echo esc_html( get_the_title( $service ) ); ?></h3>
					<p><?php echo esc_html( wp_strip_all_tags( $service->post_content ) ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="section" id="prices" style="padding-top: 0">
	<div class="wrap">
		<?php kov_section_head( 'prices_eyebrow', 'prices_title', 'prices_text' ); ?>
		<div class="prices">
			<?php foreach ( kov_items( 'kov_price' ) as $index => $price ) : ?>
				<?php
				$featured = (string) get_post_meta( $price->ID, '_kov_featured', true ) === '1';
				$badge    = (string) get_post_meta( $price->ID, '_kov_badge', true );
				?>
				<article class="price reveal<?php echo $featured ? ' is-featured' : ''; ?>" style="--d: <?php echo (int) $index * 80; ?>ms">
					<?php if ( $featured && $badge !== '' ) : ?>
						<span class="badge"><?php echo esc_html( $badge ); ?></span>
					<?php endif; ?>
					<h3><?php echo esc_html( get_the_title( $price ) ); ?></h3>
					<p class="price-value"><?php echo esc_html( (string) get_post_meta( $price->ID, '_kov_price', true ) ); ?></p>
					<p><?php echo esc_html( wp_strip_all_tags( $price->post_content ) ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>
		<?php if ( kov( 'price_note' ) !== '' ) : ?>
			<p class="price-note reveal"><?php echo kov_html( 'price_note' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<section class="section" id="about">
	<div class="wrap about-grid">
		<div class="about-copy reveal">
			<p class="eyebrow"><?php echo esc_html( kov( 'about_eyebrow' ) ); ?></p>
			<h2><?php echo esc_html( kov( 'about_title' ) ); ?></h2>
			<?php if ( kov( 'about_text' ) !== '' ) : ?>
				<p><?php echo kov_html( 'about_text' ); ?></p>
			<?php endif; ?>
			<?php if ( kov( 'about_text_2' ) !== '' ) : ?>
				<p><?php echo kov_html( 'about_text_2' ); ?></p>
			<?php endif; ?>
			<ul class="about-points">
				<?php foreach ( kov_lines( 'about_points' ) as $point ) : ?>
					<li><?php echo esc_html( $point ); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<div class="about-photos reveal" style="--d: 80ms">
			<?php kov_about_image( 'about_image_1', 'assets/img/01-1024x768.png', 'about_alt_1', 1024, 768 ); ?>
			<?php kov_about_image( 'about_image_2', 'assets/img/04-768x576.png', 'about_alt_2', 768, 576 ); ?>
			<?php kov_about_image( 'about_image_3', 'assets/img/06-768x576.png', 'about_alt_3', 768, 576 ); ?>
		</div>
	</div>
</section>

<section class="section" id="steps" style="padding-top: 10px">
	<div class="wrap">
		<?php kov_section_head( 'steps_eyebrow', 'steps_title' ); ?>
		<ol class="steps">
			<?php foreach ( kov_items( 'kov_step' ) as $index => $step ) : ?>
				<li class="step reveal" style="--d: <?php echo (int) $index * 80; ?>ms">
					<span class="step-num"><?php echo (int) $index + 1; ?></span>
					<h3><?php echo esc_html( get_the_title( $step ) ); ?></h3>
					<p><?php echo esc_html( wp_strip_all_tags( $step->post_content ) ); ?></p>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>

<section class="section" id="advantages" style="padding-top: 10px">
	<div class="wrap">
		<?php kov_section_head( 'perks_eyebrow', 'perks_title' ); ?>
		<div class="perks">
			<?php foreach ( kov_items( 'kov_perk' ) as $index => $perk ) : ?>
				<article class="perk reveal" style="--d: <?php echo (int) $index * 60; ?>ms">
					<?php echo kov_icon( (string) get_post_meta( $perk->ID, '_kov_icon', true ) ); ?>
					<h3><?php echo esc_html( get_the_title( $perk ) ); ?></h3>
					<p><?php echo esc_html( wp_strip_all_tags( $perk->post_content ) ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="section" id="faq" style="padding-top: 10px">
	<div class="wrap">
		<?php kov_section_head( 'faq_eyebrow', 'faq_title' ); ?>
		<div class="faq">
			<?php foreach ( kov_items( 'kov_faq' ) as $index => $item ) : ?>
				<details class="faq-item reveal" style="--d: <?php echo (int) $index * 40; ?>ms"<?php echo 0 === $index ? ' open' : ''; ?>>
					<summary><?php echo esc_html( get_the_title( $item ) ); ?></summary>
					<p><?php echo esc_html( wp_strip_all_tags( $item->post_content ) ); ?></p>
				</details>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="section" id="contacts" style="padding-bottom: 28px">
	<div class="wrap">
		<?php kov_section_head( 'contacts_eyebrow', 'contacts_title' ); ?>
		<div class="contacts">
			<article class="contact-card reveal">
				<?php echo kov_icon( 'phone' ); ?>
				<h3>Телефон</h3>
				<p><a href="<?php echo esc_url( kov_tel_href() ); ?>"><?php echo esc_html( kov( 'phone_display' ) ); ?></a></p>
			</article>
			<article class="contact-card reveal" style="--d: 70ms">
				<?php echo kov_icon( 'message' ); ?>
				<h3>Мессенджеры</h3>
				<p><?php echo esc_html( kov( 'messengers_text' ) ); ?></p>
				<div class="messengers">
					<?php if ( kov( 'telegram' ) !== '' ) : ?>
						<a href="<?php echo esc_url( kov( 'telegram' ) ); ?>" target="_blank" rel="noopener noreferrer">Telegram</a>
					<?php endif; ?>
					<?php if ( kov( 'whatsapp' ) !== '' ) : ?>
						<a href="<?php echo esc_url( kov( 'whatsapp' ) ); ?>" target="_blank" rel="noopener noreferrer">WhatsApp</a>
					<?php endif; ?>
				</div>
			</article>
			<article class="contact-card reveal" style="--d: 140ms">
				<?php echo kov_icon( 'speed' ); ?>
				<h3>Режим работы</h3>
				<p><?php echo esc_html( kov( 'hours' ) ); ?></p>
			</article>
		</div>
	</div>
</section>

<section class="section request" id="request">
	<div class="wrap request-grid">
		<div class="request-copy reveal">
			<p class="eyebrow"><?php echo esc_html( kov( 'request_eyebrow' ) ); ?></p>
			<h2><?php echo esc_html( kov( 'request_title' ) ); ?></h2>
			<p><?php echo kov_html( 'request_text' ); ?></p>
		</div>
		<div class="request-form reveal" style="--d: 80ms">
			<form class="kov-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php if ( 'ok' === $lead_flag ) : ?>
					<p class="note note-ok">Заявка принята. Перезвоним, уточним детали и назовём стоимость до выезда.</p>
				<?php elseif ( 'err' === $lead_flag ) : ?>
					<p class="note note-err">Не получилось отправить заявку. Проверьте поля или позвоните нам.</p>
				<?php endif; ?>
				<input type="hidden" name="action" value="kov_submit_lead">
				<?php wp_nonce_field( 'kov_lead' ); ?>
				<input class="hp" type="text" name="company" value="" tabindex="-1" autocomplete="off" aria-hidden="true">
				<label>Имя
					<input type="text" name="lead_name" required maxlength="80" autocomplete="name" placeholder="Как к вам обращаться">
				</label>
				<label>Телефон
					<input type="tel" name="lead_phone" required maxlength="30" autocomplete="tel" inputmode="tel" placeholder="+7 (___) ___-__-__">
				</label>
				<label>Откуда забрать
					<input type="text" name="lead_from" required maxlength="160" placeholder="Адрес или ориентир">
				</label>
				<label>Куда доставить
					<input type="text" name="lead_to" required maxlength="160" placeholder="СТО, дом, другой город">
				</label>
				<label class="wide">Ситуация
					<textarea name="lead_note" required maxlength="800" placeholder="Поломка, ДТП, заблокированы колёса, марка авто"></textarea>
				</label>
				<label class="check">
					<input type="checkbox" name="lead_consent" value="1" required>
					<span>Согласен на обработку персональных данных. <a href="<?php echo esc_url( kov_page_url( 'soglasie' ) ); ?>">Текст согласия</a></span>
				</label>
				<button class="btn btn-accent btn-block" type="submit">Отправить заявку</button>
			</form>
		</div>
	</div>
</section>

<?php
get_footer();
