@extends('layouts.auth')


@section('contents')
	<!-- begin:: Page -->
		<div class="kt-grid kt-grid--ver kt-grid--root kt-page auth-background">
			<div class="kt-grid kt-grid--hor kt-grid--root  kt-login kt-login--v6 kt-login--signin" id="kt_login">
				<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--desktop kt-grid--ver-desktop kt-grid--hor-tablet-and-mobile auth-background" >
					<div class="kt-grid__item  kt-grid__item--order-tablet-and-mobile-2  kt-grid kt-grid--hor kt-login__aside auth-background" style="background: transparent;">
						<div class="kt-login__wrapper">
							<div class="kt-login__container">
								<div class="kt-login__body" style="
    background: #fff;
    padding: 0 20px;
">
									<div class="kt-login__logo" style="margin: 0px; padding: 40px 0 10px 0">
										<a href="#">
											<img src="/assets/img/rp-logo-auth.png" width="189">
										</a>
									</div>
									<div class="kt-login__signin">
										<div class="kt-login__head">
											<h3 class="kt-login__title">Sign in to Mining Sales Calculation</h3>
										</div>
										<div class="kt-login__form">
											@if(Session::has('status') && Session::get('status')=='failed')
									            <div class="row form-group">
									                <div class="col-sm-12" align="center">
									                    <br>

									                    <span class="label label-danger" style="color: red">{{ Session::get('message') }}</span>
									                </div>
									            </div>
									            @endif
											<form class="kt-form" method="POST" action="{{ route('login') }}">
												 @csrf
												<div class="form-group validate is-invalid"" >
													<input class="form-control" type="text" placeholder="Username" name="username" autocomplete="off">
												@error('username')
												<div id="email-error" class="error invalid-feedback">{{ $message }}</div>

				                                @enderror
												</div>
												<div class="form-group validate is-invalid"">
													<input class="form-control form-control-last" type="password" placeholder="Password" name="password">
													@error('password')
													<div id="password-error" class="error invalid-feedback">{{ $message }}</div>
				                               		 @enderror
												</div>
												<div class="kt-login__extra">
													<label class="kt-checkbox">
														<input type="checkbox" name="remember"> Remember me
														<span></span>
													</label>
													<a href="{{url('password/reset')}}" id="kt_login_forgot">Forget Password ?</a>
												</div>
												<div class="kt-login__actions">
													<button id="kt_login_signin_submit" class="btn btn-brand btn-pill btn-elevate">Sign In</button>
												</div>
											</form>
										</div>
									</div>
									<div class="kt-login__signup">
										<div class="kt-login__head">
											<h3 class="kt-login__title">Sign Up</h3>
											<div class="kt-login__desc">Enter your details to create your account:</div>
										</div>
										<div class="kt-login__form">
											<form class="kt-form" action="">
												<div class="form-group">
													<input class="form-control" type="text" placeholder="Fullname" name="fullname">
												</div>
												<div class="form-group">
													<input class="form-control" type="text" placeholder="Email" name="email" autocomplete="off">
												</div>
												<div class="form-group">
													<input class="form-control" type="password" placeholder="Password" name="password">
												</div>
												<div class="form-group">
													<input class="form-control form-control-last" type="password" placeholder="Confirm Password" name="rpassword">
												</div>
												<div class="kt-login__extra">
													<label class="kt-checkbox">
														<input type="checkbox" name="agree"> I Agree the <a href="#">terms and conditions</a>.
														<span></span>
													</label>
												</div>
												<div class="kt-login__actions">
													<button id="kt_login_signup_submit" class="btn btn-brand btn-pill btn-elevate">Sign Up</button>
													<button id="kt_login_signup_cancel" class="btn btn-outline-brand btn-pill">Cancel</button>
												</div>
											</form>
										</div>
									</div>
									<div class="kt-login__forgot">
										<div class="kt-login__head">
											<h3 class="kt-login__title">Forgotten Password ?</h3>
											<div class="kt-login__desc">Enter your email to reset your password:</div>
										</div>
										<div class="kt-login__form">
											<form class="kt-form" action="">
												<div class="form-group">
													<input class="form-control" type="text" placeholder="Email" name="email" id="kt_email" autocomplete="off">
												</div>
												<div class="kt-login__actions">
													<button id="kt_login_forgot_submit" class="btn btn-brand btn-pill btn-elevate">Request</button>
													<button id="kt_login_forgot_cancel" class="btn btn-outline-brand btn-pill">Cancel</button>
												</div>
											</form>
										</div>
									</div>
								</div>
							</div>

						</div>
					</div>
					<!-- <div class="kt-grid__item kt-grid__item--fluid kt-grid__item--center kt-grid kt-grid--ver kt-login__content" style="background-image: url(/assets/media/bg/bg-4.jpg);">
						<div class="kt-login__section">
							<div class="kt-login__block">
								<h3 class="kt-login__title">Join Our Community</h3>
								<div class="kt-login__desc">

								</div>
							</div>
						</div>
					</div> -->
				</div>
			</div>
		</div>

		<!-- end:: Page -->
@endsection
