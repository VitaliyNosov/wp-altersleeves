<template>
	<footer class="site-footer">
		<div class="footer-main">
			<div class="container">
				<div class="row" v-if="footer_logo">
					<div class="col-12 col-md-auto">
						<div class="footer-logo">
							<a href="/" class="link-home">
								<img v-bind:src="footer_logo" alt="Logo">
							</a>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-6 col-md-2" v-if="menus.first && menus.first.length">
						<nav class="footer-nav">
							<ul class="menu">
								<li class="menu-item" v-for="(menuSingle, index) in menus.first" :key="index">
									<a v-bind:href="menuSingle.link" class="menu-link">{{ menuSingle.text }}</a>
								</li>
							</ul>
						</nav>
					</div>
					<div class="col-6 col-md-2" v-if="menus.second && menus.second.length">
						<nav class="footer-nav">
							<ul class="menu">
								<li class="menu-item" v-for="(menuSingle, index) in menus.second" :key="index">
									<a v-bind:href="menuSingle.link" class="menu-link">{{ menuSingle.text }}</a>
								</li>
							</ul>
						</nav>
					</div>
					<div class="col-md-2" v-if="menus.third && menus.third.length">
						<nav class="footer-nav nav-primary">
							<ul class="menu">
								<li class="menu-item" v-for="(menuSingle, index) in menus.third" :key="index">
									<a v-bind:href="menuSingle.link" class="menu-link">{{ menuSingle.text }}</a>
								</li>
							</ul>
						</nav>
					</div>
					<div class="col-md-3" v-if="menus.socials && menus.socials.length">
						<div class="footer-social">
							<div class="social-item" v-for="(menuSingle, index) in menus.socials" :key="index">
								<a v-bind:href="menuSingle.link" class="social-link">
									<i v-bind:class="menuSingle.classes"></i>
								</a>
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="subscribe-box">
							<div class="subscribe-title">Subscribe to be up to date</div>
							<form action="#">
								<input class="form-field" type="email" id="subscribe-email" title="Your email"
									   placeholder="Your email" autocomplete="email" required>
								<button type="submit" class="button button-primary">I'm In</button>
							</form>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<div class="footer-payments-line">
							<div class="payment-item">
								<img src="/wp-content/themes/mythic-app/resources/img/partners/AMEX.svg" alt="MC">
							</div>
							<div class="payment-item">
								<img src="/wp-content/themes/mythic-app/resources/img/partners/Visa.svg" alt="MC">
							</div>
							<div class="payment-item">
								<img src="/wp-content/themes/mythic-app/resources/img/partners/MC.svg" alt="MC">
							</div>
							<div class="payment-item">
								<img src="/wp-content/themes/mythic-app/resources/img/partners/Amazon.svg" alt="MC">
							</div>
							<div class="payment-item">
								<img src="/wp-content/themes/mythic-app/resources/img/partners/SagePay.svg" alt="MC">
							</div>
							<div class="payment-item">
								<img src="/wp-content/themes/mythic-app/resources/img/partners/PP.svg" alt="MC">
							</div>
							<div class="payment-item">
								<img src="/wp-content/themes/mythic-app/resources/img/partners/Verve.svg" alt="MC">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="footer-bottom" v-if="copyright">
			<p class="site-copiright">{{ copyright }}</p>
		</div>
	</footer>
</template>

<script>
	export default {
		data: function () {
			return {
				footer_logo: vue_global_data.footer_data.logo ? vue_global_data.footer_data.logo : "",
				menus: vue_global_data.footer_data.menus ? vue_global_data.footer_data.menus : {},
				copyright: vue_global_data.footer_data.copyright ? vue_global_data.footer_data.copyright : {}
			}
		}
	}
</script>
