<template>
	<header class="site-header">
		<template v-if="top_promo_line.html">
			<div class="header-top"
				 v-bind:style="{ background: top_promo_line.background, padding: top_promo_line.padding }"
				 v-html="top_promo_line.html">
			</div>
		</template>
		<div class="lg-container">
			<div class="header-wrapper navbar-expand-lg">
				<template v-if="logo">
					<div class="header-logo">
						<a href="/">
							<img v-bind:src="logo" alt="Logo GM">
						</a>
					</div>
					<div class="header-logo-mobile">
						<a href="/">
							<img v-bind:src="logo" alt="Logo GM">
						</a>
					</div>
				</template>

				<div id="header-search" class="header-search" v-bind:class="{ active: searchVal }">
                    <span class="main-search-icon">
                        <i class="icon-search"></i>
                    </span>
					<span id="close-autocomplete" class="close-autocomplete" v-on:click="clearSearchValue">&#x2715</span>
					<input type="search" class="main-search-field" name="header-search" id="header-search-input"
						   placeholder="Search for our Staff Brands of Artists"
						   v-model="searchVal"
						   @keyup="searchProcess"
					>
					<div id="search-autocomplete" class="autocomplete-container">
						<ul class="autocomplete-list" v-if="searchResults.length">
							<li v-for="(searchResultSingle, index) in searchResults" :key="index">
								<a v-bind:href="searchResultSingle.link">
									{{ searchResultSingle.r }}
								</a>
							</li>
						</ul>
						<div v-else-if="searchVal && !searchResults.length">
							Nothing found
						</div>
					</div>
				</div>
				<div class="mobile-menu-toggler">
					<div class="burger collapsed" data-bs-toggle="collapse" data-bs-target="#main-navbar"
						 aria-controls="main-navbar" aria-expanded="false" aria-label="Toggle navigation">
						<i class="icon-hamburger"></i>
					</div>
				</div>
				<nav class="header-nav collapse navbar-collapse" id="main-navbar">
					<ul class="menu" v-if="menu">
						<li class="menu-item" v-for="(menuSingle, index) in menu" :key="index"
							v-bind:class="[menuSingle.classes ? menuSingle.classes : '', menu.children && menu.children.length ? 'menu-item-has-mega-menu' : '']">
							<a v-bind:href="menuSingle.link" class="menu-link">
								{{ menuSingle.text }}
							</a>
							<div class="mega-menu-container" v-if="menuSingle.children && menuSingle.children.length">
								<div class="lg-container">
									<div class="row">
										<div class="col-md-2">
											<div class="mega-menu-widget">
												<h4 class="widget-title">Lorem ipsum</h4>
												<ul class="sub-menu">
													<li v-for="(submenuSingle, subIndex) in menuSingle.children"
														:key="subIndex">
														<a v-bind:href="submenuSingle.link">{{ submenuSingle.text }}</a>
													</li>
												</ul>
											</div>
										</div>
									</div>
								</div>
							</div>
						</li>
					</ul>
					<template v-if="user_data.currency">
						<div class="mobile-currency display-mobile">
							<div class="currensy">{{ user_data.currency }}</div>
						</div>
					</template>
				</nav>

				<div class="header-actions-box">
					<a href="#" class="action-link action-account-link">
						<i class="icon-user"></i>
					</a>
					<a href="#" class="action-link action-cart-link">
						<i class="icon-cart"></i>
					</a>
					<template v-if="user_data.currency">
						<a href="#" class="action-link action-currency">
							<span class="currency">{{ user_data.currency }}</span>
						</a>
					</template>
				</div>
			</div>
		</div>
	</header>
</template>

<script>
	export default {
		data: function () {
			return {
				top_promo_line: vue_global_data.header_data.top_promo_line ? vue_global_data.header_data.top_promo_line : {},
				logo: vue_global_data.header_data.logo ? vue_global_data.header_data.logo : "",
				menu: vue_global_data.header_data.menu ? vue_global_data.header_data.menu : [],
				user_data: vue_global_data.header_data.user_data ? vue_global_data.header_data.user_data : {},
				cart_data: vue_global_data.header_data.cart_data ? vue_global_data.header_data.cart_data : {},
				enabledCategoriesForSearch: ['artists', 'cards', 'content_creators', 'sets'],
				searchVal: '',
				searchLoadedLetters: [],
				searchVariants: {},
				searchResults: []
			}
		},
		methods: {
			searchProcess() {
				let app = this;
				app.searchResults = [];
				if (app.searchVal == '') return;

				app.checkSearchResults();
			},
			checkSearchResults() {
				let app = this;
				let firstLetter = app.searchVal.charAt(0);
				if (Number.isInteger(firstLetter)) {
					firstLetter = 'a' + firstLetter;
				} else {
					firstLetter = firstLetter.toLowerCase();
				}
				if (app.searchLoadedLetters.includes(firstLetter)) {
					app.fillSearchResults();
					return;
				}

				$.getJSON('/files/search_indexing/' + firstLetter + '.json', function (data) {
					app.searchLoadedLetters.push(firstLetter);
					app.enabledCategoriesForSearch.forEach(function (el) {
						if (!data[el].length) return;
						app.searchVariants[el] = app.searchVariants[el] || [];

						data[el].forEach(function (searchResult) {
							searchResult.link = app.createLinkByType(searchResult, el);
						});
						Array.prototype.push.apply(app.searchVariants[el], data[el]);
					});

					app.fillSearchResults();
				}).fail(function () {
					console.log('fail');
					app.searchLoadedLetters.push(firstLetter);
				});
			},
			fillSearchResults() {
				let app = this;
				let currentResults = [];
				let currentSearchVal = app.searchVal.toLowerCase();
				app.enabledCategoriesForSearch.forEach(function (el) {
					if (!app.searchVariants[el].length) return;
					currentResults = app.searchVariants[el].filter(post => {
						return post.n && post.n.toLowerCase().includes(currentSearchVal) || post.r && post.r.toLowerCase().includes(currentSearchVal) || post.l && post.l.toLowerCase().includes(currentSearchVal)
					});

					if (!currentResults.length) return;

					Array.prototype.push.apply(app.searchResults, currentResults);
					if (app.searchResults.length > 24) {
						app.searchResults = app.searchResults.slice(0, 25);
						return false;
					}
				});
			},
			createLinkByType($searchResult, $type) {
				switch ($type) {
					case 'artists':
						return '/alterist/' + $searchResult.l;
					case 'content_creators':
						return '/content-creator/' + $searchResult.l;
					case 'cards':
						return '/browse?browse_type=cards&card_id=' + $searchResult.i;
					case 'sets':
						return '/browse?browse_type=sets&set_id=' + $searchResult.i;
				}
			},
			clearSearchValue(){
				this.searchVal = '';
			}
		}
	}
</script>
