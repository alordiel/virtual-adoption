<?php
get_header();
$post_id       = get_the_ID();
$age           = (int) get_post_meta( $post_id, 'animals-age', true );
$sheltered_for = (int) get_post_meta( $post_id, 'sheltered-years', true );
$sex           = get_post_meta( $post_id, 'animals-sex', true );
?>
	<div class="shaltered-animal-container">
		<div class="sinlge-animal-infobox">
			<div class="animal-image">
				<?php
				the_post_thumbnail( 'large', [
					'class' => 'post-thumbnail',
					'title' => get_the_title(),
				] );
				?>
			</div>
			<div class="animal-details">
				<div class="animal-additional-info">
					<svg width="26px" height="26px" viewBox="0 0 30 30" xmlns="http://www.w3.org/2000/svg">
						<path
							d="M15 13.286c-4.343 0-10.5 6.576-10.5 10.727C4.5 25.883 5.966 27 8.423 27c2.671 0 4.435-1.344 6.577-1.344 2.16 0 3.93 1.344 6.577 1.344 2.457 0 3.923-1.117 3.923-2.987 0-4.15-6.157-10.727-10.5-10.727zm6.577 12c-1.11 0-2.068-.31-3.082-.637-1.076-.347-2.189-.706-3.495-.706-1.293 0-2.398.357-3.467.703-1.02.329-1.982.64-3.11.64-2.173 0-2.173-.958-2.173-1.273C6.25 20.643 11.73 15 15 15c3.27 0 8.75 5.644 8.75 9.013 0 .315 0 1.273-2.173 1.273zm5.39-15.36a2.574 2.574 0 00-.59-.068c-1.414 0-2.824 1.125-3.323 2.753-.568 1.856.261 3.663 1.854 4.035.196.047.394.069.591.069 1.414 0 2.823-1.125 3.322-2.753.569-1.857-.261-3.664-1.854-4.036h0zm.178 3.543C26.85 14.427 26.077 15 25.499 15a.803.803 0 01-.184-.02c-.203-.048-.368-.178-.491-.389-.226-.385-.26-.94-.093-1.487.293-.959 1.067-1.532 1.646-1.532.063 0 .125.007.184.02.202.048.368.18.491.39.225.384.26.94.093 1.487h0zm-8.724-1.972c.194.05.391.074.589.074 1.522 0 3.074-1.437 3.647-3.495.648-2.326-.199-4.565-1.89-5.002a2.338 2.338 0 00-.59-.074c-1.522 0-3.074 1.437-3.647 3.496-.647 2.326.2 4.565 1.891 5.001h0zm-.203-4.55c.406-1.457 1.4-2.232 1.96-2.232.05 0 .097.006.143.017.225.059.389.268.486.434.278.471.466 1.365.162 2.46-.406 1.456-1.399 2.232-1.96 2.232a.576.576 0 01-.143-.018c-.225-.058-.39-.267-.487-.433-.277-.472-.465-1.365-.16-2.46h0zm-7.228 4.625c.198 0 .395-.024.589-.074 1.692-.436 2.538-2.675 1.89-5.001C12.898 4.437 11.346 3 9.823 3c-.197 0-.394.024-.588.074-1.692.436-2.539 2.675-1.891 5.001.573 2.059 2.125 3.496 3.647 3.496zM9.192 5.166c.097-.166.262-.375.487-.434a.572.572 0 01.143-.017c.56 0 1.553.775 1.96 2.233.304 1.094.116 1.988-.162 2.459-.098.166-.261.375-.487.433a.572.572 0 01-.143.018c-.56 0-1.553-.776-1.96-2.233-.304-1.094-.116-1.988.162-2.46zm-4.1 11.48c1.593-.372 2.423-2.18 1.854-4.035-.5-1.628-1.909-2.753-3.322-2.753a2.57 2.57 0 00-.591.068c-1.593.372-2.423 2.18-1.854 4.036.499 1.628 1.909 2.753 3.322 2.753.198 0 .396-.022.592-.069zm-2.237-3.178c-.167-.547-.132-1.103.093-1.487.124-.21.29-.341.492-.388.059-.014.12-.021.184-.021.578 0 1.352.573 1.645 1.531.168.547.133 1.103-.093 1.488-.123.21-.288.341-.49.388A.806.806 0 014.5 15c-.578 0-1.352-.573-1.646-1.532h0z"
							stroke="#000" stroke-width="0.5" fill="#222" fill-rule="nonzero"></path>
					</svg>
					<?php _e( 'Name', 'ars-sheltered-animals' ) ?>: <?php echo get_the_title(); ?>
				</div>
				<div class="animal-additional-info">
					<svg viewBox="0 0 30 30" height="30px" width="30px" xmlns="http://www.w3.org/2000/svg">
						<path
							d="M7.571 5.625A1.86 1.86 0 015.714 3.75c0-1.816 1.857-1.348 1.857-3.75.697 0 1.858 1.729 1.858 3.281 0 1.553-.827 2.344-1.858 2.344zm7.429 0a1.86 1.86 0 01-1.857-1.875C13.143 1.934 15 2.402 15 0c.696 0 1.857 1.729 1.857 3.281 0 1.553-.827 2.344-1.857 2.344zm7.429 0A1.86 1.86 0 0120.57 3.75c0-1.816 1.858-1.348 1.858-3.75.696 0 1.857 1.729 1.857 3.281 0 1.553-.827 2.344-1.857 2.344zM25.214 15h-1.857V6.562H21.5V15h-5.571V6.562H14.07V15H8.5V6.562H6.643V15H4.786C3.248 15 2 16.26 2 17.813V30h26V17.812C28 16.26 26.752 15 25.214 15zm.929 13.125H3.857v-4.221C4.8 23.348 5.246 22.5 6.338 22.5c1.622 0 1.814 1.875 4.338 1.875 2.487 0 2.74-1.875 4.324-1.875 1.634 0 1.81 1.875 4.338 1.875 2.516 0 2.714-1.875 4.338-1.875 1.073 0 1.523.848 2.467 1.404v4.221zm0-6.584c-.559-.462-1.231-.916-2.467-.916-2.52 0-2.717 1.875-4.338 1.875-1.607 0-1.825-1.875-4.338-1.875-2.486 0-2.74 1.875-4.324 1.875-1.633 0-1.81-1.875-4.338-1.875-1.245 0-1.92.456-2.48.917v-3.73c0-.516.416-.937.928-.937h20.428c.512 0 .929.42.929.938v3.728z"
							fill="#222222" fill-rule="nonzero"></path>
					</svg>
					<?php _e( 'Age', 'ars-sheltered-animals' ) ?>: <?php echo $age; ?>
				</div>
				<div class="animal-additional-info">
					<svg viewBox="0 0 30 30" height="30px" width="30px" xmlns="http://www.w3.org/2000/svg">
						<path
							d="M28.754 14.815l-2.847-2.502V5.342a.78.78 0 00-.779-.782h-4.67a.78.78 0 00-.778.782v1.501l-4.158-3.65a.776.776 0 00-1.024 0L1.266 14.815a.784.784 0 00-.073 1.103.776.776 0 001.098.074l1.822-1.603v11.267A2.342 2.342 0 006.448 28h17.124a2.342 2.342 0 002.335-2.344V14.389l1.822 1.601a.776.776 0 001.078-.09.783.783 0 00-.053-1.085zM24.35 25.656a.78.78 0 01-.778.781H6.448a.78.78 0 01-.778-.781V13.022l9.34-8.204 9.34 8.204v12.634zm0-14.71L21.237 8.21V6.123h3.113v4.822zm-13.232 2.73v5.209c.003.718.582 1.3 1.297 1.302h5.188a1.304 1.304 0 001.299-1.302v-5.21a1.304 1.304 0 00-1.3-1.301h-5.187a1.303 1.303 0 00-1.297 1.302zm1.557.26h4.67v4.688h-4.67v-4.688z"
							stroke="#000" stroke-width="0.5" fill="#222222" fill-rule="nonzero"></path>
					</svg>
					<?php _e( 'Years in the shelter', 'ars-sheltered-animals' ) ?>: <?php echo $sheltered_for ?><br>
				</div>
				<div class="animal-additional-info">
					<svg viewBox="0 0 30 30" height="30px" width="30px" xmlns="http://www.w3.org/2000/svg">
						<path
							d="M15 13.156c0-3.882-3.135-7.031-7-7.031s-7 3.15-7 7.031c0 3.618 2.722 6.602 6.222 6.988v3.169H4.694a.586.586 0 00-.583.585v.391c0 .322.263.586.583.586h2.528v2.54c0 .321.263.585.584.585h.388c.321 0 .584-.264.584-.586v-2.539h2.528c.32 0 .583-.264.583-.586v-.39a.586.586 0 00-.583-.587H8.778v-3.168c3.5-.386 6.222-3.37 6.222-6.988zm-12.444 0c0-3.022 2.43-5.469 5.444-5.469 3.009 0 5.444 2.442 5.444 5.47 0 3.022-2.43 5.468-5.444 5.468-3.009 0-5.444-2.441-5.444-5.469zM29 3.586v4.297a.586.586 0 01-.583.586h-.39a.586.586 0 01-.583-.586V5.666l-3.086 3.1a7.032 7.032 0 011.53 4.39c0 3.882-3.135 7.031-7 7.031a6.978 6.978 0 01-4.335-1.508c.335-.4.632-.83.89-1.284a5.393 5.393 0 003.441 1.23c3.014 0 5.444-2.446 5.444-5.469 0-3.027-2.435-5.469-5.444-5.469a5.393 5.393 0 00-3.442 1.231 8.379 8.379 0 00-.89-1.284 6.978 6.978 0 018.707.03l3.087-3.101h-2.207a.586.586 0 01-.583-.586v-.391c0-.322.262-.586.583-.586h4.278c.32 0 .583.264.583.586z"
							stroke="#000" stroke-width="0.5" fill="#222222" fill-rule="nonzero"></path>
					</svg>
					<?php _e( 'Sex', 'ars-sheltered-animals' ) ?>: <?php echo $sex ?><br>
				</div>
			</div>
		</div>
		<div class="single-animal-content">
			<?php the_content(); ?>
		</div>
		<div class="how-it-works">
				<div class="SectionSteppedProcess-module--step--d8ba3">
					<div class="SectionSteppedProcess-module--stepordering--b7679">
						<div class="SectionSteppedProcess-module--number--7bef8">
							<div class="SectionSteppedProcess-module--numbertitle--5d1a0">1</div>
							<div class="SectionSteppedProcess-module--numberborder--dd04b"></div>
						</div>
					</div>
					<div class="SectionSteppedProcess-module--stepcontent--52101">
						<h5	class="">Pick a dog to sponsor</h5>
						<div class="">
							<p>Choose a dog who reminds you	of a beloved pet, or a dog with a story that tugs simply at your heartstrings. Or why not sponsor both?</p>
						</div>
				</div>
				<div class="SectionSteppedProcess-module--step--d8ba3">
					<div class="SectionSteppedProcess-module--stepordering--b7679">
						<div class="SectionSteppedProcess-module--number--7bef8">
							<div class="SectionSteppedProcess-module--numbertitle--5d1a0">2</div>
							<div class="SectionSteppedProcess-module--numberborder--dd04b"></div>
						</div>
					</div>
					<div class="SectionSteppedProcess-module--stepcontent--52101">
						<h5 class="">Set up your sponsorship</h5>
						<div class="">
							<p>Tell us how much you'd like to give and whether you want to give monthly or annually. For gift sponsorships we'll
								just need to know who to send doggy updates too.</p>
						</div>
					</div>
				</div>
				<div class="SectionSteppedProcess-module--step--d8ba3">
					<div class="SectionSteppedProcess-module--stepordering--b7679">
						<div class="SectionSteppedProcess-module--number--7bef8">
							<div class="SectionSteppedProcess-module--numbertitle--5d1a0">3</div>
						</div>
					</div>
					<div class="SectionSteppedProcess-module--stepcontentbottom--5f0d1">
						<h5 class="">Enjoy regular updates from your Sponsor Dog</h5>
						<div class="SectionSteppedProcess-module--stepintrotext--cbfda">
							<p>As well as the exclusive sponsorship welcome pack you, or your giftee, will receive updates from your chosen
								sponsor dog three times a year.</p>
						</div>
					</div>
				</div>
		</div>
	</div>

<?php
get_footer();
