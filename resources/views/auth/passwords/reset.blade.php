



@extends('layouts.auth')


@section('contents')
	<!-- begin:: Page -->
		<div class="kt-grid kt-grid--ver kt-grid--root kt-page auth-background">
			<div class="kt-grid kt-grid--hor kt-grid--root  kt-login kt-login--v6 kt-login--signin" id="kt_login">
				<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--desktop kt-grid--ver-desktop kt-grid--hor-tablet-and-mobile auth-background">
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
											<h3 class="kt-login__title">{{ __('Reset Password') }}</h3>
										</div>
										<div class="kt-login__form">
											<form method="POST" action="{{ route('password.update') }}">
												 @csrf
												  <input type="hidden" name="token" value="{{ $token }}">

												<div class="form-group validate is-invalid"" >
													<input class="form-control" type="text" placeholder="username" name="username" autocomplete="off" value="{{$username}}">
												@error('username')
												<div id="email-error" class="error invalid-feedback">{{ $message }}</div>

				                                @enderror
												</div>
												<div class="form-group validate is-invalid">
													<input class="form-control form-control-last" type="password" placeholder="Password" name="password">
													@error('password')
													<div id="password-error" class="error invalid-feedback">{{ $message }}</div>
				                               		 @enderror
												</div>

												<div class="form-group validate is-invalid">
													<input class="form-control form-control-last" type="password" placeholder="{{ __('Confirm Password') }}" name="password_confirmation" >

												</div>

												<div class="kt-login__actions">
													<button id="kt_login_signin_submit" class="btn btn-brand btn-pill btn-elevate">  {{ __('Reset Password') }}</button>
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
