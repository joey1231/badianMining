@extends('layouts.main')

@section('extra_css')
@endsection

@section('contents')
		<div class="row">
		<div class="col-md-12">
			<div class="kt-portlet">



				<div class="kt-portlet__body">
						<ul class="nav nav-tabs" id="myTab" role="tablist">
						  @if($isOwner)<li class="nav-item" role="presentation">
						    <button class="nav-link active" id="home-tab" data-toggle="tab" data-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Dashboard</button>
						  </li>
						  @endif
						   @if($isInvestor)
						  <li class="nav-item" role="presentation">
						    <button class="nav-link active" id="profile-tab" data-toggle="tab" data-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Investor Dashobard</button>
						  </li>
						  @endif
						   @if($isOwner)
						  <li class="nav-item" role="presentation">
						    <button class="nav-link" id="contact-tab" data-toggle="tab" data-target="#contact" type="button" role="tab" aria-controls="contact" aria-selected="false">Owner Dashboard</button>
						  </li>
						  @endif
						</ul>
					<div class="tab-content" id="myTabContent">
					  @if($isOwner) <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab"><v-dashboard></v-dashboard></div> @endif
					  @if($isInvestor) <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab"><v-dashboard-share></v-dashboard-share></div> @endif
					   @if($isOwner)<div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab"><v-dashboard-share-owner></v-dashboard-share-owner></div>@endif
					</div>
				</div>
			</div>
		</div>
	</div>

@endsection

@section('extra_js')
@endsection
