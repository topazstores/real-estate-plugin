<div class="wrap ich-settings-main-wrap">
	<div class="panel panel-default">
		<div class="panel-body">
			<div class="col-sm-2">
				<img src="<?php echo REM_URL.'/assets/images/rem-icon.png'; ?>" alt="REM" class="img-responsive">
			</div>
			<div class="col-sm-10">
				<h1>Real Estate Manager <span class="badge bg-primary">v<?php echo REM_VERSION; ?></span></h1>
				<p class="text-info lead">
					Thank you for your purchase of <b>Real Estate Manager</b>.
					We would love to hear your positive feedback <a target="_blank" href="https://codecanyon.net/downloads"><strong>here</strong></a> or if you have any issues beyond the scope of our documentation, just open a ticket <a target="_blank" href="https://wp-rem.com/docs/general/how-to-get-support/"><strong>following this URL</strong></a>.
				</p>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Activation</h3>
				</div>
				<div class="panel-body">
					<?php if(is_rem_validated()){ ?>
							<div class="alert alert-success text-center">
								Your site <b><?php echo get_site_url(); ?></b> is successfully registered.
							</div>
						<div class="input-group">
							<input type="text" class="form-control" disabled value="<?php
								$code = get_option('rem_validated', true);
								echo substr_replace($code, str_repeat('*', strlen($code)-6), 6, -6);
							 ?>">
							<span class="input-group-btn">
								<button class="btn btn-danger rem-deactivate" type="button">De-Activate</button>
							</span>
						</div>
					<?php } else { ?>
						<form action="#" class="rem-activate-license">
							<input type="hidden" name="action" value="rem_validate_pcode">
							<input type="hidden" name="rem_nonce_validate" value="<?php echo wp_create_nonce('rem-nonce-validate'); ?>">
							<label for="">Provide your purchase code</label>
							<div class="input-group">
								<input type="text" class="form-control" name="code" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx" required>
								<span class="input-group-btn">
									<button class="btn btn-success" type="submit">Activate</button>
								</span>
							</div>
							<p class="help-block">Unable to find your purchase code? <a target="_blank" href="https://wp-rem.com/docs/installation/where-is-my-purchase-code/">Please follow this</a>.</p>
						</form>
					<?php } ?>
				</div>
			</div>
		</div>
		<div class="col-sm-3">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Create Essential Pages</h3>
				</div>
				<div class="panel-body">
					<p>
						Click on the following button if you want to create these pages automatically.
					</p>
					<ol>
						<li>All Listings Page</li>
						<li>Search Properties Page</li>
						<li>Create Property Page</li>
						<li>Agent Registration Page</li>
						<li>Agent Login Page</li>
						<li>Edit Profile Page</li>
						<li>Edit Property Page</li>
						<li>My Properties Page</li>
					</ol>
					<?php if(get_option( 'rem_basic_pages_created' )){ ?>
						<div class="alert alert-warning">You have already created these pages. It may add duplicate entries.</div>
					<?php } ?>
					<hr>
					<a href="#" class="btn btn-info rem-create-pages"> <span class="glyphicon glyphicon-refresh"></span> Create Pages</a>
				</div>
			</div>
		</div>
		<div class="col-sm-3">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Getting Started</h3>
				</div>
				<div class="panel-body">
					<div class="list-group">
						<a href="<?php echo admin_url('edit.php?post_type=rem_property&page=rem_settings'); ?>" class="list-group-item">
							<span class="glyphicon glyphicon-cog"></span> 
							Basic Settings
						</a>

						<a href="https://wp-rem.com/online-documentation/category/shortcodes/" target="_blank" class="list-group-item">
							<span class="glyphicon glyphicon-console"></span> 
							Shortcodes
						</a>

						<a href="https://wp-rem.com/online-documentation/" target="_blank" class="list-group-item">
							<span class="glyphicon glyphicon-book"></span> 
							Documentation
						</a>

						<a href="https://wp-rem.com/online-documentation/category/faqs/" target="_blank" class="list-group-item">
							<span class="glyphicon glyphicon-question-sign"></span> 
							FAQs
						</a>

						<a href="https://www.youtube.com/playlist?list=PLAyqGZN06NDryROa1PRrooHxpjOT1MRJV" target="_blank" class="list-group-item">
							<span class="glyphicon glyphicon-facetime-video"></span> 
							Video Tutorials
						</a>

						<a href="https://wp-rem.com/changelog/" target="_blank" class="list-group-item">
							<span class="glyphicon glyphicon-flag"></span> 
							Changelog
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>