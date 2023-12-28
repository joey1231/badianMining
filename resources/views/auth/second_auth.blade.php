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
     height: 519px;
">
									<div class="kt-login__logo" style="margin: 0px; padding: 40px 0 10px 0">
										<a href="#">
											<img src="/assets/img/rp-logo-auth.png" width="189">
										</a>
									</div>
									<div class="kt-login__signin">
										<div class="kt-login__head">
											<h3 class="kt-login__title">2FA CODE</h3>
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
									            <form id="form" action="2faProcess" method="post">
									            {{ csrf_field() }}
									            @if(Session::has('status') && Session::get('status')=='success')
									                     <p style="
									                    margin-top: 20px;
									                    font-weight: normal; text-align: justify;
									                ">{{ Session::get('message') }} </p>
									                 @else
									                      @if(Session::has('status') && Session::get('status')=='fail')
									                         <p style="
									                        margin-top: 20px;
									                        font-weight: normal; text-align: justify;
									                    ">{{ Session::get('message') }} </p>
									                      @else
									                      	   <label for="username"><p style="
									                              margin-top: 20px;
									                              font-weight: normal; text-align: justify;
									                          ">To complete the sign-in and access your RevenuePlus account, please provide the authentication code we sent to your registered email address.</p>

									                          <p style="
									                              margin-top: 20px;
									                              font-weight: normal;text-align: justify;
									                          ">The code is valid for only five (5) minutes. When the code expires, click the Resend Code button to send a new authentication code to your email.</p></label>
									                  @endif
													@endif
									            <div class="form-group validate is-invalid"" >
													<input class="form-control" type="text" id="code" name="code" value="{{ old('code') }}" placeholder="Enter Code here">

													<div id="email-error" class="error invalid-feedback">
													</div>


												</div>
									            <div class="row form-group">
									                <div align="center" style="text-align: left;    padding-left: 15px;">
									            	   <input class="btn btn-primary" type="submit" name="btnLogin" value="Submit Code" />
									                   <a href="{{url('resend_code')}}" class="btn btn-default">Resend Code</a>
									                </div>
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
