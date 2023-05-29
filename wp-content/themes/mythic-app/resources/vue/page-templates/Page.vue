<template>
	<div>
		<maHeader/>

		<main class="site-content general-page-content">

			<template v-if="nav_data">
				<NavContainer :imported-data="nav_data"/>
			</template>

			<div class="container">
				<div class="row">
					<div class="col-md-4">
						<template v-if="side_menu">
							<SideMenu :imported-data="side_menu"/>
						</template>
					</div>
					<div class="col-md-8">
						<div class="general-page-body">
							<h1 v-if="page_title">{{ page_title }}</h1>
							<div v-html="page_content"></div>
						</div>
					</div>
				</div>
			</div>

		</main>

		<maFooter/>
	</div>
</template>

<script>
	export default {
		data: function () {
			return {
				page_title: "GENERAL PAGE",
				page_content: "<div class=\"text-section\">" +
					"<h4>Aenean turpis lacus</h4>" +
					"<p> Semper et diam quis, pellentesque sodales magna. Nunc ac turpis augue. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nam aliquet rhoncus sapien, id consequat nunc venenatis non. Ut in magna auctor sapien varius aliquam quis sit amet risus. Mauris quis fermentum ante. Phasellus quis venenatis risus.</p>" +
					"</div>",
				nav_data: {
					breadcrumbs: [
						{
							title: 'Home',
							url: '/'
						},
						{
							title: 'Generic page',
						}
					]
				},
				side_menu: [
					{
						title: 'Shipping',
						url: '/'
					},
					{
						title: 'Returns',
						url: '/'
					},
					{
						title: 'FAQ',
						url: '/'
					},
					{
						title: 'Terms',
						url: '/'
					},
					{
						title: 'Legal information',
						url: '/'
					},
					{
						title: 'Privacy policy',
						url: '/'
					},
				],
			}
		}
	}
</script>
