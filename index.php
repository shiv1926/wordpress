<?php include('header.php'); ?>
<ul class="index">
	<li>
		<h4>Plugin Development</h4>
		<ul>
			<li><a href="<?php echo wp_url('plugin-development/introduction.php'); ?>">Introduction</a></li>
			<li><a href="<?php echo wp_url('plugin-development/what-is-a-plugin.php'); ?>">What is a plugin</a></li>
		</ul>
	</li>
	<li>
		<h4>Theme Development</h4>
		<ul>
			<li><a href="<?php echo wp_url('theme-development'); ?>">Introduction</a></li>
		</ul>
	</li>
	<li>
		<h4>USers</h4>
		<ul>
			<li><a href="<?php echo wp_url('https://developer.wordpress.org/reference/classes/wp_user_query'); ?>">User class</a></li>
		</ul>
	</li>
	<li>
		<h4>Wordpress Performance and optimization</h4>
		<ul>
			<li><a href="<?php echo wp_url('wordpress-performance/introduction.php'); ?>">Introduction</a></li>
			<li><a href="<?php echo wp_url('custom-crud-operations.php'); ?>">Custom Crud Operations</a></li>
			<li><a href="<?php echo wp_url('sanitizing.php'); ?>">Securing (sanitizing) Input</a></li>
			<li><a href="<?php echo wp_url('securing-output.php'); ?>">Securing Output</a></li>
		</ul>
	</li>
	<li>
		<h4>Wordpress Security</h4>
		<ul>
			<li><a href="<?php echo wp_url('wordpress-security/introduction.php'); ?>">Introduction</a></li>
			<li>https://onlinemediamasters.com/slow-wordpress-site/</li>
			<li>contact form 7 redirect https://www.rocketclicks.com/client-education/contact-form-7-thank-page-redirects/</li>
		</ul>
	</li>
	<li>
		<ul>
			<li>Jetpack provides everything you need to build a successful WordPress website including an image/photo CDN (free) and a video hosting service (paid).</li>
			<li>Yahoo! Yslow analyzes web pages and suggests ways to improve their performance based on a set of rules for high performance web pages. Also try the performance tools online at GTMetrix.</li>
			<li>Use Google Libraries allows you to load some commonly used Javascript libraries from Google webservers. Ironically, it may reduce your Yslow score.</li>
			<li>Advanced users only: Install an object cache. Choose from Memcached, XCache, eAcccelerator and others.</li>
			<li>WP Crontrol is a useful plugin to use when trying to debug garbage collection and preload problems.</li>
			<li>for speed : </li>
			<li>ManageWP - Worker</li>
			<li>W3Speedster</li>
			<li>Comet Cache</li>
			<li>https://w3speedup.com/how-to-improve-fcp-first-contentful-paint/</li>
		</ul>
	</li>
	<li>
		<h4>create table in admin interface</h4>
		<div>https://www.smashingmagazine.com/2011/11/native-admin-tables-wordpress/</div>
		<div>https://www.vijayan.in/how-to-create-custom-table-list-in-wordpress-admin-page/</div>
		<div>https://www.wpbeginner.com/wp-tutorials/how-to-add-indexnow-in-wordpress-to-speed-up-seo-results/ : this is indexNow new property, it is very important for SEO</div>
	</li>
</ul>

<?php include('footer.php'); ?>